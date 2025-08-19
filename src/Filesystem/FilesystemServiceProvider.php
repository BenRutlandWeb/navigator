<?php

namespace Navigator\Filesystem;

use Navigator\Foundation\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Filesystem::class, function () {

            if (!function_exists('WP_Filesystem')) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
            }

            WP_Filesystem();

            global $wp_filesystem;

            return new Filesystem($wp_filesystem);
        });
    }

    public function boot(): void
    {
        //
    }
}
