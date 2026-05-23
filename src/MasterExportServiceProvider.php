<?php

namespace Mastertek\MasterExport;

use Mastertek\MasterExport\Services\ExcelExportService;
use Mastertek\MasterExport\Services\ExporterService;
use Mastertek\MasterExport\Services\ImgExporterService;
use Mastertek\MasterExport\Services\PdfExporterService;
use Mastertek\MasterExport\Services\WordExporterService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MasterExportServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('mastertek/master-export')
            ->hasConfigFile();

        // REMOVED: ->publishesServiceProvider('MasterExportServiceProvider');
        // This method does not exist. If you need to publish a provider, 
        // it is usually done by the user manually or via a different mechanism.
        // Most packages do not publish their own provider file; they register it automatically.
    }

    public function register(): void
    {
        // CRITICAL: You MUST call parent::register() first.
        // This initializes $this->package and sets up the package context.
        parent::register();

        // Now you can safely bind your services
        $this->app->bind('data-exporter', function ($app) {
            return new ExporterService(
                $app->make(ExcelExportService::class),
                $app->make(PdfExporterService::class),
                $app->make(WordExporterService::class),
                $app->make(ImgExporterService::class),
            );
        });
    }

    public function boot(): void
    {
        // CRITICAL: You MUST call parent::boot() first.
        parent::boot();

        // Add any boot logic here if needed
    }
}