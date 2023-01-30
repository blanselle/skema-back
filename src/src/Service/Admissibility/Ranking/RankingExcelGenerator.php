<?php

namespace App\Service\Admissibility\Ranking;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class RankingExcelGenerator
{
    private const DEFAULT_STYLE = [
        'font' => [
            'name' => 'Calibri',
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
        ],
    ];

    private const STYLE_HEADER = [
        'font' => [
            'name' => 'Calibri',
            'bold' => false,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'argb' => 'FFFFFF00',
            ],
        ],
    ];

    private const STYLE_DATA = [
        'font' => [
            'name' => 'Calibri',
            'bold' => false,
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
    ];

    public function __construct(
        private DataToExportInterface $dataAverageToExport,
        private DataToExportInterface $dataForProgramChannelsToExport,
        private DataRecordAverageToExport $dataRecordAverageToExport,
        private DataToExportInterface $dataNoteToExport
    ){}

    public function export(array $coefficients, string $filename, array $programChannels): string
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(
            $spreadsheet->getIndex(
                $spreadsheet->getSheetByName('Worksheet')
            )
        );

        // Calculate sum coefficient for each programChannel
        $totalCoefficientByProgramChannel = [];

        foreach ($coefficients as $position => $coefficient) {
            foreach($coefficient as $type => $values) {
                if (!isset($totalCoefficientByProgramChannel[$position])) {
                    $totalCoefficientByProgramChannel[$position] = 0;
                }
                $totalCoefficientByProgramChannel[$position] += $values['value'];
            }
        }

        $this->setWorksheetForProgramChannels(coefficients: $totalCoefficientByProgramChannel, programChannels: $programChannels, spreadsheet: $spreadsheet);
        $this->setWorksheetForAverage(coefficients: $totalCoefficientByProgramChannel, programChannels: $programChannels, spreadsheet: $spreadsheet);
        $this->setWorksheetForNote(coefficients: $coefficients, programChannels: $programChannels, spreadsheet: $spreadsheet);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        if ($tempFile === false || !file_exists($tempFile)) {
            throw new FileNotFoundException('The excel file is not found');
        }

        $writer->save($tempFile);

        return $tempFile;
    }

    private function setWorksheetForProgramChannels(array $coefficients, array $programChannels, Spreadsheet $spreadsheet): void
    {
        // Create worksheet
        $title = sprintf('Concours AST %s', date('Y'));
        $subTitle1 = 'Simulation de barre d\'Admissibilité';
        $subTitle2 = '%d candidats présents pour l\'année %s';
        $startedCell = 'A1';

        $data = $this->dataForProgramChannelsToExport->generate(coefficients: $coefficients, programChannels: $programChannels);

        foreach ($data as $datum) {
            $worksheet = new Worksheet();
            $spreadsheet->addSheet($worksheet);
            $worksheet->setTitle($datum['programChannel']->getName());

            $students = $datum['students'];
            $nbOfCandidates = count($students);
            $cell = $worksheet->getCell($startedCell);
            $line = $cell->getRow();
            $column = Coordinate::columnIndexFromString($cell->getColumn());

            // Add titles
            $worksheet->setCellValueByColumnAndRow($column, $line, $title);
            $worksheet->getCell(Coordinate::stringFromColumnIndex($column).$line)->getStyle()->applyFromArray(self::DEFAULT_STYLE);
            ++$line;
            $worksheet->setCellValueByColumnAndRow($column, $line, $subTitle1);
            $style = $worksheet->getCell(Coordinate::stringFromColumnIndex($column).$line)->getStyle()->applyFromArray(self::DEFAULT_STYLE);
            $style->getFont()->setBold(false);
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $worksheet->mergeCells(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + 3), $line));
            ++$line;
            switch ($datum['programChannel']->getKey()) {
                case 'ast1':
                    $subTitle2All = sprintf($subTitle2, $nbOfCandidates, '1');
                    break;
                case 'ast2':
                    $subTitle2All = sprintf($subTitle2, $nbOfCandidates, '2');
                    break;
                default:
                    $subTitle2All = sprintf($subTitle2, $nbOfCandidates, 'x');
                    break;
            }
            $worksheet->setCellValueByColumnAndRow($column, $line, $subTitle2All);
            $style = $worksheet->getCell(Coordinate::stringFromColumnIndex($column).$line)->getStyle()->applyFromArray(self::DEFAULT_STYLE);
            $style->getFont()->setBold(false);
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $worksheet->mergeCells(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + 3), $line));
            $line += 2;

            // Add data
            $worksheet->fromArray(source: $datum['data'], startCell: Coordinate::stringFromColumnIndex($column).$line, strictNullComparison: true);
            $style = $worksheet->getStyle(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + 3), $line));
            $style->applyFromArray(self::STYLE_HEADER);
            $style = $worksheet->getStyle(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line + 1, Coordinate::stringFromColumnIndex($column + 3), $worksheet->getHighestRow()));
            $style->applyFromArray(self::STYLE_DATA);

            foreach (range('A', $worksheet->getHighestColumn()) as $columnId) {
                $worksheet->getColumnDimension($columnId)->setAutoSize(true);
            }
        }
    }

    private function setWorksheetForAverage(array $coefficients, array $programChannels, Spreadsheet $spreadsheet): void
    {
        // Create worksheet
        $title = sprintf('Concours AST %s', date('Y'));
        $subTitle1 = 'Moyennes par année et par épreuve';
        $startedCell = 'A1';
        $worksheet = new Worksheet();
        $spreadsheet->addSheet($worksheet);
        $worksheet->setTitle('Moyenne');

        $cell = $worksheet->getCell($startedCell);
        $line = $cell->getRow();
        $column = Coordinate::columnIndexFromString($cell->getColumn());

        // Add titles
        $worksheet->setCellValueByColumnAndRow($column, $line, $title);
        $worksheet->getCell(Coordinate::stringFromColumnIndex($column).$line)->getStyle()->applyFromArray(self::DEFAULT_STYLE);
        ++$line;
        $worksheet->setCellValueByColumnAndRow($column, $line, $subTitle1);
        $style = $worksheet->getCell(Coordinate::stringFromColumnIndex($column).$line)->getStyle()->applyFromArray(self::DEFAULT_STYLE);
        $style->getFont()->setBold(false);
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $worksheet->mergeCells(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + 4), $line));
        $line = $worksheet->getHighestRow() + 2;

        $data = $this->dataAverageToExport->generate(coefficients: $coefficients, programChannels: $programChannels);
        
        foreach ($data as $datum) {
            switch ($datum['programChannel']->getKey()) {
                case 'ast1':
                    $title = '1ère année :';
                    break;
                case 'ast2':
                    $title = '2ème année :';
                    break;
                default:
                    $title = 'x année :';
                    break;
            }
            $worksheet->setCellValueByColumnAndRow($column, $line, $title);

            list('nbOfCandidates' => $nbOfCandidates, 'data' => $arrayData) = $datum;

            $worksheet->setCellValueByColumnAndRow($column + 1, $line, sprintf('%d candidats', $nbOfCandidates));
            $worksheet->getCell(Coordinate::stringFromColumnIndex($column + 1).$line)->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $worksheet->mergeCells(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column + 1), $line, Coordinate::stringFromColumnIndex($column + 2), $line));
            ++$line;

            $worksheet->fromArray(source: $arrayData, startCell: Coordinate::stringFromColumnIndex($column).$line, strictNullComparison: true);
            $style = $worksheet->getStyle(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + 2), $line));
            $style->applyFromArray(self::STYLE_HEADER);
            $style = $worksheet->getStyle(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line + 1, Coordinate::stringFromColumnIndex($column + 2), $worksheet->getHighestRow()));
            $style->applyFromArray(self::STYLE_DATA);
            $line = $worksheet->getHighestRow() + 3;
        }

        $worksheet->setCellValueByColumnAndRow($column, $line, 'Dossier :');
        ++$line;

        $dataRecord = $this->dataRecordAverageToExport->generate(data: $data);
        $worksheet->fromArray(source: $dataRecord, startCell: Coordinate::stringFromColumnIndex($column).$line, strictNullComparison: true);
        $style = $worksheet->getStyle(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + count($dataRecord[0]) - 1), $line));
        $style->applyFromArray(self::STYLE_HEADER);
        $style = $worksheet->getStyle(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line + 1, Coordinate::stringFromColumnIndex($column + count($dataRecord[0]) - 1), $worksheet->getHighestRow()));
        $style->applyFromArray(self::STYLE_DATA);

        foreach (range('A', $worksheet->getHighestColumn()) as $columnId) {
            $worksheet->getColumnDimension($columnId)->setAutoSize(true);
        }
    }

    private function setWorksheetForNote(array $coefficients, array $programChannels, Spreadsheet $spreadsheet): void
    {
        foreach ($programChannels as $programChannel) {
            // Create worksheet
            $title = sprintf('Concours AST %s', date('Y'));
            $subTitle1 = 'Notes des présents à l\'écrit';
            $startedCell = 'A1';
            $worksheet = new Worksheet();
            $spreadsheet->addSheet($worksheet);
            $worksheet->setTitle(sprintf('Notes %s', $programChannel->getName()));

            $cell = $worksheet->getCell($startedCell);
            $line = $cell->getRow();
            $column = Coordinate::columnIndexFromString($cell->getColumn());

            // Add titles
            $worksheet->setCellValueByColumnAndRow($column, $line, $title);
            $worksheet->getCell(Coordinate::stringFromColumnIndex($column).$line)->getStyle()->applyFromArray(self::DEFAULT_STYLE);
            ++$line;
            $worksheet->setCellValueByColumnAndRow($column, $line, $subTitle1);
            $style = $worksheet->getCell(Coordinate::stringFromColumnIndex($column).$line)->getStyle()->applyFromArray(self::DEFAULT_STYLE);
            $style->getFont()->setBold(false);
            $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $worksheet->mergeCells(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + 4), $line));
            $line = $worksheet->getHighestRow() + 2;

            $data = $this->dataNoteToExport->generate(coefficients: $coefficients, programChannels: [$programChannel]);
            $worksheet->fromArray(source: $data, startCell: Coordinate::stringFromColumnIndex($column).$line, strictNullComparison: true);
            $style = $worksheet->getStyle(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + count($data[0]) - 1), $line));
            $style->applyFromArray(self::STYLE_HEADER);
            $style = $worksheet->getStyle(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line + 1, Coordinate::stringFromColumnIndex($column + count($data[0]) - 1), $worksheet->getHighestRow()));
            $style->applyFromArray(self::STYLE_DATA);

            foreach (range('A', $worksheet->getHighestColumn()) as $columnId) {
                $worksheet->getColumnDimension($columnId)->setAutoSize(true);
            }
        }

    }
}