<?php

namespace Navigator\Acf;

use Navigator\Acf\Console\Commands\MakeBlock;
use Navigator\Acf\Console\Commands\MakeFieldGroup;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Foundation\ServicesRepository;
use Navigator\View\ViewFactory;

class AcfServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AcfFactory::class, function (Application $app) {
            return new AcfFactory(
                $app,
                $app->get(ViewFactory::class),
                $app->path('resources/blocks')
            );
        });
    }

    public function boot(): void
    {
        $factory = $this->app->get(AcfFactory::class);
        $manifest = $this->app->get(ServicesRepository::class);

        foreach ($manifest->get(Block::class) as $block) {
            $factory->registerBlock($block);
        }

        foreach ($manifest->get(FieldGroup::class) as $fieldGroup) {
            $factory->registerFieldGroup($fieldGroup);
        }

        $this->commands([
            MakeBlock::class,
            MakeFieldGroup::class,
        ]);
    }
}
