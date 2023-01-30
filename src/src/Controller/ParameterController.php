<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Parameter\Parameter;
use App\Form\Parameter\ParameterCreateType;
use App\Form\Parameter\ParameterType;
use App\Repository\Parameter\ParameterRepository;
use App\Repository\ProgramChannelRepository;
use App\Service\Datatable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/admin/parameter')]
#[IsGranted('ROLE_ADMIN')]
class ParameterController extends AbstractController
{
    #[Route('/', name: 'parameter_index', methods: ['GET', 'POST'])]
    public function index(Request $request, Datatable $datatable): Response
    {
        $data = [];
        $data['filters'] = $datatable->cleanEmptyArray([
            'name'  => $datatable->filter('name'),
            'descr' => is_string($datatable->filter('descr')) && !empty($datatable->filter('descr'))
                ? html_entity_decode($datatable->filter('descr')) : null,
        ]);

        $data['columns'] = [
            'label'         => ['db' => 'k.name', 'label' => "Nom"],
            'type'          => ['db' => 'k.type', 'label' => "Type"],
            'value'         => ['label' => "Valeur"],
            'description'   => ['db' => 'k.description', 'label' => "Description"],
            'subject'       => ['label' => "Sujet"],
        ];

        return $datatable->getDatatableResponse($request, Parameter::class, $data, 'parameter');
    }

    #[Route('/new', name: 'parameter_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ParameterRepository $parameterRepository,
        ProgramChannelRepository $programChannelRepository
    ): Response
    {
        $parameter = new Parameter();
        $programChannels = $programChannelRepository->findAll();
        foreach ($programChannels as $programChannel) {
            $parameter->addProgramChannel($programChannel);
        }

        $form = $this->createForm(ParameterCreateType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parameterRepository->add($parameter, true);

            return $this->redirectToRoute('parameter_edit', ['id' => $parameter->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('parameter/new.html.twig', [
            'parameter' => $parameter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'parameter_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Parameter $parameter, 
        ParameterRepository $parameterRepository, 
        CacheInterface $cache, 
        ProgramChannelRepository $programChannelRepository
    ): Response {
        $form = $this->createForm(ParameterType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parameterRepository->add($parameter, true);
            
            // In redis, the parameters have the programChannel as suffix 
            // so we delete all the entries with the parameter
            $key = $parameter->getKey()->getName();
            foreach($programChannelRepository->findAll() as $programChannel) {
                $cache->delete($key . $programChannel->getId());
            }

            return $this->redirectToRoute('parameter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('parameter/edit.html.twig', [
            'item' => $parameter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'parameter_delete', methods: ['POST'])]
    public function delete(Request $request, Parameter $parameter, ParameterRepository $parameterRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parameter->getId(), strval($request->request->get('_token')))) {
            $parameterRepository->remove($parameter, true);
        }

        return $this->redirectToRoute('parameter_index', [], Response::HTTP_SEE_OTHER);
    }
}
