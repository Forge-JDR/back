<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Wiki;
use App\Entity\Job;
use App\Entity\Bestiary;
use App\Entity\Race;
use App\Entity\Scenario;

class UserFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $plaintextPassword = 'admin';
        $user = new user();
        $user->setUsername('admin@gmail.com');
        $user->setPseudo('admin');
        
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);      
        
        // Create Wikis in developpement
        $wiki = new Wiki();
        $wiki->setName('wiki en développement');
        $wiki->setContent('Description of wiki en dev');

        // Create Jobs for each Wiki
        for ($j = 0; $j < 3; $j++) {
            $job = new Job();
            $job->setName('Name of job'.$j);
            $job->setContent('Description of job-'.$j);
            $job->setWiki($wiki);
            $manager->persist($job);
        }

        // Create Races for each Wiki
        for ($r = 0; $r < 3; $r++) {
            $race = new Race();
            $race->setName('Name of race-'. $r);
            $race->setContent('Description of race-'.  $r);
            $race->setWiki($wiki);
            $manager->persist($race);
        }

        // Create Bestiaries for each Wiki
        for ($b = 0; $b < 3; $b++) {
            $bestiary = new Bestiary();
            $bestiary->setName('Name of bestary-'.  $b);
            $bestiary->setContent('Description of bestary-'. $b);
            $bestiary->setWiki($wiki);
            $bestiary->setType('Type of bestary-'.  $b);
            $manager->persist($bestiary);
        }

        // Create Scenari for each Wiki
        for ($s = 0; $s < 3; $s++) {
            $scenari = new Scenario();
            $scenari->setName('Title of scenari-'.  $b);
            $scenari->setContent('Narrative tram of scenari-'. $b);
            $scenari->setWiki($wiki);
            $manager->persist($scenari);
        }

        $manager->persist($wiki);
        $user->addWiki($wiki);

        // Create Wikis published
        $wiki = new Wiki();
        $wiki->setName('wiki en développement');
        $wiki->setContent('Description of wiki en dev');
        $wiki->setStatus("published");

        // Create Jobs for each Wiki
        for ($j = 0; $j < 3; $j++) {
            $job = new Job();
            $job->setName('Name of job'.$j);
            $job->setContent('Description of job-'.$j);
            $job->setWiki($wiki);
            $manager->persist($job);
        }

        // Create Races for each Wiki
        for ($r = 0; $r < 3; $r++) {
            $race = new Race();
            $race->setName('Name of race-'. $r);
            $race->setContent('Description of race-'.  $r);
            $race->setWiki($wiki);
            $manager->persist($race);
        }

        // Create Bestiaries for each Wiki
        for ($b = 0; $b < 3; $b++) {
            $bestiary = new Bestiary();
            $bestiary->setName('Name of bestary-'.  $b);
            $bestiary->setContent('Description of bestary-'. $b);
            $bestiary->setWiki($wiki);
            $bestiary->setType('Type of bestary-'.  $b);
            $manager->persist($bestiary);
        }

        // Create Scenari for each Wiki
        for ($s = 0; $s < 3; $s++) {
            $scenari = new Scenario();
            $scenari->setName('Title of scenari-'.  $b);
            $scenari->setContent('Narrative tram of scenari-'. $b);
            $scenari->setWiki($wiki);
            $manager->persist($scenari);
        }

        $manager->persist($wiki);
        $user->addWiki($wiki);
        //$user->addWiki($this->addWikiPublished($manager));

        $manager->persist($user);





        // User
        $plaintextPassword = 'password';
        $user = new user();
        $user->setUsername('user@gmail.com');
        $user->setPseudo('user');
        
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);        
        // Create Wikis in developpement
        $wiki = new Wiki();
        $wiki->setName('wiki en développement');
        $wiki->setContent('Description of wiki en dev');

        // Create Jobs for each Wiki
        for ($j = 0; $j < 3; $j++) {
            $job = new Job();
            $job->setName('Name of job'.$j);
            $job->setContent('Description of job-'.$j);
            $job->setWiki($wiki);
            $manager->persist($job);
        }

        // Create Races for each Wiki
        for ($r = 0; $r < 3; $r++) {
            $race = new Race();
            $race->setName('Name of race-'. $r);
            $race->setContent('Description of race-'.  $r);
            $race->setWiki($wiki);
            $manager->persist($race);
        }

        // Create Bestiaries for each Wiki
        for ($b = 0; $b < 3; $b++) {
            $bestiary = new Bestiary();
            $bestiary->setName('Name of bestary-'.  $b);
            $bestiary->setContent('Description of bestary-'. $b);
            $bestiary->setWiki($wiki);
            $bestiary->setType('Type of bestary-'.  $b);
            $manager->persist($bestiary);
        }

        // Create Scenari for each Wiki
        for ($s = 0; $s < 3; $s++) {
            $scenari = new Scenario();
            $scenari->setName('Title of scenari-'.  $b);
            $scenari->setContent('Narrative tram of scenari-'. $b);
            $scenari->setWiki($wiki);
            $manager->persist($scenari);
        }
        $manager->persist($wiki);
        $user->addWiki($wiki);

        // Create Wikis published
        $wiki = new Wiki();
        $wiki->setName('wiki en développement');
        $wiki->setContent('Description of wiki en dev');
        $wiki->setStatus("published");

        // Create Jobs for each Wiki
        for ($j = 0; $j < 3; $j++) {
            $job = new Job();
            $job->setName('Name of job'.$j);
            $job->setContent('Description of job-'.$j);
            $job->setWiki($wiki);
            $manager->persist($job);
        }

        // Create Races for each Wiki
        for ($r = 0; $r < 3; $r++) {
            $race = new Race();
            $race->setName('Name of race-'. $r);
            $race->setContent('Description of race-'.  $r);
            $race->setWiki($wiki);
            $manager->persist($race);
        }

        // Create Bestiaries for each Wiki
        for ($b = 0; $b < 3; $b++) {
            $bestiary = new Bestiary();
            $bestiary->setName('Name of bestary-'.  $b);
            $bestiary->setContent('Description of bestary-'. $b);
            $bestiary->setWiki($wiki);
            $bestiary->setType('Type of bestary-'.  $b);
            $manager->persist($bestiary);
        }

        // Create Scenari for each Wiki
        for ($s = 0; $s < 3; $s++) {
            $scenari = new Scenario();
            $scenari->setName('Title of scenari-'.  $b);
            $scenari->setContent('Narrative tram of scenari-'. $b);
            $scenari->setWiki($wiki);
            $manager->persist($scenari);
        }
        $manager->persist($wiki);
        $user->addWiki($wiki);

        $manager->persist($user);




        // User Michel
        $plaintextPassword = 'password';
        $user = new user();
        $user->setUsername('michel@gmail.com');
        $user->setPseudo('michel');
        
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);        
        // Create Wikis in developpement
        $wiki = new Wiki();
        $wiki->setName('wiki en développement');
        $wiki->setContent('Description of wiki en dev');

        // Create Jobs for each Wiki
        for ($j = 0; $j < 3; $j++) {
            $job = new Job();
            $job->setName('Name of job'.$j);
            $job->setContent('Description of job-'.$j);
            $job->setWiki($wiki);
            $manager->persist($job);
        }

        // Create Races for each Wiki
        for ($r = 0; $r < 3; $r++) {
            $race = new Race();
            $race->setName('Name of race-'. $r);
            $race->setContent('Description of race-'.  $r);
            $race->setWiki($wiki);
            $manager->persist($race);
        }

        // Create Bestiaries for each Wiki
        for ($b = 0; $b < 3; $b++) {
            $bestiary = new Bestiary();
            $bestiary->setName('Name of bestary-'.  $b);
            $bestiary->setContent('Description of bestary-'. $b);
            $bestiary->setWiki($wiki);
            $bestiary->setType('Type of bestary-'.  $b);
            $manager->persist($bestiary);
        }

        // Create Scenari for each Wiki
        for ($s = 0; $s < 3; $s++) {
            $scenari = new Scenario();
            $scenari->setName('Title of scenari-'.  $b);
            $scenari->setContent('Narrative tram of scenari-'. $b);
            $scenari->setWiki($wiki);
            $manager->persist($scenari);
        }
        $manager->persist($wiki);
        $user->addWiki($wiki);

        // Create Wikis published
        $wiki = new Wiki();
        $wiki->setName('wiki en développement');
        $wiki->setContent('Description of wiki en dev');
        $wiki->setStatus("published");

        // Create Jobs for each Wiki
        for ($j = 0; $j < 3; $j++) {
            $job = new Job();
            $job->setName('Name of job'.$j);
            $job->setContent('Description of job-'.$j);
            $job->setWiki($wiki);
            $manager->persist($job);
        }

        // Create Races for each Wiki
        for ($r = 0; $r < 3; $r++) {
            $race = new Race();
            $race->setName('Name of race-'. $r);
            $race->setContent('Description of race-'.  $r);
            $race->setWiki($wiki);
            $manager->persist($race);
        }

        // Create Bestiaries for each Wiki
        for ($b = 0; $b < 3; $b++) {
            $bestiary = new Bestiary();
            $bestiary->setName('Name of bestary-'.  $b);
            $bestiary->setContent('Description of bestary-'. $b);
            $bestiary->setWiki($wiki);
            $bestiary->setType('Type of bestary-'.  $b);
            $manager->persist($bestiary);
        }
        // Create Scenari for each Wiki
        for ($s = 0; $s < 3; $s++) {
            $scenari = new Scenario();
            $scenari->setName('Title of scenari-'.  $b);
            $scenari->setContent('Narrative tram of scenari-'. $b);
            $scenari->setWiki($wiki);
            $manager->persist($scenari);
        }
        $manager->persist($wiki);
        $user->addWiki($wiki);

        $manager->persist($user);

        $manager->flush();
    }

    
}
