<?php

namespace App\DataFixtures;

use App\Entity\Wiki;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WikisFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 20; $i++) {
            $wiki = new Wiki();
            $wiki->setName('wiki-'. $i);
            $wiki->setDescription('Description of wiki-'. $i);
            $wiki->setCreatedAt(new \DateTimeImmutable());
            $wiki->setStatus('active');
            $manager->persist($wiki);
         }
        $manager->flush();
    }
}
