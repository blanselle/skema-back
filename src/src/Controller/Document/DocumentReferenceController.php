<?php

declare(strict_types=1);

namespace App\Controller\Document;

use App\Entity\Document\DocumentReference;
use App\Form\DocumentReference\DocumentReferenceType;
use App\Form\DocumentReference\DocumentReferenceCreateType;
use App\Repository\Document\DocumentReferenceRepository;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/document/reference')]
#[IsGranted('ROLE_RESPONSABLE')]
class DocumentReferenceController extends AbstractController
{
    private FileManager $fileManager;

    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    #[Route('', name: 'document_reference_index', methods: ['GET'])]
    public function index(DocumentReferenceRepository $documentReferenceRepository): Response
    {
        return $this->render('document/document_reference/index.html.twig', [
            'document_references' => $documentReferenceRepository->findBy([], ['createdAt' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'document_reference_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $documentReference = new DocumentReference();
        /** @var Form $form $form */
        $form = $this->createForm(DocumentReferenceCreateType::class, $documentReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file instanceof UploadedFile) {
                $directory = strval($this->getParameter('document_reference_directory'));
                $filename = $this->fileManager->moveFile(
                    $file,
                    $directory
                );

                if (null != $filename) {
                    $documentReference->setFile($directory . $filename);
                }
            }

            $entityManager->persist($documentReference);
            $entityManager->flush();

            return $this->redirectToRoute('document_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('document/document_reference/new.html.twig', [
            'document_reference' => $documentReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'document_reference_edit', methods: ['GET','POST'])]
    public function edit(Request $request, DocumentReference $documentReference, EntityManagerInterface $entityManager): Response
    {
        /** @var Form $form $form */
        $form = $this->createForm(DocumentReferenceType::class, $documentReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if (null !== $file) {
                $file->move(
                    sprintf(
                        '%s/public/%s',
                        $this->getParameter('kernel.project_dir'),
                        strval($this->getParameter('document_reference_directory'))
                    ),
                    $documentReference->getFile()
                );
            }

            $entityManager->flush();

            return $this->redirectToRoute('document_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('document/document_reference/edit.html.twig', [
            'document_reference' => $documentReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'document_reference_delete', methods: ['POST'])]
    public function delete(Request $request, DocumentReference $documentReference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$documentReference->getId(), strval($request->request->get('_token')))) {
            $entityManager->remove($documentReference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('document_reference_index');
    }
}
