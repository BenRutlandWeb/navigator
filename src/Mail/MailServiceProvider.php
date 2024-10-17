<?php

namespace Navigator\Mail;

use Navigator\Events\Dispatcher;
use Navigator\Foundation\Application;
use Navigator\Foundation\ServiceProvider;
use Navigator\Mail\Console\Commands\MakeMail;
use Navigator\View\View;
use Navigator\View\ViewFactory;
use Navigator\WordPress\WordPressFactory;

class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Mailer::class, function (Application $app) {
            return new Mailer($app->get(Dispatcher::class));
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakeMail::class,
        ]);

        $view = $this->app->get(ViewFactory::class);
        $factory = $this->app->get(WordPressFactory::class);

        $this->app->get(Dispatcher::class)
            ->listen('navigator_mailer_content', function (string $content) use ($view): string {
                return $view->file(
                    __DIR__ . '/resources/views/layout.php',
                    ['slot' => do_shortcode($content)]
                );
            });

        $this->registerShortcodes($factory, $view);
    }

    public function registerShortcodes(WordPressFactory $factory, ViewFactory $view): void
    {
        $factory->registerShortcode('button', function (array $attributes = [], string $slot = '') use ($view): View {
            return $view->file(__DIR__ . '/resources/views/button.php', [
                'url'   => $attributes['url'],
                'color' => $attributes['color'] ?? 'primary',
                'slot'  => $slot,
            ]);
        });

        $factory->registerShortcode('panel', function (array $attributes = [], string $slot = '') use ($view): View {
            return $view->file(__DIR__ . '/resources/views/panel.php', compact('slot'));
        });

        $factory->registerShortcode('subcopy', function (array $attributes = [], string $slot = '') use ($view): View {
            return $view->file(__DIR__ . '/resources/views/subcopy.php', compact('slot'));
        });

        $factory->registerShortcode('table', function (array $attributes = [], string $slot = '') use ($view): View {
            return $view->file(__DIR__ . '/resources/views/table.php', compact('slot'));
        });
    }
}
