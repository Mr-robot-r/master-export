<?php
// packages/YourVendor/DataExporter/src/Facades/DataExporter.php
namespace Mastertek\MasterExport\Facades;

use Illuminate\Support\Facades\Facade;

class DataExporter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'data-exporter'; // این کلید باید در Service Provider ثبت بشه
    }
}