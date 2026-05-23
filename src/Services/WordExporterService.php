<?php

namespace Mastertek\MasterExport\Services;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;

class WordExporterService
{
    /**
     * Export data to Word document
     */
    public function export(array $data, array $fields, string $fileName): string
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Add title
        $section->addTitle($fileName, 1);

        // Create table
        $table = $section->addTable();

        // Add header row
        $headerRow = $table->addRow();
        foreach ($fields as $field) {
            $headerRow->addCell(2000)->addText($field, ['bold' => true]);
        }

        // Add data rows
        foreach ($data as $row) {
            $tableRow = $table->addRow();
            foreach (array_keys($fields) as $field) {
                $value = $this->getNestedValue($row, $field);
                $tableRow->addCell(2000)->addText((string) $value);
            }
        }

        // Save file
        $tempPath = storage_path("app/temp/{$fileName}.docx");
        $this->ensureDirectoryExists(dirname($tempPath));

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempPath);

        return $tempPath;
    }

    /**
     * Get nested array value using dot notation
     */
    protected function getNestedValue(array $data, string $key): mixed
    {
        if (str_contains($key, '.')) {
            $keys = explode('.', $key);
            $value = $data;
            foreach ($keys as $k) {
                if (!isset($value[$k])) {
                    return null;
                }
                $value = $value[$k];
            }
            return $value;
        }

        return $data[$key] ?? null;
    }

    /**
     * Ensure directory exists
     */
    protected function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}