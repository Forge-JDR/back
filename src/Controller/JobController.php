<?php  

namespace App\Controller;

use App\Repository\UserRepository;


use App\Entity\Wiki;
use App\Entity\Job;
use App\Form\RegistrationFormType;
use App\Repository\JobRepository;
use App\Repository\WikiRepository;
use App\Security\Voter\WikiElementVoter;
use App\Security\Voter\WikiVoter;
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
    #[IsGranted(WikiVoter::EDIT, subject: 'wiki')]
    public function addJob(Wiki $wiki, Request $request, JobRepository $jobRepository, WikiRepository $wikiRepository,SerializerInterface $serializer): Response
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
        // Vérifiez si le job appartient bien au wiki
        if ($job->getWiki()->getId() !== $wiki->getId()) {
            return $this->json(['error' => 'Job does not belong to the specified wiki.'], 400);
        }

        // Supprimez le job via le repository
        $jobRepository->removeJob($job, $wiki); 
        
        // Rafraîchissez le wiki pour obtenir les derniers changements
        $wiki = $wikiRepository->findOneById($wiki->getId());

        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index', 'wiki.details']
        ]);
    }

    #[Route('/api/wikis/{wiki}/jobs/{job}', name: 'update.job', methods: ['PUT'])]
    #[IsGranted(WikiVoter::EDIT, subject: 'wiki')]
    public function updateJob(Wiki $wiki,Job $job, Request $request, JobRepository $jobRepository, WikiRepository $wikiRepository, SerializerInterface $serializer): Response
    {
        // Récupérer l'entité Wiki
        $wiki = $wikiRepository->find($wiki);
        if (!$wiki) {
            return $this->json(['error' => 'Wiki not found.'], 404);
        }
    
        // Récupérer l'entité Job
        $job = $jobRepository->find($job);
        if (!$job) {
            return $this->json(['error' => 'Job not found.'], 404);
        }
    
        // Vérifiez si le job appartient bien au wiki
        if ($job->getWiki()->getId() !== $wiki->getId()) {
            return $this->json(['error' => 'Job does not belong to the specified wiki.'], 400);
        }
    
        // Désérialiser l'objet Job à partir du contenu de la requête
        $serializer->deserialize($request->getContent(), Job::class, 'json', ['object_to_populate' => $job]);
        $jobRepository->updateJob($job, $wiki);
    
        // Rafraîchir le Wiki pour obtenir les derniers changements
        $wiki = $wikiRepository->find($wiki->getId());
    
        return $this->json($wiki, 200, [], [
            'groups' => ['wiki.index', 'wiki.details']
        ]);
    }
    

}