<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Repository\ProgramChannelRepository;
use App\Service\Dashboard\DashboardAdmissibleCounter;
use App\Service\Dashboard\DashboardInscriptionCounter;
use App\Service\Dashboard\DashboardMediaMissingCounter;
use App\Service\Dashboard\DashboardMediaToValidateCounter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_COORDINATOR')]
#[Route('/dashboard')]
class DashbordController extends AbstractController
{
    public function __construct(private ProgramChannelRepository $programChannelRepository)
    {
    }

    #[Route('/candidate/reporting', name: 'dashboard_candidate_reporting')]
    public function candidateReporting(DashboardInscriptionCounter $dashboardInscriptionCounter): Response
    {
        $programChannels = $this->getProgramChannels();
        $rows = $dashboardInscriptionCounter->getRows($programChannels);

        return $this->render('dashbord/reporting.html.twig', [
            'rows' => $rows,
            'programChannels' => $programChannels,
        ]);
    }

    #[Route('/media/to_validate/reporting', name: 'dashbord_media_to_validate_reporting')]
    public function mediaToValidateReporting(DashboardMediaToValidateCounter $dashboardMediaToValidateCounter): Response
    {
        $programChannels = $this->getProgramChannels();
        $rows = $dashboardMediaToValidateCounter->getRows($programChannels);

        return $this->render('dashbord/reporting.html.twig', [
            'rows' => $rows,
            'programChannels' => $programChannels,
            'action' => 'toValidate',
        ]);
    }

    #[Route('/media/missing/reporting', name: 'dashbord_media_missing_reporting')]
    public function mediaMissingReporting(DashboardMediaMissingCounter $dashboardMediaMissingCounter): Response
    {
        $programChannels = $this->getProgramChannels();
        $rows = $dashboardMediaMissingCounter->getRows($programChannels);

        return $this->render('dashbord/reporting.html.twig', [
            'rows' => $rows,
            'programChannels' => $programChannels,
            'action' => 'missing',
        ]);
    }

    #[Route('/candidate/admissible', name: 'dashboard_candidate_admissible')]
    public function admissible(DashboardAdmissibleCounter $dashboardAdmissibleCounter): Response
    {
        $programChannels = $this->getProgramChannels();
        $rows = $dashboardAdmissibleCounter->getRows($programChannels);

        return $this->render('dashbord/reporting.html.twig', [
            'rows' => $rows,
            'programChannels' => $programChannels,
        ]);
    }

    private function getProgramChannels(): array
    {
        return $this->programChannelRepository->findBy(['intern' => true]);
    }
}
