<?php

declare(strict_types=1);

namespace App\Service\Export;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PageToWorkSheet
{
    private const TITLE_CELL = 'A1';
    private const HEADER_CELL = 'A2';
    private const CONTENT_CELL = 'A3';

    private const MAX_LENGTH = 31;

    public function transform(PageModel $page, Spreadsheet $spreadsheet): Worksheet
    {
        $worksheet = new Worksheet();
        $spreadsheet->addSheet($worksheet);

        $worksheet->setTitle(substr(strval($page->getName()), 0, self::MAX_LENGTH));
        $worksheet->getDefaultRowDimension()->setRowHeight($page->getRowHeight());
        $worksheet->getDefaultColumnDimension()->setAutoSize(true);

        $this->setTitle($worksheet, $page);

        $this->writeRow($worksheet, $page->getHeaders(), $worksheet->getCell(self::HEADER_CELL), bold: true);
        $this->writeRows($worksheet, $page->getRows(), $worksheet->getCell(self::CONTENT_CELL));

        return $worksheet;
    }

    private function setTitle(Worksheet $worksheet, PageModel $page): void
    {
        $cell = $worksheet->getCell(self::TITLE_CELL);
        $line = $cell->getRow();
        $column = Coordinate::columnIndexFromString($cell->getColumn());
        $worksheet->mergeCells(sprintf('%s%d:%s%d', Coordinate::stringFromColumnIndex($column), $line, Coordinate::stringFromColumnIndex($column + $page->getWidth() -1), $line));

        $style = $worksheet->getStyle(self::TITLE_CELL);
        $this->setBorder($style);
        $style->getFont()->setSize('18')->setName('Calibri')->setBold(true);
        $style->getAlignment()->setHorizontal('center');
        $worksheet->getColumnDimension($cell->getColumn())->setAutoSize(true);

        $worksheet->setCellValue(self::TITLE_CELL, $page->getTitle());
    }

    private function writeRows(WorkSheet $worksheet, array $rows, Cell $cell): void
    {
        $line = $cell->getRow();
        $column = $cell->getColumn();

        foreach ($rows as $row) {
            $this->writeRow($worksheet, $row, $worksheet->getCell($column.$line));
            $line++;
        }
    }

    /**
     * @SuppressWarnings(PHPMD).BooleanArgumentFlag
     */
    private function writeRow(WorkSheet $worksheet, array $row, Cell $cell, string $font = 'Calibri', bool $bold = false, string $alignment = 'left', int $size = 18): void
    {
        $line = $cell->getRow();
        $column = Coordinate::columnIndexFromString($cell->getColumn());

        foreach ($row as $item) {
            $worksheet->setCellValueByColumnAndRow($column, $line, $item);
            $style = $worksheet->getCell(Coordinate::stringFromColumnIndex($column).$line)->getStyle();
            $style->getFont()->setSize($size)->setName($font)->setBold($bold);
            $style->getAlignment()->setHorizontal($alignment);
            $this->setBorder($style);
            $worksheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setAutoSize(true);
            $column++;
        }
    }

    private function setBorder(Style $style): void
    {
        $style
            ->getBorders()
            ->getOutline()
            ->setBorderStyle(Border::BORDER_THIN)
            ->setColor(new Color('FF000000'))
        ;
    }
}
