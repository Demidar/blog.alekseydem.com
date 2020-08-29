<?php

namespace App\Admin\Form;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    private $commentRepository;
    private $userRepository;

    public function __construct(
        CommentRepository $commentRepository,
        UserRepository $userRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Comment $comment */
        $comment = $options['data'];
        /** @var Article $article */
        $article = $comment->getArticle();

        $builder
            ->add('article', HeavyArticleSelectorType::class, [
                'data' => $article
            ])
            ->add('transition', WorkflowType::class, [
                'subject' => $comment,
                'current_place' => $comment->getStatus(),
            ])
            ->add('text', TextareaType::class, [
                'required' => false,
                'label' => 'form.text.comment'
            ])
            ->add('parent', EntityType::class, [
                'required' => false,
                'label' => 'form.parent.comment',
                'class' => Comment::class,
                // comments related to the current article
                'choices' => $this->commentRepository->findCommentsByArticle($article->getId()),
                'choice_value' => static function (?Comment $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'choice_label' => static function (?Comment $entity) {
                    if (!$entity) {
                        return '';
                    }
                    $owner = $entity->getOwner() ? $entity->getOwner()->getUsername() : '';
                    $text = strlen($entity->getText()) > 40 ? substr($entity->getText(), 0, 37).'...' : $entity->getText();
                    return $owner . ': ' .$text;
                }
            ])
            ->add('owner', EntityType::class, [
                'required' => false,
                'label' => 'form.owner.comment',
                'class' => User::class,
                'choices' => $this->userRepository->findAll(),
                'choice_value' => static function (?User $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'choice_label' => static function (?User $entity) {
                    return $entity ? $entity->getUsername() : '';
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.submit'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
