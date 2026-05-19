<?php

namespace Mastertek\MasterExport\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfExporterService
{

    public function __construct()
    {
    }

    public function export(array $data, array $fields, string $filename = 'export', array $options = [])
    {


        // ساخت PDF
        $pdf = PDF::loadView('pdf.exportTablePdf', [
            'columns' => $fields,
            'rows' => $data,
        ]);

        return $pdf->download($filename . '.pdf');
    }
}