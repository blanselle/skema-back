<?php

declare(strict_types=1);

namespace App\Controller\Admissibility;

use App\Constants\Admissibility\AdmissibilityConstants;
use App\Entity\Admissibility\Admissibility;
use App\Entity\Admissibility\Param;
use App\Entity\Exam\ExamClassification;
use App\Form\Admissibility\AdmissibilityType;
use App\Repository\Admissibility\AdmissibilityRepository;
use App\Repository\Admissibility\ConversionTableRepository;
use App\Service\Admissibility\ConversionTableManager;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/admissibility')]
#[IsGranted('ROLE_RESPONSABLE')]
class ConversionController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('', name: 'admissibility_conversion_index', methods: ['GET', 'POST'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $this->em->getRepository(Admissibility::class)->findBy([], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admissibility/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/new', name: 'admissibility_conversion_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $admissibility = new Admissibility();

        $form = $this->createForm(AdmissibilityType::class, $admissibility, ['attr' => ['disabled' => false]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $existingAdmissibility = $this->em->getRepository(Admissibility::class)->findOneBy(['examClassification' => $data->getExamClassification()]);
            if (!empty($existingAdmissibility)) {
                return $this->redirectToRoute('admissibility_conversion_edit', ['id' => $existingAdmissibility->getId()]);
            } else {
                $examClassification = $this->em->getRepository(ExamClassification::class)->find($data->getExamClassification());

                foreach ($examClassification->getProgramChannels() as $programChannel) {
                    $param = new Param();
                    $param->setProgramChannel($programChannel);

                    $this->em->persist($param);

                    $admissibility->addParam($param);
                    $this->em->persist($admissibility);
                }
                $this->em->flush();

                return $this->redirectToRoute('admissibility_conversion_edit', ['id' => $admissibility->getId()]);
            }
        }

        return $this->renderForm('admissibility/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'admissibility_conversion_edit', methods: ['GET', 'POST'])]
    public function edit(Admissibility $admissibility, Request $request, FileManager $fileManager): Response
    {
        $form = $this->createForm(AdmissibilityType::class, $admissibility, ['attr' => ['disabled' => true]]);

        switch ($admissibility->getType()) {
            case AdmissibilityConstants::CALCUL_WITH_BORDERS:
                foreach ($form->get('params') as $param) {
                    $param->remove('median');
                    $param->remove('file');
                }
                break;
            case AdmissibilityConstants::CALCUL_WITH_MEDIAN:
                foreach ($form->get('params') as $param) {
                    $param->remove('borders');
                    $param->remove('file');
                }
                break;
            case AdmissibilityConstants::CALCUL_WITH_IMPORT:
                foreach ($form->get('params') as $param) {
                    $param->remove('borders');
                    $param->remove('median');
                    $param->remove('highClipping');
                    $param->remove('lowClipping');
                }
                break;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($request->files->get('admissibility'))) {
                foreach($admissibility->getParams() as $key => $param) {
                    $file = $request->files->get('admissibility')['params'][$key];
                    $filename = $fileManager->moveFile($file['file'], '/tmp');
                    $param->setFile($filename);
                }
            }
            $this->em->flush();

            return $this->redirectToRoute('admissibility_conversion_generate', ['id' => $admissibility->getId()]);
        }

        return $this->renderForm('admissibility/edit.html.twig', [
            'form' => $form,
            'admissibility' => $admissibility,
            'type_border' => AdmissibilityConstants::CALCUL_WITH_BORDERS,
            'type_median' => AdmissibilityConstants::CALCUL_WITH_MEDIAN
        ]);
    }

    #[Route('/{id<\d+>}/generate', name: 'admissibility_conversion_generate', methods: ['GET', 'POST'])]
    public function generate(Admissibility $admissibility, Request $request, ConversionTableManager $conversionTableManager): Response
    {
        $notes = [];
        if ($admissibility->getType() !== AdmissibilityConstants::CALCUL_WITH_IMPORT) {
            $notes = $conversionTableManager->getAdmissibilityConversionTable($admissibility);
            $averages = $conversionTableManager->getAverages($notes);
        } else {
            foreach ($admissibility->getParams() as $param) {
                if (!empty($param->getFile())) {
                    $filesystem = new Filesystem();
                    if (!$filesystem->exists('/tmp/'.$param->getFile())) {
                        $this->addFlash('error', sprintf('The file %s is not found for admissibilty %s with program channel %s', $param->getFile(), $admissibility->getType(), $param->getProgramChannel()->getName()));
                        continue;
                    }

                    $conversionTableManager->importNotesWithFile(
                        $admissibility,
                        $param->getProgramChannel(),
                        new File('/tmp/'.$param->getFile()),
                        $notes
                    );
                    $param->setFile(null);
                }
            }

            $averages = $conversionTableManager->getAverages($notes);
        }

        if ($request->isMethod('POST')) {
            $this->addFlash('success', 'Vous recevrez une notification lorsque la simulation sera terminée.');
            $admissibilityByProgramChannel = $admissibility->getAdmissibilityByProgramChannel();
            $conversionTableManager->setConversionTableResults($notes, $admissibilityByProgramChannel);

            if (empty($request->files->get('admissibility'))) {
                $this->em->flush();

                return $this->redirectToRoute('admissibility_conversion_index');
            }
        }

        return $this->render('admissibility/generate.html.twig', [
            'notes' => $notes,
            'averages' => $averages,
            'admissibility' => $admissibility
        ]);
    }

    #[Route('/{id<\d+>}', name: 'admissibility_conversion_delete', methods: ['POST'])]
    public function delete(Request $request, Admissibility $admissibility): Response
    {
        if ($this->isCsrfTokenValid('delete'.$admissibility->getId(), strval($request->request->get('_token')))) {
            $this->em->remove($admissibility);
            $this->em->flush();
        }

        return $this->redirectToRoute('admissibility_conversion_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/hasConversionTable', name: 'admissibility_has_conversion_table', methods: ['GET'])]
    public function hasConversionTable(Request $request, AdmissibilityRepository $admissibilityRepository, ConversionTableRepository $conversionTableRepository): Response
    {
        $examId = $request->query->getInt('examId');
        $admissibility = $admissibilityRepository->findOneBy(['examClassification' => $examId]);
        return new JsonResponse([
            'hasConversionTable' => $conversionTableRepository->hasConversionTable(examClassificationId: $examId),
            'admissibilityId' => $admissibility?->getId(),
        ]);
    }
}
