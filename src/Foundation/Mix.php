<?php

namespace Navigator\Foundation;

use Navigator\Str\Str;

class Mix
{
    public function __construct(protected string $assetPath)
    {
        //
    }

    public function path(string $path): string
    {
        $file = Str::trim(ABSPATH, '/') . wp_make_link_relative($this->assetPath . 'mix-manifest.json');

        if (!file_exists($file)) {
            return $path;
        }

        $key = Str::start($path, '/');

        $manifest = wp_json_file_decode($file, ['associative' => true]);

        return Str::trim($this->assetPath, '/') . ($manifest[$key] ?? $key);
    }
}
