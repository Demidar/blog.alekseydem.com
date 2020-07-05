<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadArticles($manager);

        $manager->flush();
    }

    private function loadArticles(ObjectManager $manager): void
    {
        $article = new Article();
        $article->setTitle('some article');
        $article->setText('Text for article');
        $article->setStatus('published');

        $manager->persist($article);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $user = new User();

        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
        $user->setRoles(['ROLE_ADMIN']);
        $user->setStatus('active');

        $manager->persist($user);
    }
}
