<?php
// src/Services/ImageExportService.php

namespace Mastertek\MasterExport\Services;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class ImgExporterService
{
    protected $imageManager;

    public function __construct()
    {
        //gd
        $this->imageManager = new ImageManager(['driver' => 'gd']);
    }

    /**
     * Export data to Image (as table screenshot)
     */
    public function export(array $data, array $fields, string $fileName): string
    {
        // Calculate dimensions
        $colCount = count($fields);
        $rowCount = count($data) + 1; // +1 for header

        $cellWidth = 200;
        $cellHeight = 30;
        $width = $colCount * $cellWidth;
        $height = $rowCount * $cellHeight;

        // Create image
        $image = $this->imageManager->canvas($width, $height, '#ffffff');

        // Draw header
        $x = 0;
        foreach ($fields as $field) {
            $image->rectangle($x, 0, $x + $cellWidth, $cellHeight, function ($draw) {
                $draw->background('#4CAF50');
                $draw->border(1, '#000000');
            });
            $image->text($field, $x + 10, 10, function ($font) {
                $font->color('#ffffff');
                $font->size(14);
                $font->align('left');
                $font->valign('top');
            });
            $x += $cellWidth;
        }

        // Draw data rows
        $y = $cellHeight;
        foreach ($data as $row) {
            $x = 0;
            foreach (array_keys($fields) as $field) {
                $value = $this->getNestedValue($row, $field);
                $image->rectangle($x, $y, $x + $cellWidth, $y + $cellHeight, function ($draw) {
                    $draw->border(1, '#000000');
                });
                $image->text((string) $value, $x + 10, $y + 10, function ($font) {
                    $font->size(12);
                    $font->align('left');
                    $font->valign('top');
                });
                $x += $cellWidth;
            }
            $y += $cellHeight;
        }

        // Save image
        $tempPath = storage_path("app/temp/{$fileName}.png");
        $this->ensureDirectoryExists(dirname($tempPath));
        $image->save($tempPath);

        return $tempPath;
    }

    /**
     * Export as chart image (bar chart)
     */
    public function exportAsChart(array $data, string $xField, string $yField, string $fileName): string
    {
        // Simple chart implementation
        $width = 800;
        $height = 600;

        $image = $this->imageManager->canvas($width, $height, '#ffffff');

        // Draw axes
        $image->line(50, 50, 50, $height - 50, function ($draw) {
            $draw->color('#000000');
        });
        $image->line(50, $height - 50, $width - 50, $height - 50, function ($draw) {
            $draw->color('#000000');
        });

        // Find max value for scaling
        $maxValue = max(array_column($data, $yField));
        $scale = ($height - 100) / $maxValue;

        // Draw bars
        $barWidth = ($width - 100) / count($data);
        $x = 60;

        foreach ($data as $index => $item) {
            $barHeight = $item[$yField] * $scale;
            $image->rectangle($x, $height - 50 - $barHeight, $x + $barWidth - 5, $height - 50, function ($draw) use ($index) {
                $draw->background(['rgb' => [100, 150, 200 + ($index * 10)]]);
                $draw->border(1, '#000000');
            });

            // Add label
            $image->text($item[$xField], $x + 5, $height - 40, function ($font) {
                $font->size(10);
                $font->align('center');
            });

            $x += $barWidth;
        }

        $tempPath = storage_path("app/temp/{$fileName}.png");
        $this->ensureDirectoryExists(dirname($tempPath));
        $image->save($tempPath);

        return $tempPath;
    }

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

    protected function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}