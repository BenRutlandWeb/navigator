<?php

namespace Navigator\Session\Handlers;

use Carbon\Carbon;
use Navigator\Collections\Collection;
use Navigator\Filesystem\Filesystem;
use SessionHandlerInterface;

class FileSessionHandler implements SessionHandlerInterface
{
    public function __construct(protected Filesystem $files, protected string $path, protected int $minutes)
    {
        $this->path = $this->makeDirectory($path);
    }

    protected function makeDirectory(string $path): string
    {
        $directory = $path;

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0777, true, true);
        }

        return $path;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        if (
            $this->files->isFile($path = $this->path . '/' . $id) &&
            $this->files->lastModified($path) >= Carbon::now()->subMinutes($this->minutes)->getTimestamp()
        ) {
            return $this->files->sharedGet($path);
        }

        return '';
    }

    public function write(string $id, string $data): bool
    {
        $this->files->put($this->path . '/' . $id, $data, true);

        return true;
    }

    public function destroy(string $id): bool
    {
        $this->files->delete($this->path . '/' . $id);

        return true;
    }

    public function gc(int $lifetime): int
    {
        $deleted = 0;

        Collection::make($this->files->glob($this->path . '/*'))
            ->each(function ($path) use ($deleted, $lifetime) {
                if ($this->files->lastModified($path) < time() - $lifetime) {
                    $this->files->delete($path);
                    $deleted++;
                }
            });

        return $deleted;
    }
}
