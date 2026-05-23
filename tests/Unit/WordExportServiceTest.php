<?php

namespace Mastertek\MasterExport\Tests\Unit;

use Mastertek\MasterExport\Services\ExcelExportService;
use Mastertek\MasterExport\Services\ExporterService;
use Mastertek\MasterExport\Services\ImgExporterService;
use Mastertek\MasterExport\Services\PdfExporterService;
use Mastertek\MasterExport\Services\WordExporterService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class WordExportServiceTest extends MockeryTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_export_calls_word_download_with_correct_arguments()
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
        $filename = 'users.docx';

        // ماک کردن سرویس Word
        $wordMock = Mockery::mock(WordExporterService::class);

        // ماک کردن سایر سرویس‌ها
        $excelMock = Mockery::mock(ExcelExportService::class);
        $excelMock->shouldIgnoreMissing();

        $pdfMock = Mockery::mock(PdfExporterService::class);
        $pdfMock->shouldIgnoreMissing();

        $imgMock = Mockery::mock(ImgExporterService::class);
        $imgMock->shouldIgnoreMissing();

        // تنظیم انتظار
        $wordMock->shouldReceive('export')
            ->once()
            ->withArgs([$data, $fields, $filename])
            ->andReturn('done');

        // تزریق ماک به سرویس مادر
        $exporter = new ExporterService($excelMock, $pdfMock, $wordMock, $imgMock);

        // تست
        $this->assertEquals('done', $exporter->toWord($data, $fields, $filename));
    }
}