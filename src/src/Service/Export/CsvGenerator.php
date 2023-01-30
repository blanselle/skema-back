<?php

namespace App\Service\Export;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class CsvGenerator
{
    private const SEPARATOR = ';';

    public function __construct(
        #[Autowire('%export_private_path%')]
        private string $path,
        private LoggerInterface $exportLogger
    ) {}

    public function generate(string $filename, array $header, array $list): string
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists(files: $this->path)) {
            $filesystem->mkdir($this->path, 0775);
        }
        $file = $filesystem->tempnam(dir: $this->path, prefix: $filename, suffix: '.csv');

        $this->exportLogger->info("Start generate file {$file}");

        $fp = fopen($file, 'w');
        if (false === $fp) {
            throw new FileNotFoundException("Cannot open file {$file}");
        }

        if (false === fputcsv($fp, $header, self::SEPARATOR)) {
            $this->exportLogger->error("Cannot write header on file {$file}");
        }

        foreach ($list as $fields) {
            if (false === fputcsv($fp, $fields, self::SEPARATOR)) {
                $this->exportLogger->error("Cannot write fields for student {$fields['student_identifier']} on file {$file}");
            }
        }

        fclose($fp);

        return $file;
    }
}