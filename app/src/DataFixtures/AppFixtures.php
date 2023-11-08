<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
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
