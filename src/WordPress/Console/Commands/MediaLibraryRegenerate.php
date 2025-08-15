<?php

namespace Navigator\WordPress\Console\Commands;

use Navigator\Console\Command;

class MediaLibraryRegenerate extends Command
{
    protected string $signature = 'media-library:regenerate';

    protected string $description = 'Regenerate the derived images of media.';

    protected function handle(): void
    {
        $this->callSilently('media regenerate --yes');

        $this->success("Images regenerated");
    }
}
