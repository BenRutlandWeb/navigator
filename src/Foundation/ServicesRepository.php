<?php

namespace Navigator\Foundation;

use Navigator\Filesystem\Filesystem;

class ServicesRepository
{
    public function __construct(protected Filesystem $files, protected string $path, protected array $scanDirs = [])
    {
        //
    }

    public function getPath(): string
    {
        return $this->path;
    }

    protected function makeDirectory(string $path): string
    {
        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0777, true, true);
        }

        return $path;
    }

    protected function scanForClasses(): array
    {
        $classes = [];

        foreach ($this->scanDirs as $namespace => $scanDir) {
            foreach (glob($scanDir . '/*.php') as $file) {
                $className = $namespace . basename($file, '.php');

                if (class_exists($className)) {
                    $classes[] = $className;
                }
            }
        }

        return $classes;
    }

    public function all(): array
    {
        if (!$this->files->exists($this->path)) {
            $this->makeDirectory($this->path);
            $this->write($this->scanForClasses());
        }

        return $this->files->exists($this->path)
            ? $this->files->getRequire($this->path)
            : [];
    }

    public function get(string $parentClass): array
    {
        $return = [];

        foreach ($this->all() as $class) {
            if (class_exists($class) && is_subclass_of($class, $parentClass)) {
                $return[] = $class;
            }

            if (!class_exists($class)) {
                $this->remove($class);
            }
        }

        return $return;
    }

    public function add(string $class): void
    {
        $manifest = $this->all();

        if (!in_array($class, $manifest, true)) {
            $manifest[] = $class;

            $this->write($manifest);
        }
    }

    public function remove(string $class): void
    {
        $manifest = array_values(array_filter($this->all(), fn($c) => $c !== $class));

        $this->write($manifest);
    }

    protected function write(array $classes): void
    {
        $this->files->put(
            $this->path,
            "<?php\nreturn " . var_export($classes, true) . ";\n"
        );
    }
}
