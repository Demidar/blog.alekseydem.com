<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Section;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\CustomFixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends CustomFixture
{
    private $passwordEncoder;
    private $faker;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadSections($manager);
        $this->loadTags($manager);
        $this->loadArticles($manager);
        $this->addTagsToArticles($manager);
        $this->loadComments($manager);

        $manager->flush();
    }

    private function loadComments(ObjectManager $manager)
    {
        $users = $this->getReferencesByPrefix(User::class);
        $articles = $this->getReferencesByPrefix(Article::class);

        for ($i = 0; $i < 180; $i++) {
            $comments = $this->getReferencesByPrefix(Comment::class);
            /** @var Article $article */
            $article = $this->faker->randomElement($articles);

            $comment = new Comment();
            $comment->setOwner($this->faker->randomElement($users));
            $comment->setArticle($article);
            $comment->setText($this->faker->text);
            $comment->setParent($this->faker->optional(0.5)->randomElement($comments));
            $comment->setStatus($this->faker->randomElement(['visible', 'visible', 'invisible']));
            $comment->setCreatedAt($this->faker->dateTimeBetween($article->getCreatedAt(), 'now'));
            $comment->setUpdatedAt($this->faker->randomElement([$comment->getCreatedAt(), $this->faker->dateTimeBetween($comment->getCreatedAt(), 'now')]));

            $this->addReference(Comment::class.':'.$i, $comment);

            $manager->persist($comment);
        }
        $this->faker->unique(true); // reset unique modifier
    }

    private function addTagsToArticles(ObjectManager $manager)
    {
        $articles = $this->getReferencesByPrefix(Article::class);
        $tags = $this->getReferencesByPrefix(Tag::class);

        foreach ($articles as &$article) {
            /** @var Article $article */
            $tagsAmount = random_int(0, 8);
            for ($i = 0; $i < $tagsAmount; $i++) {
                $article->addTag($this->faker->unique()->randomElement($tags));
            }

            $manager->persist($article);

            $this->faker->unique(true); // reset unique modifier
        }
    }

    private function loadTags(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $tag->setTag($this->faker->word);

            $this->addReference(Tag::class.':'.$i, $tag);

            $manager->persist($tag);
        }
        $this->faker->unique(true); // reset unique modifier
    }

    private function loadSections(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++) {
            $sections = $this->getReferencesByPrefix(Section::class);

            $section = new Section();
            $section->setTitle($this->faker->sentence(3));
            $section->setSlug($this->faker->unique()->slug);
            $section->setStatus($this->faker->randomElement(['visible', 'invisible']));
            $section->setText($this->faker->optional(0.3)->text);
            $section->setLanguage($this->faker->randomElement(['en', 'en', 'ru', 'de']));
            $section->setParent($this->faker->optional(0.8)->randomElement($sections));

            $this->addReference(Section::class.':'.$i, $section);

            $manager->persist($section);
        }
        $this->faker->unique(true); // reset unique modifier
    }

    private function loadArticles(ObjectManager $manager): void
    {
        $users = $this->getReferencesByPrefix(User::class);
        $sections = $this->getReferencesByPrefix(Section::class);

        for ($i = 0; $i < 120; $i++) {
            $article = new Article();
            $article->setSection($this->faker->randomElement($sections));
            $article->setTitle($this->faker->sentence(5));
            $article->setText($this->faker->realText(700));
            $article->setStatus($this->faker->randomElement(['published', 'published', 'hidden', 'rejected', 'inReview', 'draft']));
            $article->setSlug($this->faker->unique()->slug);
            $article->setOwner($this->faker->randomElement($users));
            $article->setCreatedAt($this->faker->dateTimeBetween('-500 days', 'now'));
            $article->setUpdatedAt($this->faker->randomElement([$article->getCreatedAt(), $this->faker->dateTimeBetween($article->getCreatedAt(), 'now')]));

            $this->addReference(Article::class.':'.$i, $article);

            $manager->persist($article);
        }
        $this->faker->unique(true); // reset unique modifier
    }

    private function loadUsers(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $user = new User();

            $user->setUsername($this->faker->unique()->userName);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'user'));
            $user->setStatus('active');
            $user->setEmail($this->faker->optional(0.5)->email);
            $user->setCreatedAt($this->faker->dateTimeBetween('-500 days', 'now'));

            $this->addReference(User::class.':'.$i, $user);

            $manager->persist($user);
        }

        $user = new User();

        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setStatus('active');

        $this->addReference(User::class.':admin', $user);

        $manager->persist($user);

        $this->faker->unique(true); // reset unique modifier
    }
}
