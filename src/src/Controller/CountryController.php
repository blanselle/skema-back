<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use App\Service\Datatable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/countries')]
#[IsGranted('ROLE_ADMIN')]
class CountryController extends AbstractController
{
    #[Route('', name: 'country_index', methods: ['GET', 'POST'])]
    public function index(Request $request, CountryRepository $countryRepository, Datatable $datatable): Response
    {
        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'code'     => $datatable->filter('code'),
            'name'     => is_string($datatable->filter('name')) && !empty($datatable->filter('name'))
                ? html_entity_decode($datatable->filter('name')) : null,
            'codeSISE'     => $datatable->filter('codeSISE'),
        ]);

        $data['columns'] = [
            'idCountry'     => ['db' => 'a.idCountry', 'label' => "Code"],
            'name'          => ['db' => 'a.name', 'label' => "Nom du pays"],
            'codeSISE'      => ['db' => 'a.codeSISE', 'label' => "codeSISE"],
            'nationality'   => ['db' => 'a.nationality', 'label' => "Nationalité"],
            'active'        => ['db' => 'a.active', 'label' => "Actif"],
            'action'        => ['label' => "Actions"],
        ];

        return $datatable->getDatatableResponse($request, Country::class, $data, 'country');
    }

    #[Route('/new', name: 'country_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($country);
            $entityManager->flush();

            return $this->redirectToRoute('country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('country/new.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'country_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Country $country, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('country/edit.html.twig', [
            'item' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($country);
            $entityManager->flush();
        }

        return $this->redirectToRoute('country_index', [], Response::HTTP_SEE_OTHER);
    }
}
