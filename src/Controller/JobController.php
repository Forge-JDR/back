<?php  

namespace App\Controller;

use App\Repository\UserRepository;


use App\Entity\Wiki;
use App\Entity\Job;
use App\Form\RegistrationFormType;
use App\Repository\JobRepository;
use App\Repository\WikiRepository;
use App\Security\Voter\WikiElementVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class JobController extends AbstractController
{

    #[Route('/api/wikis/{wiki}/jobs', name: 'add.job', methods: ['POST'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'job')]
    public function addJob(Job $job, Wiki $wiki, Request $request, JobRepository $jobRepository, WikiRepository $wikiRepository,SerializerInterface $serializer): Response
    {
        $job = $serializer->deserialize($request->getContent(), Job::class, 'json');
        $job->setWiki($wiki);
        $jobRepository->addJob($job, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/jobs/{job}', name: 'delete.job', methods: ['DELETE'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'job')]
    public function deleteJob(Job $job, Wiki $wiki, JobRepository $jobRepository, WikiRepository $wikiRepository): Response
    {
        $jobRepository->removeJob($job, $wiki);
        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/jobs/{job}', name: 'update.job', methods: ['PUT'])]
    #[IsGranted(WikiElementVoter::EDIT, subject: 'job')]
    public function updateJob(Job $job, Wiki $wiki, Request $request, JobRepository $jobRepository, WikiRepository $wikiRepository, SerializerInterface $serializer): Response
    {
        $job = $serializer->deserialize($request->getContent(), Job::class, 'json', ['object_to_populate' => $job]);
        $jobRepository->updateJob($job, $wiki);

        $wiki = $wikiRepository->findOneById($wiki->getId());
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index','wiki.details']
        ]);
    }

}