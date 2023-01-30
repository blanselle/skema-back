<?php

namespace App\Service\Admissibility;

use App\Constants\Admissibility\CalculatorTypeConstants;
use App\Entity\Admissibility\Admissibility;
use App\Entity\Admissibility\Calculator;
use App\Entity\Admissibility\ConversionTable;
use App\Entity\Exam\ExamStudent;
use App\Entity\ProgramChannel;
use App\Entity\User;
use App\Message\AdmissibilityCalculation;
use App\Repository\Admissibility\CalculatorRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;

class ConversionTableManager
{
    private const CSV_SEPARATOR = ';';

    public function __construct(
        private EntityManagerInterface $em,
        private AdmissibilityManager $admissibilityManager,
        private MessageBusInterface $bus,
        private CalculatorRepository $calculatorRepository,
        private Security $security
    ){
    }

    public function getAdmissibilityConversionTable(Admissibility $admissibility): array
    {
        $notes = [];
        $return = [];
        foreach ($admissibility->getParams() as $param) {
            $notes[$param->getProgramChannel()->getName()] = $this->admissibilityManager->getNotes(
                $admissibility->getExamClassification(),
                $param->getProgramChannel(),
                $param->getMedian(),
                $param->getLowClipping(),
                $param->getHighClipping(),
                $param->getBorders()
            );
        }

        ksort($notes, SORT_STRING);

        foreach ($admissibility->getParams() as $param) {
            foreach ($notes[$param->getProgramChannel()->getName()] as $item) {
                $return[$item['available_score']][$param->getProgramChannel()->getName()]['score'] = $item['note'];
                $return[$item['available_score']][$param->getProgramChannel()->getName()]['student_score'] = $item['student_score'];
                $return[$item['available_score']][$param->getProgramChannel()->getName()]['nb_student'] = $item['nb_student'];
            }
        }

        return $return;
    }

    public function getAverages(array $notes): array
    {
        $return = [];
        foreach ($notes as $programChannels) {
            foreach ($programChannels as $key => $note) {
                if ($note['nb_student'] > 0) {
                    if (empty($return[$key])) {
                        $return[$key]['nb_student'] = 0;
                        $return[$key]['total'] = 0;
                    }

                    $return[$key]['nb_student'] += $note['nb_student'];
                    $return[$key]['total'] += $note['score'] * $note['nb_student'];
                }
            }
        }

        foreach ($return as $key => $item) {
            $return[$key]['average'] = $item['total'] / $item['nb_student'];
        }

        ksort($return, SORT_STRING);

        return $return;
    }

    public function importNotesWithFile(Admissibility $admissibility, ProgramChannel $programChannel, File $file, array &$notes): void
    {
        $i = 0;
        $data = [];
        $handle = fopen($file->getPathname(), "r");
        if (false !== $handle) {
            while (($line = fgetcsv($handle, null, self::CSV_SEPARATOR)) !== false) {
                if ($i !== 0) {
                    $data[$programChannel->getName()][$i]['available_score'] = $line[0];
                    $data[$programChannel->getName()][$i]['note'] = $line[1];
                    $data[$programChannel->getName()][$i]['student_score'] = $line[1];
                    $data[$programChannel->getName()][$i]['nb_student'] = (int)$this->em->getRepository(ExamStudent::class)->countNbStudentByExamClassificationAndScore(
                        $admissibility->getExamClassification(),
                        (float)$line[0]
                    );
                }
                $i++;
            }

            fclose($handle);
        }

        foreach ($data[$programChannel->getName()] as $item) {
            $notes[$item['available_score']][$programChannel->getName()]['score'] = (float)$item['note'];
            $notes[$item['available_score']][$programChannel->getName()]['student_score'] = (float)$item['student_score'];
            $notes[$item['available_score']][$programChannel->getName()]['nb_student'] = $item['nb_student'];
        }
    }

    public function setConversionTableResults(array $notes, array $admissibilityByProgramChannel): void
    {
        foreach ($notes as $score => $programChannels) {
            foreach ($programChannels as $key => $value) {
                if (count($admissibilityByProgramChannel[$key]->getConversionTableResults()) == 0) {
                    $conversionItem = new ConversionTable();
                    $conversionItem
                        ->setNote($value['score'])
                        ->setScore($score)
                        ->setParam($admissibilityByProgramChannel[$key])
                    ;
                    $this->em->persist($conversionItem);
                } else {
                    foreach ($admissibilityByProgramChannel[$key]->getConversionTableResults() as $conversionItem) {
                        if ($conversionItem->getScore() == $score) {
                            $conversionItem->setNote($value['score']);
                        }
                    }
                }
            }
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $calculator = $this->calculatorRepository->findOneBy(['type' => CalculatorTypeConstants::TYPE_RANKING_SIMULATOR]);
        if (null === $calculator) {
            $calculator = new Calculator(CalculatorTypeConstants::TYPE_RANKING_SIMULATOR);
            $this->em->persist($calculator);
        }

        $calculator
            ->setUserId($user->getId())
            ->setLastLaunchDate(new DateTime());

        $this->em->flush();

        $this->bus->dispatch(new AdmissibilityCalculation(message: sprintf('Update admissibility notes %s', (new DateTime())->format('Y-m-d H:i:s')), calculatorId: $calculator->getId(), userId: $user->getId()));
    }
}