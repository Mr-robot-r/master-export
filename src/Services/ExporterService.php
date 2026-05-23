<?php


namespace Mastertek\MasterExport\Services;

class ExporterService
{
    protected $excelExporter;
    protected $pdfExporter;
    protected $wordExporter;
    protected $imgExporter;
    // ...

    public function __construct(
        ExcelExportService $excelExporter,
        PdfExporterService $pdfExporter,  /* ... */
        WordExporterService $wordExporter,  /* ... */
        ImgExporterService $imgExporter,  /* ... */
    ) {
        $this->excelExporter = $excelExporter;
        $this->pdfExporter = $pdfExporter;
        $this->wordExporter = $wordExporter;
        $this->imgExporter = $imgExporter;
    }

    public function toExcel($data, $fields, $fileName)
    {
        return $this->excelExporter->export($data, $fields, $fileName);
    }

    public function toPdf($data, $fields, $fileName)
    {
        return $this->pdfExporter->export($data, $fields, $fileName);
    }
     public function toImg($data, $fields, $fileName)
    {
        return $this->imgExporter->export($data, $fields, $fileName);
    }
     public function toWord($data, $fields, $fileName)
    {
        return $this->wordExporter->export($data, $fields, $fileName);
    }
    // ... متدهای دیگر برای Word, Image, TXT
}