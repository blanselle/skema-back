<?php

declare(strict_types=1);

namespace App\Controller\Exam;

use App\Entity\Exam\ExamClassification;
use App\Repository\Exam\ExamClassificationRepository;
use App\Service\Exam\SessionGradingImport;
use App\Service\FileManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\File as UploadedFile;

#[Route('/admin/exams/grades')]
#[IsGranted('ROLE_COORDINATOR')]
class GradeController extends AbstractController
{
    public const IMPORT_VALIDATE = 'accepted';
    public const IMPORT_REJECT = 'cancelled';

    #[Route('/import', name: 'exam_grade_import', methods: ['GET', 'POST'])]
    public function import(
        Request $request,
        SessionGradingImport $import,
        ValidatorInterface $validator,
        FileManager $fileManager,
        ExamClassificationRepository $examClassificationRepository,
    ): Response {
        $examStudents = [];
        $filename = null;
        $examClassification = null;

        if ($request->isMethod('POST')) {
            $examClassificationId = $request->request->get('exam-classification');
            if($examClassificationId === null) {
                throw new BadRequestException('examClassificationId not found');
            }
            $examClassification = $examClassificationRepository->find($examClassificationId);
            if($examClassification === null) {
                throw new BadRequestException('ExamClassification not found');
            }
            
            if ($request->request->has('validation_import')) {

                if (self::IMPORT_VALIDATE === $request->request->get('validation_import')) {
                    $uploadedFile = $request->request->get('file');
                    $errors = [];
                    $examStudents = $import->confirmImport($examClassification, sys_get_temp_dir() . '/' . $uploadedFile, $errors);
                    foreach($errors as $error) {
                        $this->addFlash('error', $error);
                    }
                    $this->addFlash('success', sprintf('%d score(s) importé(s)', count($examStudents)));
                } 
                
                return $this->redirectToRoute('exam_grade_import', ['id' => $examClassification->getId()]);

            } else {
                $uploadedFile = $request->files->get('file');
                $violations = $validator->validate($uploadedFile, [new NotBlank(), new File(['mimeTypes' => ['text/plain','text/csv']])]);
                if ($violations->count() > 0) {
                    foreach($violations as $violation) {
                        $this->addFlash('error', $violation->getMessage());
                    }
                } else {
                    try {
                        $filename = $fileManager->moveFile($uploadedFile, sys_get_temp_dir());
                        $errors = [];
                        $examStudents = $import->execute((new UploadedFile(sys_get_temp_dir() . '/' . $filename)), $examClassification, $errors);

                        foreach($errors as $error) {
                            $this->addFlash('error', $error);
                        }
                        $this->addFlash('success', sprintf('%d score(s) controllé(s)', count($examStudents)));

                    } catch (BadRequestException $e) {
                        $this->addFlash('error', $e->getMessage());
                    }
                }
            }
        }

        return $this->renderForm('exam/grade/index.html.twig', [
            'examClassification' => $examClassification,
            'examStudents' => $examStudents,
            'examClassifications' => $examClassificationRepository->findAll(),
            'file' => $filename
        ]);
    }

    #[Route('/{examClassification}/form', name: 'exam_grade_form', methods: ['GET'])]
    public function form(ExamClassification $examClassification): Response
    {
        return $this->renderForm('exam/grade/form.html.twig', [
            'examClassification' => $examClassification,
        ]);
    }
}
