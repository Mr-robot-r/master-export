<?php

namespace Mastertek\MasterExport\Tests\Unit;

use Mastertek\MasterExport\Services\ExcelExportService;
use Mastertek\MasterExport\Services\ExporterService;
use Mastertek\MasterExport\Services\ImgExporterService;
use Mastertek\MasterExport\Services\PdfExporterService;
use Mastertek\MasterExport\Services\WordExporterService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

// اگر از Mockery استفاده می‌کنید، بهتر است از MockeryTestCase ارث‌بری کنید
// یا در متد tearDown آن را پاکسازی کنید.
class PdfExportServiceTest extends MockeryTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_export_calls_pdf_download_with_correct_arguments()
    {
        // Test data
        $data = [['id' => 1, 'name' => 'Test User 1']];
        $fields = ['id' => 'شناسه', 'name' => 'نام کاربری'];
        $filename = 'users';

        // Create mocks
        $pdfMock = Mockery::mock(PdfExporterService::class);

        // Create a dummy mock for Excel (no expectations needed)
        $excelMock = Mockery::mock(ExcelExportService::class);
        $excelMock->shouldIgnoreMissing();  // Ignore any calls to Excel


        // Create a dummy mock for word (no expectations needed)
        $wordMock = Mockery::mock(WordExporterService::class);
        $wordMock->shouldIgnoreMissing();


        // Create a dummy mock for img (no expectations needed)
        $imgMock = Mockery::mock(ImgExporterService::class);
        $imgMock->shouldIgnoreMissing();

        // Set expectation for PDF
        $pdfMock->shouldReceive('export')
            ->once()
            ->with($data, $fields, $filename)
            ->andReturn('done');

        // Inject both dependencies
        $exporter = new ExporterService($excelMock, $pdfMock, $wordMock, $imgMock);

        // Test
        $this->assertEquals('done', $exporter->toPdf($data, $fields, $filename));
    }
}