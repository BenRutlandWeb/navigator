<?php

namespace Navigator\Acf;

use Navigator\Acf\Console\Commands\MakeBlock;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\View\ViewFactory;

class AcfServiceProvider extends ServiceProvider
{
    /**
     * @var array<int, class-string<Block>>
     */
    protected array $blocks = [];

    /**
     * @var class-string<SubscriberInterface>[]
     */
    protected array $fieldGroups = [];


    public function register(): void
    {
        $this->app->singleton(BlockManager::class, function (Application $app) {
            return new BlockManager(
                $app->get(ViewFactory::class),
                $app->path('resources/blocks')
            );
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakeBlock::class,
        ]);

        $blockManager = $this->app->get(BlockManager::class);

        foreach ($this->blocks as $block) {
            $blockManager->registerBlock($block);
        }
    }
}
