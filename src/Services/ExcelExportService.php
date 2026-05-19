<?php

namespace Mastertek\MasterExport\Services;

use Maatwebsite\Excel\Excel;
use Mastertek\MasterExport\Exports\GenericExport;

class ExcelExportService
{
    protected Excel $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function export(array $data, array $fields, string $filename = 'export', array $options = [])
    {
        $export = new GenericExport($data, $fields, $filename, $options);
        
        return $this->excel->download($export, $export->filename());
    }
}