<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\DbHelper;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class ResetDbController extends AbstractController
{
    #[Route('/reset-db', name: 'reset_db_index', methods: ['GET', 'POST'])]
    public function index(Request $request, DbHelper $dbHelper): Response
    {
        $form = $this
            ->createFormBuilder()
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Lancer la raz',
                    'attr' => [
                        'class' => 'btn btn-danger',
                        'onclick' => 'return confirm("Attention, cette action est définitive. Etes-vous sûr de vouloir lancer la remise à zéro de la base de donnée?")'
                    ]
                ]
            )
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $dbHelper->resetDb();
                $this->addFlash('info', 'La base de donnée a été remise à zéro.');

                return $this->redirectToRoute('reset_db_index');
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    sprintf('La base de donnée n\'a pas pu être remise à zéro: %s', $e->getMessage())
                );
            }
        }

        return $this->render('reset-db.html.twig', ['form' => $form->createView()]);
    }
}
