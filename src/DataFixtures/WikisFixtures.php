<?php

namespace App\DataFixtures;

use App\Entity\Wiki;
use App\Entity\Scenari;
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
            
            // Create scenari for each Wiki
            for ($j = 0; $j < 3; $j++) {
                $scenari = new Scenari();
                $scenari->setWiki($wiki);
                $scenari->setTitle('Scenari-'. $j);
                $scenari->setNarrativeTram('Narrative tram of Scenari-'. $j);
                $manager->persist($scenari);
            }
            $manager->persist($wiki);
        }
        $manager->flush();
    }
}
