export function respondToComment() {
    let formNote = document.querySelector('.comments__form-container .form-container__note');
    document.querySelectorAll('.js_comment-reply-link').forEach(link => {
        link.addEventListener('click', event => {
            let commentId = parseInt(event.target.dataset['comment']);
            let commentAuthor = event.target.dataset['author'];
            document.querySelector('.js_comment-reply_form-parent').value = commentId;
            document.querySelector('.js_comment-form_reply-to').innerHTML = 'Reply to: '+commentAuthor;
            formNote.classList.remove('form-container__note_hidden');
        });
    });
    document.querySelector('.js-comment-reply_remove').addEventListener('click', event => {
        event.preventDefault();
        document.querySelector('.js_comment-reply_form-parent').value = '';
        document.querySelector('.js_comment-form_reply-to').innerHTML = '';
        formNote.classList.add('form-container__note_hidden');
    });
}
