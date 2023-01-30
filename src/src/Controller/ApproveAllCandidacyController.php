<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Message\ApproveAllCandidacy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class ApproveAllCandidacyController extends AbstractController
{
    #[Route('/approve_all_candidacy', name: 'app_approve_all_candidacy', methods: ['POST'])]
    public function approve(Request $request, MessageBusInterface $bus): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('app_approve_all_candidacy', strval($submittedToken))) {
            throw new BadRequestException('The token csrf is not valid');
        }

        /** @var User $user */
        $user = $this->getUser();
        
        $bus->dispatch(new ApproveAllCandidacy($user->getId()));

        $this->addFlash('success', "L’approbation des candidatures est en cours. Vous recevrez une notification lorsque le traitement sera terminé");
        
        return $this->redirectToRoute('student_index');
    }
}
