<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * AppFixtures constructor.
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('test_user1@mail.com');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user1, '1234567890');
        $user1->setPassword($hashedPassword);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('test_user2@mail.com');
        $hashedPassword = $this->userPasswordHasher->hashPassword($user2, '1234567890');
        $user2->setPassword($hashedPassword);
        $manager->persist($user2);

        $microPost1 = new MicroPost();
        $microPost1->setTitle('Test title 1');
        $microPost1->setText('Test text 1');
        $microPost1->setCreated(new \DateTime());
        $manager->persist($microPost1);

        $microPost2 = new MicroPost();
        $microPost2->setTitle('Test title 2');
        $microPost2->setText('Test text 2');
        $microPost2->setCreated(new \DateTime());
        $manager->persist($microPost2);

        $microPost3 = new MicroPost();
        $microPost3->setTitle('Test title 3');
        $microPost3->setText('Test text 3');
        $microPost3->setCreated(new \DateTime());
        $manager->persist($microPost3);

        $manager->flush();
    }
}
