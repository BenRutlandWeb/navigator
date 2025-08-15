<?php

namespace Navigator\WordPress\Console\Commands;

use Navigator\Console\Command;

class MediaLibraryClean extends Command
{
    protected string $signature = 'media-library:clean';

    protected string $description = 'Clean deprecated conversions and files.';

    protected function handle(): void
    {
        $this->callSilently('media regenerate --yes --delete-unknown');

        $this->success("Deleted deprecated image sizes");
    }
}
