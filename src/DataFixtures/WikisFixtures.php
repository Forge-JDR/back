<?php

namespace App\DataFixtures;

use App\Entity\Bestiary;
use App\Entity\Job;
use App\Entity\Wiki;
use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WikisFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // Create Wikis published
        for ($i = 0; $i < 10; $i++) {
            $wiki = new Wiki();
            $wiki->setName('wiki-'. $i);
            $wiki->setContent('Description of wiki-'. $i);
            $wiki->setCreatedAt(new \DateTimeImmutable());
            $wiki->setStatus('published');

            // Create Jobs for each Wiki
            for ($j = 0; $j < 3; $j++) {
                $job = new Job();
                $job->setName('Name of job-'. $i . $j);
                $job->setDescription('Description of job-'. $i . $j);
                $job->setWiki($wiki);
                $manager->persist($job);
            }

            // Create Races for each Wiki
            for ($r = 0; $r < 3; $r++) {
                $race = new Race();
                $race->setName('Name of race-'. $i . $r);
                $race->setDescription('Description of race-'. $i . $r);
                $race->setWiki($wiki);
                $manager->persist($race);
            }

            // Create Bestiaries for each Wiki
            for ($b = 0; $b < 3; $b++) {
                $bestiary = new Bestiary();
                $bestiary->setName('Name of bestary-'. $i . $b);
                $bestiary->setDescription('Description of bestary-'. $i . $b);
                $bestiary->setWiki($wiki);
                $bestiary->setType('Type of bestary-'. $i . $b);
                $manager->persist($bestiary);
            }

            $manager->persist($wiki);
         }
        $manager->flush();
    }
}
