<?php

declare(strict_types=1);

namespace App\Service\Export;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class ExcelGenerator
{
    public function __construct(
        private PageToWorkSheet $pageToWorkSheet,
        private LoggerInterface $exportLogger
    ) {
    }

    public function generate(?string $fileName = null, array $pages = [], ?string $path = null): string
    {
        if (null === $fileName) {
            $fileName = uniqid();
        }

        // Remove the default page
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(
            $spreadsheet->getIndex(
                $spreadsheet->getSheetByName('Worksheet')
            )
        );

        foreach ($pages as $page) {
            $this->pageToWorkSheet->transform($page, $spreadsheet);
        }

        $writer = new Xlsx($spreadsheet);

        $dir = (null !== $path)? $path : sys_get_temp_dir();
        $filesystem = new Filesystem();
        $tempFile = $filesystem->tempnam($dir, $fileName, '.xlsx');

        $this->exportLogger->debug("File available in {$tempFile}");

        if (!file_exists($tempFile)) {
            throw new FileNotFoundException('The excel file is not found');
        }

        // Create the excel file in the tmp directory of the system
        $writer->save($tempFile);

        return $tempFile;
    }
}
