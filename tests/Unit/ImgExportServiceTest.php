<?php

namespace Mastertek\MasterExport\Tests\Unit;

use Mastertek\MasterExport\Services\ExcelExportService;
use Mastertek\MasterExport\Services\ExporterService;
use Mastertek\MasterExport\Services\ImgExporterService;
use Mastertek\MasterExport\Services\PdfExporterService;
use Mastertek\MasterExport\Services\WordExporterService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ImgExportServiceTest extends MockeryTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_export_calls_image_download_with_correct_arguments()
    {
        // داده‌های آزمایشی
        $data = [
            ['id' => 1, 'name' => 'Test User 1'],
            ['id' => 2, 'name' => 'Test User 2'],
        ];
        $fields = [
            'id' => 'شناسه',
            'name' => 'نام کاربری'
        ];
        $filename = 'users.png';

        // ماک کردن سرویس Image
        $imgMock = Mockery::mock(ImgExporterService::class);

        // ماک کردن سایر سرویس‌ها
        $excelMock = Mockery::mock(ExcelExportService::class);
        $excelMock->shouldIgnoreMissing();

        $pdfMock = Mockery::mock(PdfExporterService::class);
        $pdfMock->shouldIgnoreMissing();

        $wordMock = Mockery::mock(WordExporterService::class);
        $wordMock->shouldIgnoreMissing();

        // تنظیم انتظار
        $imgMock->shouldReceive('export')
            ->once()
            ->withArgs([$data, $fields, $filename])
            ->andReturn('done');

        // تزریق ماک به سرویس مادر
        $exporter = new ExporterService($excelMock, $pdfMock, $wordMock, $imgMock);

        // تست
        $this->assertEquals('done', $exporter->toImg($data, $fields, $filename));
    }
}