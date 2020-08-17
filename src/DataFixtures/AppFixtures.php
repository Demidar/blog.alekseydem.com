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
        $manager->flush();
        $this->translateSections($manager);
        $this->loadTags($manager);
        $this->loadArticles($manager);
        $manager->flush();
        $this->translateArticles($manager);
        $this->addTagsToArticles($manager);
        $manager->flush();
        // flush comments separately, otherwise
        // an error "Integrity constraint violation: Column 'ancestor' cannot be null"
        // will be arised, idk why
        $this->loadComments($manager);
        $manager->flush();
    }

    private function loadComments(ObjectManager $manager)
    {
        $users = $this->getReferencesByPrefix(User::class);
        $articles = $this->getReferencesByPrefix(Article::class);

        for ($i = 0; $i < 600; $i++) {
            $comments = $this->getReferencesByPrefix(Comment::class);
            /** @var Article $article */
            $article = $this->faker->randomElement($articles);

            $comment = new Comment();
            $comment->setOwner($this->faker->randomElement($users));
            $comment->setArticle($article);
            $comment->setText($this->faker->text);
            $comment->setParent($this->faker->optional(0.5)->randomElement($comments));
            $comment->setStatus($this->faker->randomElement(['visible', 'visible', 'visible', 'invisible']));
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
        for ($i = 0; $i < 30; $i++) {
            $tag = new Tag();
            $tag->setTag($this->faker->word);

            $this->addReference(Tag::class.':'.$i, $tag);

            $manager->persist($tag);
        }
        $this->faker->unique(true); // reset unique modifier
    }

    private function loadSections(ObjectManager $manager): void
    {
        for ($i = 0; $i < 60; $i++) {
            $sections = $this->getReferencesByPrefix(Section::class);

            $section = new Section();
            $section->setTitle($this->faker->sentence(3));
            // $section->setSlug($this->faker->unique()->slug);
            $section->setStatus($this->faker->randomElement(['visible', 'visible', 'visible', 'invisible']));
            $section->setText($this->faker->optional(0.3)->text);
            $section->setTranslatableLocale('en');
            $section->setParent($this->faker->optional(0.9)->randomElement($sections));

            $this->addReference(Section::class.':'.$i, $section);

            $manager->persist($section);
        }
        $this->faker->unique(true); // reset unique modifier
    }

    private function translateSections(ObjectManager $manager): void
    {
        $this->translateSectionsToRussian($manager);
        $manager->flush();
    }

    private function translateSectionsToRussian(ObjectManager $manager): void
    {
        /** @var Section[] $sections */
        $sections = $this->getReferencesByPrefix(Section::class);
        $faker = Factory::create('ru_RU');
        foreach ($sections as $section) {
            $section = $faker->unique()->randomElement($sections);

            $section->setTranslatableLocale('ru');
            $section->setTitle($faker->realText(15));
            $section->setText($faker->optional(0.3)->realText(800));

            $manager->persist($section);
        }
    }

    private function loadArticles(ObjectManager $manager): void
    {
        $users = $this->getReferencesByPrefix(User::class);
        $sections = $this->getReferencesByPrefix(Section::class);

        for ($i = 0; $i < 200; $i++) {
            $text = '';
            $paragraphCount = random_int(2, 12);
            for ($paragraphNum = 0; $paragraphNum < $paragraphCount; $paragraphNum++) {
                $text .= '<p>';
                $text .= $this->faker->paragraph;
                $text .= '</p>';
            }

            $article = new Article();
            $article->setSection($this->faker->randomElement($sections));
            $article->setTranslatableLocale('en');
            $article->setTitle($this->faker->sentence(5));
            $article->setText($text);
            $article->setStatus($this->faker->randomElement(['published', 'published', 'published', 'hidden', 'rejected', 'inReview', 'draft']));
            // $article->setSlug($this->faker->unique()->slug);
            $article->setOwner($this->faker->randomElement($users));
            $article->setCreatedAt($this->faker->dateTimeBetween('-500 days', 'now'));
            $article->setUpdatedAt($this->faker->randomElement([$article->getCreatedAt(), $this->faker->dateTimeBetween($article->getCreatedAt(), 'now')]));

            $this->addReference(Article::class.':'.$i, $article);

            $manager->persist($article);
        }
        $this->faker->unique(true); // reset unique modifier
    }

    private function translateArticles(ObjectManager $manager): void
    {
        $this->translateArticlesToRussian($manager);
        $manager->flush();
    }

    private function translateArticlesToRussian(ObjectManager $manager): void
    {
        /** @var Article[] $articles */
        $articles = $this->getReferencesByPrefix(Article::class);
        $faker = Factory::create('ru_RU');

        foreach ($articles as $article) {
            $article->setTranslatableLocale('ru');
            $article->setTitle($faker->realText(30));
            $article->setText($faker->realText(2000));

            $manager->persist($article);
        }
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
