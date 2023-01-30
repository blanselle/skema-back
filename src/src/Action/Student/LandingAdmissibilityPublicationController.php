<?php

declare(strict_types=1);

namespace App\Action\Student;

use App\Constants\User\StudentWorkflowStateConstants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Dto\LandingAdmissibilityOutput;
use DateTime;
use App\Manager\ParameterManager;
use App\Service\Bloc\BlocRewriter;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use App\Repository\Admissibility\LandingPage\AdmissibilityStudentTokenRepository;
use App\Repository\ProgramChannelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LandingAdmissibilityPublicationController extends AbstractController
{
    public function __invoke(
        Request $request,
        ParameterManager $parameterManager,
        BlocRewriter $blocRewriter,
        AdmissibilityStudentTokenRepository $admissibilityStudentTokenRepository,
        ProgramChannelRepository $programChannelRepository,
    ): LandingAdmissibilityOutput {
        
        $requestToken = $request->query->get('token');

        if (null === $requestToken) {
            throw new BadRequestHttpException("Missing token query parameter");
        }
        $student = $admissibilityStudentTokenRepository->getAdmissibilityStudent($requestToken);
        if ($student === false) {
            throw new BadRequestException("Pas d'étudiant trouvé avec le token {$requestToken}");
        }

        $programChannel = $programChannelRepository->find($student['program_channel_id']);
        $dateAdmissibility = $parameterManager->getParameter('dateResultatsAdmissibilite', $programChannel)->getValue();
        
        $now = (new DateTime());
        if ($now < $dateAdmissibility) {
            $bloc = $blocRewriter->rewriteBloc('LANDING_ADMISSIBILITE', $programChannel);
            throw new BadRequestException($bloc->getContent());
        }

        $output = new LandingAdmissibilityOutput();
        $output->fullname = sprintf('%s %s', $student['first_name'], $student['last_name']);
        $output->identifier = $student['identifier'];
        $output->result = $student['state'];
        $output->admissible = ($student['state'] === StudentWorkflowStateConstants::STATE_ADMISSIBLE);

        return $output;
    }
}
