<?php

namespace App\Service\OralTest;

use App\Constants\Parameters\ParametersKeyConstants;
use App\Entity\Exam\ExamLanguage;
use App\Entity\OralTest\CampusOralDay;
use App\Entity\OralTest\CampusOralDayConfiguration;
use App\Entity\Student;
use App\Exception\Parameter\ParameterKeyNotFoundException;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Helper\CacheHelper;
use App\Helper\DateHelper;
use App\Repository\Exam\ExamLanguageRepository;
use App\Repository\OralTest\CampusOralDayRepository;
use App\Repository\Parameter\ParameterKeyRepository;
use App\Repository\Parameter\ParameterRepository;
use DateTimeImmutable;
use DateTimeInterface;

class CampusOralDayManager
{
    public function __construct(
        private CampusOralDayRepository $campusOralDayRepository,
        private ParameterRepository $parameterRepository,
        private ParameterKeyRepository $parameterKeyRepository,
        private CacheHelper $cacheHelper,
        private ExamLanguageRepository $examLanguageRepository,
    ) {}

    /**
     * @throws ParameterNotFoundException
     */
    public function performCampusOralDays(CampusOralDayConfiguration $configuration): array
    {
        return $this->getPlanning(configuration: $configuration);
    }

    /**
     * @param Student $student
     * @return CampusOralDay[]
     * @throws ParameterKeyNotFoundException
     * @throws ParameterNotFoundException
     */
    public function getAvailableSlots(Student $student): array
    {
        $programChannel = $student->getProgramChannel();
        // First Language is available only for BCE so get ANG language for AST
        $firstLanguage = $this->examLanguageRepository->findOneBy(['key' => 'ANG']);
        $secondLanguage = $student->getAdministrativeRecord()?->getExamLanguage();

        // Get all campuses by program Channel which have available places
        $campuses = $this->campusOralDayRepository->findCampusesAvailable(programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);

        // Find available slots depends on campus and date limit
        $now = new DateTimeImmutable('now');
        $availableSlots = [];
        $parameterKeyDateFermetureRDV = $this->parameterKeyRepository->findOneBy(['name' => ParametersKeyConstants::DATE_FERMETURE_RDV,]);
        if (null === $parameterKeyDateFermetureRDV) {
            throw new ParameterKeyNotFoundException(parameterKey: ParametersKeyConstants::DATE_FERMETURE_RDV);
        }

        foreach ($campuses as $campus) {
            $parameterDateFermetureRDV = $this->parameterRepository->findOneParameterWithKeyAndCampusAndProgramChannel(key: $parameterKeyDateFermetureRDV, campus: $campus, programChannel: $programChannel);
            if (null !== $parameterDateFermetureRDV) {
                if ($now > $parameterDateFermetureRDV->getValueDateTime()) {
                    continue;
                }

                // Gets slots for this campus
                $slots = $this->campusOralDayRepository->findByCampusAndProgramChannelAndLanguages(campus: $campus, programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);
                $availableSlots = array_merge($availableSlots, $slots);

                continue;
            }

            // Gets slot depends of limit date
            $parameterKeyLimiteRDV = $this->parameterKeyRepository->findOneBy(['name' => ParametersKeyConstants::LIMITE_RDV,]);
            if (null === $parameterKeyLimiteRDV) {
                throw new ParameterKeyNotFoundException(parameterKey: ParametersKeyConstants::LIMITE_RDV);
            }

            $parameterLimiteRDV = $this->parameterRepository->findOneParameterWithKeyAndCampusAndProgramChannel(key: $parameterKeyLimiteRDV, campus: $campus, programChannel: $programChannel);
            if (null === $parameterLimiteRDV) {
                throw new ParameterNotFoundException(parameterKey: ParametersKeyConstants::DATE_FERMETURE_RDV);
            }

            $dates = $this->campusOralDayRepository->findDatesByCampusAndProgramChannelAndLanguages(campus: $campus, programChannel: $programChannel, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);
            $years = array_unique(array_map(fn(DateTimeInterface $date): string => $date->format('Y'), $dates));
            $publicHolidays = $this->getPublicHolidays(years: $years);

            $startDate = DateHelper::getWorkDay(date: $now, limit: $parameterLimiteRDV->getValueNumber(), publicHolidays: $publicHolidays);
            $slots = $this->campusOralDayRepository->findAvailableSlotByDateAndCampusAndProgramChannelAndLanguages(campus: $campus, programChannel: $programChannel, start: $startDate, firstLanguage: $firstLanguage, secondLanguage: $secondLanguage);
            $availableSlots = array_merge($availableSlots, $slots);
        }

        return $availableSlots;
    }

    public function deleteCampusOralDays(CampusOralDayConfiguration $configuration): void
    {
        $campusOralDays = $this->campusOralDayRepository->findCampusToBeRemovedByConfiguration(configuration: $configuration);
        foreach ($campusOralDays as $campusOralDay) {
            $this->campusOralDayRepository->remove(entity: $campusOralDay, flush: true);
        }
    }

