<?php

namespace App\Manager;

use App\Model\Student\ExportChoiceModel;
use App\Model\Student\ExportStudentListModel;
use App\Repository\StudentRepository;
use App\Service\Export\CsvGenerator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudentExportManager
{
    public function __construct(
        #[Autowire('%backoffice_url%')]
        private string $backofficeUrl,
        private StudentRepository $studentRepository,
        private UrlGeneratorInterface $generator,
        private TranslatorInterface $translator,
        private CsvGenerator $csvGenerator
    ) {}

    public static function getChoices(): array
    {
        $beforeBacSups = [
            new ExportChoiceModel(value: 'programChannel.name', label: 'Voie'),
            new ExportChoiceModel(value: 'student.identifier', label: 'N°'),
            new ExportChoiceModel(value: 'student.state', label: 'Statut'),
            new ExportChoiceModel(value: 'student.gender', label: 'Titre'),
            new ExportChoiceModel(value: 'user.lastName', label: 'Nom'),
            new ExportChoiceModel(value: 'user.firstName', label: 'Prénom'),
            new ExportChoiceModel(value: 'student.firstNameSecondary', label: 'Autres Prénoms'),
            new ExportChoiceModel(value: 'student.dateOfBirth', label: 'Date Naissance'),
            new ExportChoiceModel(value: 'countryBirth.name', label: 'Pays Naissance'),
            new ExportChoiceModel(value: 'nationality.name', label: 'Nationalité 1'),
            new ExportChoiceModel(value: 'nationalitySecondary.name', label: 'Nationalité 2'),
            new ExportChoiceModel(value: 'student.address', label: 'Adresse'),
            new ExportChoiceModel(value: 'student.postalCode', label: 'CP'),
            new ExportChoiceModel(value: 'student.city', label: 'Ville'),
            new ExportChoiceModel(value: 'country.name', label: 'Pays'),
            new ExportChoiceModel(value: 'student.phone', label: 'Tel'),
            new ExportChoiceModel(value: 'user.email', label: 'Email'),
            new ExportChoiceModel(value: 'bac.ine', label: 'INE'),
            new ExportChoiceModel(value: 'bac.rewardedYear', label: 'Année Bac'),
            new ExportChoiceModel(value: 'bacTypes.name', label: 'Série Bac'),
            new ExportChoiceModel(value: 'bacDistinction.label', label: 'Mention Bac'),
            new ExportChoiceModel(value: 'media_bac.state', label: 'Attestation Bac', intern: false),
        ];

        // bacSups choices are places here

        $afterBacSups = [
            new ExportChoiceModel(value: 'experiences', label: 'Expériences', intern: false),
            new ExportChoiceModel(value: 'languages', label: 'Langues parlées', intern: false),
            new ExportChoiceModel(value: 'studentDiploma_diploma.name', label: 'Formation', intern: false),
            new ExportChoiceModel(value: 'studentDiploma_diplomaChannel.name', label: 'Filière', intern: false),
            new ExportChoiceModel(value: 'studentDiploma_studentDiploma.establishment', label: 'Etablissement', intern: false),
            new ExportChoiceModel(value: 'studentDiploma_studentDiploma.postalCode', label: 'CP Etablissement', intern: false),
            new ExportChoiceModel(value: 'studentDiploma_studentDiploma.city', label: 'Ville Etablissement', intern: false),
            new ExportChoiceModel(value: 'administrativeRecord.scholarShip', label: 'Boursier'),
            new ExportChoiceModel(value: 'scholarShipLevel.label', label: 'Echelon'),
            new ExportChoiceModel(value: 'administrativeRecord.highLevelSportsman', label: 'SHN'),
            new ExportChoiceModel(value: 'sportLevel.label', label: 'Niveau'),
            new ExportChoiceModel(value: 'administrativeRecord.thirdTime', label: 'Tiers Temps'),
            new ExportChoiceModel(value: 'media_jdc.state', label: 'Attestation JAPD', intern: false),
            new ExportChoiceModel(value: 'media_certificat_eligibilite.state', label: 'Attestation Universitaire', intern: false),
            new ExportChoiceModel(value: 'media_crous.state', label: 'Attestation Bourse', intern: false),
            new ExportChoiceModel(value: 'media_crous_second.state', label: 'Attestation Bourse 2', intern: false, manual: true),
            new ExportChoiceModel(value: 'media_shn.state', label: 'Attestation SHN', intern: false),
            new ExportChoiceModel(value: 'media_tt.state', label: 'Attestation Tiers Temps', intern: false),
            new ExportChoiceModel(value: 'cv.validated', label: 'CV Validé (Candidat)'),
            new ExportChoiceModel(value: 'schoolRegistrationFees.amount', label: 'Montant payé'),
            new ExportChoiceModel(value: 'schoolRegistrationFeesPayment.createdAt', label: 'Date Transaction'),
            new ExportChoiceModel(value: 'student.updatedAt', label: 'Date dernière modif'),
            new ExportChoiceModel(value: 'student.admissibilityGlobalScore', label: 'Points Ecrits'),
            new ExportChoiceModel(value: 'student.admissibilityRanking', label: 'Rang Ecrits'),
            new ExportChoiceModel(value: 'idCards', label: 'Pièces d\'identité', intern: false),
            new ExportChoiceModel(value: 'studentDiplomas', label: 'Double Diplome', intern: false),
            new ExportChoiceModel(value: 'studentDiplomaDual_diploma.name', label: 'Formation', intern: false),
            new ExportChoiceModel(value: 'studentDiplomaDual_diplomaChannel.name', label: 'Filière', intern: false),
            new ExportChoiceModel(value: 'media_certificat_double_parcours.state', label: 'Attestation double parcours', intern: false),
        ];

        $bacSupsChoices = self::getChoicesForBacSups();
        $examStudentChoices = self::getChoicesForExamStudent();

        $choices = array_merge($beforeBacSups, $bacSupsChoices, $afterBacSups, $examStudentChoices);
        $position = 1;
        /** @var ExportChoiceModel $choice */
        foreach ($choices as $choice) {
            $choice->setPosition($position);
            $position++;
        }

        return $choices;
    }

    private static function getChoicesForBacSups(): array
    {
        $choices = [];

        for ($i = 0; $i < 5; $i++) {
            $label = match($i) {
                0 => 'L1',
                1 => 'L2',
                2 => 'L3',
                3 => 'M1',
                4 => 'M2',
            };
            $bacSupChoices = [
                new ExportChoiceModel(value: "bacSups_{$i}.year", label: "({$label}) Année", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.diploma", label: "({$label}) Diplome", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.diplomaChannel", label: "({$label}) Filière", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.type", label: "({$label}) Type de bulletin", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.schoolReports_{$label}_{$label}S1_score", label: "({$label}) Moyenne {$label}/{$label}S1", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.schoolReports_{$label}_{$label}S1_scoreRetained", label: "({$label}) Moyenne retenue {$label}/{$label}S1", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.media_{$label}_{$label}S1", label: "({$label}) Bulletin {$label}/{$label}S1", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.schoolReports_{$label}S2_score", label: "({$label}) Moyenne {$label}S2", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.schoolReports_{$label}S2_scoreRetained", label: "({$label}) Moyenne retenue {$label}S2", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.media_{$label}S2", label: "({$label}) Bulletin {$label}S2", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup", label: "({$label}) Double diplome", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_diploma", label: "({$label}) Diplome (double diplome)", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_diplomaChannel", label: "({$label}) Filière (double diplome)", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_type", label: "({$label}) Type de bulletin (double diplome)", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_{$label}_{$label}S1_score", label: "({$label}) Moyenne {$label}/{$label}S1 (double diplome)", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_{$label}_{$label}S1_scoreRetained", label: "({$label}) Moyenne retenue {$label}/{$label}S1 (double diplome)", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_media_{$label}_{$label}S1", label: "({$label}) Bulletin {$label}/{$label}S1 (double diplome)", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_{$label}S2_score", label: "({$label}) Moyenne {$label}S2 (double diplome)", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_{$label}S2_scoreRetained", label: "({$label}) Moyenne retenue {$label}S2 (double diplome)", intern: false),
                new ExportChoiceModel(value: "bacSups_{$i}.dualPathBacSup_media_{$label}S2", label: "({$label}) Bulletin {$label}S2 (double diplome)", intern: false),
            ];

            $choices = array_merge($choices, $bacSupChoices);
        }

        return $choices;
    }

    private static function getChoicesForExamStudent(): array
    {
        $angChoices = [];
        $manChoices = [];

        for ($i=1; $i < 6; $i++) {
            $choices = [
                new ExportChoiceModel(value: "examStudents_ANG_{$i}_examClassification.name", label: "Ang Type {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_ANG_{$i}_examSession.dateStart", label: "Ang Date {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_ANG_{$i}_campus.name", label: "Ang Lieu {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_ANG_{$i}_order.state", label: "Ang Statut {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_ANG_{$i}_examStudent.score", label: "Ang Score {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_ANG_{$i}_media.state", label: "Ang Attestation {$i}", intern: false),
            ];

            $angChoices = array_merge($angChoices, $choices);
        }

        for ($i=1; $i < 4; $i++) {
            $choices = [
                new ExportChoiceModel(value: "examStudents_MAN_{$i}_examClassification.name", label: "Management Type {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_MAN_{$i}_examSession.dateStart", label: "Management Date {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_MAN_{$i}_campus.name", label: "Management Lieu {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_MAN_{$i}_order.state", label: "Management Statut {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_MAN_{$i}_examStudent.score", label: "Management Score {$i}", intern: false),
                new ExportChoiceModel(value: "examStudents_MAN_{$i}_media.state", label: "Management Attestation {$i}", intern: false),
            ];

            $manChoices = array_merge($manChoices, $choices);
        }

        return array_merge($angChoices, $manChoices);
    }

    public function export(string $filename, ExportStudentListModel $model): string
    {
        $result = $this->studentRepository->exportStudentList(model: $model);

        // First need to sort $columns by position to order the xls columns
        $columns = $model->getColumns();
        usort($columns, function($a, $b) {
            if ($a->getPosition() === $b->getPosition()) {
                return 0;
            }

            return $a->getPosition() > $b->getPosition()? 1 : -1;
        });

        $hasBacTypeColumn = false;
        $bacTypeColumnKey = false;

        $header = [];
        foreach ($columns as $keyColumn => $column) {
            if ('bacTypes.name' === $column->getValue()) {
                $hasBacTypeColumn = true;
                $bacTypeColumnKey = (int)$keyColumn;
            }

            $header[] = $column->getLabel();
        }

        // Add ended column
        $header[] = 'Lien vers la fiche candidat';

        // If we want to export bacType name, in case of student which has 2 bacType we must add a new colum for the second bacType
        if ($hasBacTypeColumn) {
            $pos = false === $bacTypeColumnKey ? count($header) : $bacTypeColumnKey + 1;
            array_splice($header, $pos, 0, 'Série Bac 2');
        }

        $generator = $this->generator;
        $translator = $this->translator;

        $fields = array_map(function($c) {
            return str_replace('.', '_', $c->getValue());
        }, $columns);
        $fields[] = 'link';
        if ($hasBacTypeColumn) {
            $pos = false === $bacTypeColumnKey ? count($header) : $bacTypeColumnKey + 1;
            array_splice($fields, $pos, 0, 'bacTypes_name_2');
        }

        $list =  [];
        foreach ($result as $u) {
            $studentId = $u['student_id'];
            if ($hasBacTypeColumn) {
                if (isset($list[$studentId])) {
                    $list[$studentId]['bacTypes_name_2'] = $u['bacTypes_name'];
                    $list[$studentId] = array_merge(array_flip($fields), $list[$studentId]);
                    continue;
                }

                $u['bacTypes_name_2'] = null;
            }

            if (null !== ($u['student_state']?? null)) {
                $u['student_state'] = $translator->trans("workflow.student.{$u['student_state']}");
            }
            if (isset($u['student_gender'])) {
                $u['student_gender'] = match ($u['student_gender']) {
                    'M' => 'M',
                    'F' => 'Mme',
                    default => 'Autre'
                };
            }
            if (isset($u['bac_rewardedYear'])) {
                if (empty($u['bac_rewardedYear'])) {
                    $u['bac_rewardedYear'] = 'Non';
                }
            }
            if (isset($u['administrativeRecord_scholarShip'])) {
                $u['administrativeRecord_scholarShip'] = (true === $u['administrativeRecord_scholarShip']) ? 'Oui' : 'Non';
            }
            if (isset($u['administrativeRecord_highLevelSportsman'])) {
                $u['administrativeRecord_highLevelSportsman'] = (true === $u['administrativeRecord_highLevelSportsman']) ? 'Oui' : 'Non';
            }
            if (isset($u['administrativeRecord_thirdTime'])) {
                $u['administrativeRecord_thirdTime'] = (true === $u['administrativeRecord_thirdTime']) ? 'Oui' : 'Non';
            }

            $fieldsForMedia = preg_grep('/^(.+)?media.*/', array_keys($u));
            if (false !== $fieldsForMedia and count($fieldsForMedia) > 0) {
                foreach ($fieldsForMedia as $field) {
                    if (null !== ($u[$field] ?? null)) {
                        $u[$field] = $translator->trans("workflow.media.{$u[$field]}");
                    }
                }
            }

            if (isset($u['cv_validated'])) {
                $u['cv_validated'] = (true === $u['cv_validated']) ? 'Oui' : 'Non';
            }
            if (isset($u['schoolRegistrationFees_amount'])) {
                $u['schoolRegistrationFees_amount'] = sprintf('%s €', number_format($u['schoolRegistrationFees_amount'] / 100, 2, ','));
            }

            // format date
            $fieldsForDate = preg_grep('/^(.+)?_date.*/', array_keys($u));
            if (false !== $fieldsForDate and count($fieldsForDate) > 0) {
                foreach ($fieldsForDate as $field) {
                    if (null !== ($u[$field] ?? null)) {
                        $u[$field] = ($u[$field])->format('d/m/Y');
                    }
                }
            }

            $fieldsForCreatedAt = preg_grep('/^(.+)?createdAt.*/', array_keys($u));
            if (false !== $fieldsForCreatedAt and count($fieldsForCreatedAt) > 0) {
                foreach ($fieldsForCreatedAt as $field) {
                    if (null !== ($u[$field] ?? null)) {
                        $u[$field] = ($u[$field])->format('d/m/Y H:i:s');
                    }
                }
            }

            $fieldsForUpdatedAt = preg_grep('/^(.+)?updatedAt.*/', array_keys($u));
            if (false !== $fieldsForUpdatedAt and count($fieldsForUpdatedAt) > 0) {
                foreach ($fieldsForUpdatedAt as $field) {
                    if (null !== ($u[$field] ?? null)) {
                        $u[$field] = ($u[$field])->format('d/m/Y H:i:s');
                    }
                }
            }

            $u['link'] = sprintf('%s/%s', rtrim($this->backofficeUrl, '/'), ltrim($generator->generate('student_edit', ['id' => $u['student_id']]), '/'));

            unset($u['student_id']);

            $list[$studentId] = array_merge(array_flip($fields), $u);
        }

        return $this->csvGenerator->generate(filename: $filename, header: $header, list: $list);
    }
}