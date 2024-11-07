<?php

namespace Navigator\Acf;

use Navigator\Acf\Console\Commands\MakeBlock;
use Navigator\Acf\Console\Commands\MakeFieldGroup;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\View\ViewFactory;

class AcfServiceProvider extends ServiceProvider
{
    /** @var array<int, class-string<Block>> */
    protected array $blocks = [];

    /** @var array<int, class-string<FieldGroup>> */
    protected array $fieldGroups = [];


    public function register(): void
    {
        $this->app->singleton(AcfFactory::class, function (Application $app) {
            return new AcfFactory(
                $app->get(ViewFactory::class),
                $app->path('resources/blocks')
            );
        });
    }

    public function boot(): void
    {
        $factory = $this->app->get(AcfFactory::class);

        foreach ($this->blocks as $block) {
            $factory->registerBlock($block);
        }

        foreach ($this->fieldGroups as $fieldGroup) {
            $factory->registerFieldGroup($fieldGroup);
        }

        $this->commands([
            MakeBlock::class,
            MakeFieldGroup::class,
        ]);
    }
}
