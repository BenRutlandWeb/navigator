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

        $copied = [];

        foreach ($this->files($tag) as $from => $to) {
            if (!$file->exists($to) || $this->option('force')) {
                $file->ensureDirectoryExists(dirname($to));

                if ($file->isDirectory($from)) {
                    $file->copyDirectory($from, $to);
                } else {
                    $file->copy($from, $to);
                }
                $copied[] = $to;
            } else {
                break;
            }
        }

        if (!empty($copied)) {
            $this->success("Copied {$tag}")->newLine();
            foreach ($copied as $path) {
                $this->line($path);
            }

            $this->callSilently('navigator clear-compiled');
        } else {
            $this->error("Files already exist. Use --force to overwrite the files.");
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
