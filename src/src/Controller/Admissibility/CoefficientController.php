<?php

declare(strict_types=1);

namespace App\Controller\Admissibility;

use App\Entity\Admissibility\Ranking\Coefficient;
use App\Form\Admissibility\Ranking\CoefficientType;
use App\Repository\Admissibility\Ranking\CoefficientRepository;
use App\Repository\ProgramChannelRepository;
use App\Service\Admissibility\CoefficientManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/admissibility/coefficient')]
class CoefficientController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private CoefficientRepository $coefficientRepository) {}

    #[Route('', name: 'admissibility_coefficient_index', methods: ['GET'])]
    public function index(): Response
    {
        $coefficients = $this->coefficientRepository->findBy([], ['programChannel' => 'ASC']);

        return $this->render('admissibility/coefficient/index.html.twig', [
            'coefficients' => $coefficients
        ]);
    }

    #[Route('/{id}/edit', name: 'admissibility_coefficient_edit', methods: ['GET', 'POST'])]
    public function edit(Coefficient $coefficient, Request $request): Response
    {
        $form = $this->createForm(CoefficientType::class, $coefficient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('admissibility_coefficient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admissibility/coefficient/edit.html.twig', [
            'coefficient' => $coefficient,
            'form' => $form
        ]);
    }

    #[Route('/new', name: 'admissibility_coefficient_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $coefficient = new Coefficient();

        $form = $this->createForm(CoefficientType::class, $coefficient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Coefficient $data */
            $data = $form->getData();
            $findCoefficient = $this->coefficientRepository->findOneBy(['programChannel' => $data->getProgramChannel(), 'type' => $data->getType()]);
            if (!empty($findCoefficient)) {
                /** @var Coefficient $coefficient */
                $coefficient = $findCoefficient;
                $coefficient->setCoefficient($data->getCoefficient());
            } else {
                $this->em->persist($coefficient);
            }
            $this->em->flush();

            return $this->redirectToRoute('admissibility_coefficient_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admissibility/coefficient/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/list', name: 'admissibility_coefficient_list', methods: ['POST'])]
    public function list(Request $request, CoefficientManager $coefficientManager, ProgramChannelRepository $programChannelRepository): Response
    {
        $programChannels = [];
        $params = $request->request->all();
        /** @var int[] $programChannelIds */
        $programChannelIds = $params['programChannelIds']?? [];
        if (count($programChannelIds) > 0) {
            $programChannels = $programChannelRepository->findById($programChannelIds);
        }
        $coefficients = $coefficientManager->getCoefficientParams(programChannels: $programChannels);

        return $this->render('admissibility/_coefficent_list.html.twig', [
            'coefficients' => $coefficients,
        ]);
    }

    #[Route('/{id}', name: 'admissibility_coefficient_delete', methods: ['POST'])]
    public function delete(Request $request, Coefficient $coefficient): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coefficient->getId(), strval($request->request->get('_token')))) {
            $this->em->remove($coefficient);
            $this->em->flush();
        }

        return $this->redirectToRoute('admissibility_coefficient_index', [], Response::HTTP_SEE_OTHER);
    }
}