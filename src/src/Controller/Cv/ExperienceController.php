<?php

namespace App\Controller\Cv;

use App\Entity\CV\Cv;
use App\Entity\CV\Experience;
use App\Form\Admin\User\CV\ExperienceType;
use App\Repository\CV\ExperienceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cv/{id}/experiences')]
#[IsGranted('ROLE_COORDINATOR')]
class ExperienceController extends AbstractController
{
    public function __construct(private ExperienceRepository $repository){}

    #[Route('/new', name: 'cv_experience_new', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function new(Request $request, Cv $cv): Response
    {
        $experience = new Experience();
        $cv->addExperience($experience);

        $form = $this->createForm(ExperienceType::class, $experience, [
            'action' => $this->generateUrl('cv_experience_new', ['id' => $cv->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->repository->save(entity: $experience, flush: true);

            return $this->redirectToRoute('student_edit', ['id' => $cv->getStudent()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('student/cv/experience_new.html.twig', [
            'form' => $form,
            'student' => $cv->getStudent(),
        ]);
    }
}