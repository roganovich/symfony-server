<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\Conference;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }
    
    public function load(ObjectManager $manager): void
    {
        $amsterdam = new Conference();
        $amsterdam->setCity('Amsterdam');
        $amsterdam->setYear('2019');
        $amsterdam->setIsInternational(true);
        $manager->persist($amsterdam);

        $paris = new Conference();
        $paris->setCity('Paris');
        $paris->setYear('2020');
        $paris->setIsInternational(false);
        $manager->persist($paris);

        $comment1 = new Comment();
        $comment1->setConference($amsterdam);
        $comment1->setAuthor('Fabien');
        $comment1->setEmail('fabien@example.com');
        $comment1->setText('This was a great conference.');
        $comment1->setState(Comment::STATE_PUBLISHED);
        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setConference($paris);
        $comment2->setAuthor('Roman');
        $comment2->setEmail('roman@example.com');
        $comment2->setText('This was a great conference.');
        $comment1->setState(Comment::STATE_PUBLISHED);
        $manager->persist($comment2);

        $admin = new Admin();
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEmail('admin@test.te');
        $admin->setPassword($this->passwordHasherFactory->getPasswordHasher(Admin::class)->hash('123456'));
        $manager->persist($admin);

        $manager->flush();
    }
}