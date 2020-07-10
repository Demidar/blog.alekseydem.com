export function navButton() {
    navButtonFn();
    window.addEventListener('resize', navButtonFn);

    function navButtonFn() {
        let panelWidth = document.querySelector('.nav__panel').clientWidth;
        let navButton = document.querySelector('.nav__button');
        let navItems = document.querySelectorAll('.nav__item');

        let panelItemsWidth = 0;

        navItems.forEach((item) => {
            panelItemsWidth += item.clientWidth;
        });
        if (panelItemsWidth > panelWidth) {
            navButton.classList.add('nav__button_show');
        } else {
            navButton.classList.remove('nav__button_show');
        }
    }
}

export function navExpander() {
    let nav = document.querySelector('.nav');
    let navButton = document.querySelector('.nav__button');
    let navButtonIcon = document.querySelector('.nav__button-icon');

    let initialHeight = nav.clientHeight;

    navButton.addEventListener('click', () => {
        let isFixedHeight = nav.clientHeight === initialHeight;
        if (isFixedHeight) {
            nav.classList.add('nav_unfix-height');
            navButtonIcon.classList.remove('fa-caret-square-down');
            navButtonIcon.classList.add('fa-caret-square-up');
        } else {
            nav.classList.remove('nav_unfix-height');
            navButtonIcon.classList.remove('fa-caret-square-up');
            navButtonIcon.classList.add('fa-caret-square-down');
        }
    });
}
