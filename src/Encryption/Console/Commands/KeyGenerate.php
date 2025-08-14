<?php

namespace Navigator\Encryption\Console\Commands;

use Navigator\Console\Command;
use Navigator\Encryption\Encrypter;
use Navigator\Str\Str;

class KeyGenerate extends Command
{
    protected string $signature = 'key:generate';

    protected string $description = 'Generate an encryption key.';

    protected function handle(): void
    {
        $this->header('Navigator', 'Generate an encryption key');

        $key = Str::of(
            Encrypter::generateKey($this->app->config('app.cipher', 'aes-256-cbc'))
        )->toBase64()->prepend('base64:');

        if ($this->callSilently('config set APP_KEY ' . $key)) {
            $this->success('Generated key');
            return;
        };

        $this->error('Failed to generate key');
    }
}
