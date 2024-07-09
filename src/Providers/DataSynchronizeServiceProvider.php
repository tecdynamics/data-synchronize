<?php

namespace Tec\DataSynchronize\Providers;

use Tec\Base\Facades\DashboardMenu;
use Tec\Base\Facades\PanelSectionManager as PanelSectionManagerFacade;
use Tec\Base\Supports\ServiceProvider;
use Tec\Base\Traits\LoadAndPublishDataTrait;
use Tec\DataSynchronize\Commands\ClearChunksCommand;
use Tec\DataSynchronize\Commands\ExportControllerMakeCommand;
use Tec\DataSynchronize\Commands\ExporterMakeCommand;
use Tec\DataSynchronize\PanelSections\ExportPanelSection;
use Tec\DataSynchronize\PanelSections\ImportPanelSection;
use Illuminate\Console\Scheduling\Schedule;

class DataSynchronizeServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('packages/data-synchronize')
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishConfigurations(['data-synchronize'])
            ->loadAndPublishViews()
            ->publishAssets()
            ->registerPanelSection()
            ->registerDashboardMenu();

        if ($this->app->runningInConsole()) {
            $this->commands([
                ExporterMakeCommand::class,
                ExportControllerMakeCommand::class,
                ClearChunksCommand::class,
            ]);

            $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
                $schedule
                    ->command(ClearChunksCommand::class)
                    ->dailyAt('00:00');
            });
        }
    }

    protected function getPath(?string $path = null): string
    {
        return __DIR__ . '/../..' . ($path ? '/' . ltrim($path, '/') : '');
    }

    protected function registerPanelSection(): self
    {
        PanelSectionManagerFacade::group('data-synchronize')->beforeRendering(function () {
            PanelSectionManagerFacade::default()
                ->register(ExportPanelSection::class)
                ->register(ImportPanelSection::class);
        });

        return $this;
    }

    protected function registerDashboardMenu(): self
    {
        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-packages-data-synchronize',
                    'parent_id' => 'cms-core-tools',
                    'priority' => 9000,
                    'name' => 'packages/data-synchronize::data-synchronize.tools.export_import_data',
                    'icon' => 'ti ti-package-import',
                    'route' => 'tools.data-synchronize',
                ]);
        });

        return $this;
    }
}
