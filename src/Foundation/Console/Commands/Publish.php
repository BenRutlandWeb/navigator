<?php

namespace Navigator\Foundation\Console\Commands;

use Navigator\Console\Command;
use Navigator\Filesystem\Filesystem;

class Publish extends Command
{
    protected string $signature = 'publish {tag : The tag to publish.}
                                      {--force : Overwrite the files if they exist}';

    protected string $description = 'Publish package resources.';

    protected function handle(): void
    {
        $tag = $this->argument('tag');

        $file = $this->app->get(Filesystem::class);

        foreach ($this->files($tag) as $from => $to) {
            if (!$file->exists($to) || $this->option('force')) {
                $file->ensureDirectoryExists(dirname($to));

                $file->copy($from, $to);

                $this->line(sprintf(
                    '<info>Copied %s</info> <comment>[%s]</comment> <info>To</info> <comment>[%s]</comment>',
                    $tag,
                    str_replace($this->app->basePath, '', realpath($from)),
                    str_replace($this->app->basePath, '', realpath($to))
                ));
            }
        }
    }

    /** @return array<string, string> */
    public function files(string $tag): array
    {
        $files = [];

        foreach ($this->app->getProviders() as $provider) {
            foreach ($provider::getPublishables($tag) as $from => $to) {
                $files[$from] = $to;
            }
        }

        return $files;
    }
}