    public function getNumberOfReservedPlaces(CampusOralDayConfiguration $configuration, ?int $firstLanguageId = null, ?int $secondLanguageId = null): int
    {
        return $this->campusOralDayRepository->getNumberOfReservedPlaces(configuration: $configuration, firstLanguageId: $firstLanguageId, secondLanguageId: $secondLanguageId);
    }

    private function getDates(CampusOralDayConfiguration $configuration): array
    {
        $dateStart = null;
        $dateEnd = null;
        $keyStart = $this->parameterKeyRepository->findOneBy(['name' => ParametersKeyConstants::ORAL_DATE_DEBUT]);
        $keyEnd = $this->parameterKeyRepository->findOneBy(['name' => ParametersKeyConstants::ORAL_DATE_FIN]);
        foreach ($configuration->getProgramChannels() as $programChannel) {
            $oralDateDebut = $this->parameterRepository->findOneParameterWithKeyAndCampusAndProgramChannel(
                key: $keyStart,
                campus: $configuration->getCampus(),
                programChannel: $programChannel
            );

            $oralDateFin = $this->parameterRepository->findOneParameterWithKeyAndCampusAndProgramChannel(
                key: $keyEnd,
                campus: $configuration->getCampus(),
                programChannel: $programChannel
            );

            if (null === $oralDateDebut) {
                throw new ParameterNotFoundException(message: "La date de début des oraux pour la voie de concours {$programChannel->getName()} n'est pas paramétrée.");

            }
            if (null === $oralDateFin) {
                throw new ParameterNotFoundException(message: "La date de fin des oraux pour la voie de concours {$programChannel->getName()} n'est pas paramétrée.");
            }

            if (null === $dateStart or $oralDateDebut->getValueDateTime() < $dateStart) {
                $dateStart = $oralDateDebut->getValueDateTime();
            }
            if (null === $dateEnd or $oralDateFin->getValueDateTime() < $dateEnd) {
                $dateEnd = $oralDateFin->getValueDateTime();
            }
        }

        $years = array_unique([$dateStart->format('Y'), $dateEnd->format('Y')]);

        $publicHolidays = $this->getPublicHolidays($years);

        return DateHelper::getWorkingDays(start: $dateStart, end: $dateEnd, publicHolidays: $publicHolidays);
    }

    /**
     * @throws ParameterNotFoundException
     */
    private function getPlanning(CampusOralDayConfiguration $configuration): array
    {
        $period = $this->getDates(configuration: $configuration);

        $firstLanguages = $configuration->getFirstLanguages()->toArray();
        $secondLanguages = $configuration->getSecondLanguages()->toArray();

        $planning = [];
        foreach ($period as $date) {
            if (count($firstLanguages) > 0) {
                if ($configuration->isOptionalLv1() and $configuration->isOptionalLv2()) {
                    $planning[] = $this->getCampusOralDay(configuration: $configuration, date: $date, first: null, second: null);
                }
                foreach ($firstLanguages as $lv) {
                    if ($configuration->isOptionalLv2()) {
                        $planning[] = $this->getCampusOralDay(configuration: $configuration, date: $date, first: $lv, second: null);
                    }

                    foreach ($secondLanguages as $l) {
                        if ($l === $lv) {
                            continue;
                        }
                        $planning[] = $this->getCampusOralDay(configuration: $configuration, date: $date, first: $lv, second: $l);
                    }
                }
            } else {
                foreach ($secondLanguages as $l) {
                    $planning[] = $this->getCampusOralDay(configuration: $configuration, date: $date, first: null, second: $l);
                }
            }
        }

        return $planning;
    }

    private function getCampusOralDay(CampusOralDayConfiguration $configuration, DateTimeImmutable $date, ?ExamLanguage $first, ?ExamLanguage $second): CampusOralDay
    {
        $campusOralDay = $this->campusOralDayRepository->findOneByCampusDayFields(
            configuration: $configuration,
            date: $date,
            first: $first,
            second: $second
        );

        if (null === $campusOralDay) {
            $campusOralDay = new CampusOralDay();
            $campusOralDay
                ->setDate($date)
                ->setFirstLanguage($first)
                ->setSecondLanguage($second)
                ->setConfiguration($configuration)
            ;

            $configuration->addCampusOralDay($campusOralDay);

            $this->campusOralDayRepository->save(entity: $campusOralDay, flush: true);
        }

        return $campusOralDay;
    }

    private function getPublicHolidays(array $years): array
    {
        $publicHolidays = [];
        foreach ($years as $year) {
            $publicHolidayResponse = $this->cacheHelper->getPublicHolidays(year: $year);
            $publicHolidays = array_merge($publicHolidays, array_keys($publicHolidayResponse));
        }

        return $publicHolidays;
    }

    public function canBeReserved(CampusOralDay $campusOralDay): bool
    {
        return $campusOralDay->getNbOfReservedPlaces() < $campusOralDay->getNbOfAvailablePlaces();
    }
}
