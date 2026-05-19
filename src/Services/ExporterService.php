<?php


namespace Mastertek\MasterExport\Services;

class ExporterService
{
    protected $excelExporter;
    protected $pdfExporter;
    // ...

    public function __construct(
        ExcelExportService $excelExporter
        ,
        PdfExporterService $pdfExporter  /* ... */
    ) {
        $this->excelExporter = $excelExporter;
        $this->pdfExporter = $pdfExporter;
        // ...
    }

    public function toExcel($data, $fields, $fileName)
    {
        return $this->excelExporter->export($data, $fields, $fileName);
    }

    public function toPdf($data, $fields, $fileName)
    {
        return $this->pdfExporter->export($data, $fields, $fileName);
    }
    // ... متدهای دیگر برای Word, Image, TXT
}