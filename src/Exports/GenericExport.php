<?php

namespace Mastertek\MasterExport\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GenericExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $data;
    protected array $fields;
    protected string $filename;
    protected array $options;

    public function __construct(array $data, array $fields, string $filename = 'export', array $options = [])
    {
        $this->data = $data;
        $this->fields = $fields;
        $this->filename = $filename;
        $this->options = $options;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return array_values($this->fields);
    }

    public function map($row): array
    {
        $mapped = [];
        $keys = array_keys($this->fields);

        foreach ($keys as $key) {
            $value = data_get($row, $key, '-');

            // فرمت تاریخ شمسی
            if ($this->isDateField($key) && $value && $value !== '-') {
                $value = $this->formatPersianDate($value);
            }

            // فرمت Boolean
            if ($this->isBoolField($key)) {
                $value = $this->formatBool($value);
            }

            // فرمت اعداد
            if ($this->isNumberField($key) && is_numeric($value)) {
                $value = number_format($value);
            }

            // فرمت آرایه‌ها (مثل Roles)
            if (is_array($value)) {
                $value = implode('، ', $value);
            }

            $mapped[] = $value;
        }

        return $mapped;
    }

    public function styles(Worksheet $sheet): array
    {
        $totalColumns = count($this->fields);
        $lastColumn = $this->getColumnLetter($totalColumns);

        // تنظیم جهت صفحه به راست به چپ
        $sheet->setRightToLeft(true);

        return [
            // استایل هدر
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => $this->options['header_font_size'] ?? 12,
                    'name' => 'Tahoma',
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'rgb' => $this->options['header_bg_color'] ?? 'E8E8E8',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ],
            // استایل بدنه
            'A2:' . $lastColumn . '10000' => [
                'font' => [
                    'name' => 'Tahoma',
                    'size' => $this->options['body_font_size'] ?? 11,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'DDDDDD'],
                    ],
                ],
            ],
        ];
    }

    public function filename(): string
    {
        return $this->filename . '.xlsx';
    }

    /**
     * فرمت تاریخ شمسی
     */
    protected function formatPersianDate($value): string
    {
        // بررسی فرمت ورودی: 1405-01-11 یا 1405-01-11 12:53:43
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})(?:\s+(\d{2}):(\d{2}):(\d{2}))?$/', $value, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];
            
            // تبدیل به فرمت شمسی
            $persianMonths = [
                '01' => 'فروردین', '02' => 'اردیبهشت', '03' => 'خرداد',
                '04' => 'تیر', '05' => 'مرداد', '06' => 'شهریور',
                '07' => 'مهر', '08' => 'آبان', '09' => 'آذر',
                '10' => 'دی', '11' => 'بهمن', '12' => 'اسفند',
            ];

            $monthName = $persianMonths[$month] ?? $month;
            
            if (isset($matches[4])) {
                $time = sprintf("%02d:%02d", $matches[4], $matches[5]);
                return "$day $monthName $year - $time";
            }
            
            return "$day $monthName $year";
        }

        return $value;
    }

    /**
     * فرمت Boolean
     */
    protected function formatBool($value): string
    {
        if (is_bool($value)) {
            return $value ? 'بله' : 'خیر';
        }
        if (in_array(strtolower($value), ['true', '1', 'yes'])) {
            return 'بله';
        }
        if (in_array(strtolower($value), ['false', '0', 'no'])) {
            return 'خیر';
        }
        return $value;
    }

    protected function isDateField(string $key): bool
    {
        $dateFields = $this->options['date_fields'] ?? [
            'created_at', 'updated_at', 'date', 'CreatedAt', 'UpdatedAt'
        ];
        return in_array($key, $dateFields);
    }

    protected function isNumberField(string $key): bool
    {
        $numberFields = $this->options['number_fields'] ?? [];
        return in_array($key, $numberFields);
    }

    protected function isBoolField(string $key): bool
    {
        $boolFields = $this->options['bool_fields'] ?? [
            'is_validated', 'is_legal', 'IsValidated', 'IsLegal', 'status', 'active'
        ];
        return in_array($key, $boolFields);
    }

    protected function getColumnLetter(int $column): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
    }
}