<?php

declare(strict_types=1);

namespace App\Action;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SamplePdf extends AbstractController
{
    #[Route('/api/pdf/sample', name: 'app_sample_pdf', methods: ['GET'])]
    public function __invoke(Pdf $pdf): Response
    {
        $html = $this->renderView('sample_pdf.html.twig');

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            'sample.pdf'
        );
    }
}
