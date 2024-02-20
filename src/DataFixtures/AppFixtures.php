<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {

            $object = (new Card())
                ->setQuestion('question' . $i)
                ->setAnswer('answer' . $i)
                ->setTag('tag' . $i)
                ->setCategory("FIRST");


            $manager->persist($object);

        }


        $manager->flush();
    }



}