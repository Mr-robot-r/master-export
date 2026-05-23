<?php

namespace Mastertek\MasterExport\Tests\Unit;

use Mastertek\MasterExport\Services\ExcelExportService;
use Mastertek\MasterExport\Services\ExporterService;
use Mastertek\MasterExport\Services\PdfExporterService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

// اگر از Mockery استفاده می‌کنید، بهتر است از MockeryTestCase ارث‌بری کنید
// یا در متد tearDown آن را پاکسازی کنید.
class ExcelExportServiceTest extends MockeryTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_export_calls_excel_download_with_correct_arguments()
    {
        // ۱. داده‌های آزمایشی
        $data = [
            ['id' => 1, 'name' => 'Test User 1'],
            ['id' => 2, 'name' => 'Test User 2'],
        ];
        $fields = [
            'id' => 'شناسه',
            'name' => 'نام کاربری'
        ];
        $filename = 'users.xlsx';
        $options = ['writerType' => 'Xlsx'];

        // ۱. ماک کردن سرویس فرزند
        $excelMock = Mockery::mock(ExcelExportService::class);


        // Create a dummy mock for pdf (no expectations needed)
        $pdfMock = Mockery::mock(PdfExporterService::class);
        $pdfMock->shouldIgnoreMissing();


        // ۲. تنظیم انتظار
        $excelMock->shouldReceive('export')
            ->once()
            ->withArgs([$data, $fields, $filename])
            ->andReturn('done');

        // ۳. تزریق ماک به سرویس مادر
        $exporter = new ExporterService($excelMock, $pdfMock);

        // ۴. تست
        $this->assertEquals('done', $exporter->toExcel($data, $fields, $filename));
    }
}