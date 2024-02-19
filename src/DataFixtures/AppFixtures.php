<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $categories = [
            'FIRST',
            'SECOND',
            'THIRD',
            'FOURTH',
            'FIFTH',
            'SIXTH',
            'SEVENTH',
            'DONE',
        ];

        foreach ($categories as $categoryName) {
            $category = new Category();
            $category->setLabel($categoryName);
            if($categoryName === 'FIRST') {
                $firstCategory = $category;
            }
            $manager->persist($category);
        }


        for ($i = 0; $i < 20; $i++) {
            $object = (new User())
                ->setUuid('uuid' . $i)
                ->setRoles(['ROLE_USER'])
                ->setPassword('password' . $i);
            $manager->persist($object);

            $object = (new Card())
                ->setQuestion('question' . $i)
                ->setAnswer('answer' . $i)
                ->setTag('tag' . $i)
                ->setCategory($firstCategory ?? null)
                ->setLastReviewedAt(new \DateTime())
                ->setUser($object);

            $manager->persist($object);

        }


        $manager->flush();
    }



}