<?php

declare(strict_types=1);

namespace App\Service\Admissibility\Cv;

use App\Constants\CV\BacSupConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\ProgramChannel\ProgramChannelKeyConstants;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\SchoolReport;
use App\Ruler\CV\CvRuler;
use Exception;

/**
 * Calcul de la note d'un Cv d'un etudiant. La partie bonus est géré par le moteur de rêgles
 */
class CvCalculator
{
    public function __construct(
        private CvRuler $cvRuler,
    ) {
        
    }

    public function updateCvNotes(Cv $cv): void
    {
        $cv->setNote($this->getCvNote($cv));
        $cv->setBonus($this->cvRuler->getBonus($cv->getStudent()));
    }

    /**
     * Calcul de la moyenne la plus avantageuse pour l'etudiant
     * 
     * En fonction de la voie de concours et de son eventuel double diplome, cette fonction calcul la 
     * moyenne la plus élevé pour l'étudiant.
     * 
     * Les moyennes des années post bac facultatives (pour les voies de concours AST1 et AST2) sont 
     * prises en compte uniquement si elles bonifient la moyenne des notes :
     * IF AST1 THEN MAX((S1+S2)/2 ; (S1+S2+S3)/3 ; (S1+S2+S3+S4)/4)
     * IF AST2 THEN MAX((S1+S2+S3+S4)/4 ; (S1+S2+S3+S4+S5)/5 ; (S1+S2+S3+S4+S5+S6)/6)
     *
     * @param Cv $cv
     * @return float
     */
    private function getCvNote(Cv $cv): float
    {
        $bacSups = $cv->getBacSups()->toArray();

        if(empty($bacSups)) {
            return 0.0;
        }

        // On calcule toutes les moyennes possibles pour
        $notesWithoutAdditionnals = [];
        $notesWithAdditionnals = [];
        $this->calculAllNotes($bacSups, false, $notesWithoutAdditionnals);
        $this->calculAllNotes($bacSups, true, $notesWithAdditionnals);
        
        // Les AST2 ne prennent pas en compte la moyenne S1, S1+S2
        if($cv->getStudent()->getProgramChannel()->getKey() === ProgramChannelKeyConstants::AST2){
            $notesWithoutAdditionnals = array_slice($notesWithoutAdditionnals, 2);
            $notesWithAdditionnals = array_slice($notesWithAdditionnals, 2);
        }

        array_shift($notesWithoutAdditionnals);
        array_shift($notesWithAdditionnals);

        $notes = array_merge(
            $notesWithoutAdditionnals, // Calcul notes simples
            $notesWithAdditionnals, // Calcul notes en double diplomes
        );

        return (!empty($notes)) ? max($notes) : 0.0; // On prend la note la plus avantageuse
    }

    /**
     * Calcul toutes les notes possible du parcours scolaire de l'etudiant
     * 
     * Cette fonction peut être executés en prendant en compte ou non le double diplome avec l'argument additionnal.
     * Elle retourne un tableau contenant : [avg(S1), avg(S1, S2), avg(S1, S2, S3), ...]
     * 
     */
    private function calculAllNotes(array $bacSups, bool $additionnal, array &$avgs = []) : void
    {
        if(count($bacSups) === 0) {
            return;
        }

        /** @var BacSup $bacSup */
        $bacSup = $bacSups[0];

        if(($additionnal and $bacSup->getDualPathBacSup() === null and null !== $bacSup->getDiploma() and false === $bacSup->getDiploma()->getAdditional()) or
            (!$additionnal and $bacSup->getParent() === null)) {

            try{
                $noteSemestre = $this->getNoteBacSup($bacSup);
            } catch(Exception $e) {
                return; // En cas de problème de calcul d'une note de semestre, la note du cv sera 0
            }

            if(count($avgs) === 0) { // Premier tour de boucle
                $avgs[0] = $noteSemestre[0];
                $avgs[1] = ($noteSemestre[0] + $noteSemestre[1])/2;
            } else {
                $avgs[] = $this->addValToAvg($noteSemestre[0], end($avgs), count($avgs));
                $avgs[] = $this->addValToAvg($noteSemestre[1], end($avgs), count($avgs));
            }
        }

        array_shift($bacSups);
        $this->calculAllNotes($bacSups, $additionnal, $avgs);
    }

    /**
     * Ajoute une nouvelle valeur à la moyenne
     *
     * @param float $newVal Nouvelle valeur
     * @param float $avg Moyenne actuelle
     * @param integer $nb Nombre d'item dans la moyenne actuelle
     * @return float new avg
     */
    private function addValToAvg(float $newVal, float $avg = 0, int $nb = 0): float
    {
        return ($nb * $avg + $newVal) / ($nb + 1);
    }

    /**
     * Récupère un tableau de 2 note qui represente chaqu'une un semestre
     * 
     * condition :
     * Si le BacSup est annuel ==> les deux semestres auront la même note
     * Si le BacSUp est semestriel alors on met les notes du schoolReport
     *
     * @param BacSup $bacSup
     * @return array
     */
    private function getNoteBacSup(BacSup $bacSup): array
    {
        $schoolReports = $bacSup->getSchoolReports();
        // Si le bacSup est de type annuel on considère qu'il a 2 semestriel identique
        // Ou si semestriel mais un seul bulletin
        if (($bacSup->getType() === BacSupConstants::TYPE_ANNUAL && count($schoolReports) === 1) ||
            ($bacSup->getType() === BacSupConstants::TYPE_SEMESTRIAL && count($schoolReports) === 1)) {
            return [$this->getNoteSemestre($schoolReports[0]), $this->getNoteSemestre($schoolReports[0])];
        } elseif($bacSup->getType() === BacSupConstants::TYPE_SEMESTRIAL && count($schoolReports) === 2) {
            return [$this->getNoteSemestre($schoolReports[0]), $this->getNoteSemestre($schoolReports[1])];
        }

        throw new Exception('Le type du bacSup ne correspond pas au nombre de schoolReport');
    }

    /**
     * Recuperation de la note du semestre
     * On prend en priorité la note retenue et sinon la note normale
     *
     * @param SchoolReport $schoolReport
     * @return float
     */
    private function getNoteSemestre(SchoolReport $schoolReport) : float
    {
        if(null === $schoolReport->getMedia()) {
            return 0;
        }

        if(MediaWorflowStateConstants::STATE_ACCEPTED !== $schoolReport->getMedia()->getState()) {
            return 0;
        }

        if(null === $schoolReport->getScoreRetained() && null === $schoolReport->getScore()) {
            throw new Exception('Le schoolReport n\'a pas de note');
        }

        if(null === $schoolReport->getScoreRetained()) {
            return $schoolReport->getScore();
        }

        return $schoolReport->getScoreRetained();
    }
}
