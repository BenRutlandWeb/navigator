<?php

namespace Navigator\Acf;

use Navigator\Acf\Models\Concerns\HasAcfFields;
use Navigator\Collections\Collection;

use function Navigator\collect;

abstract class Block implements BlockInterface
{
    use HasAcfFields;

    public string $name = '';

    protected array $settings = [];

    protected bool $preview = false;

    protected int $postid = 0;

    protected array $context = [];

    public function setSettings(array $settings): static
    {
        $this->settings = $settings;

        return $this;
    }

    function setting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    function settings(): array
    {
        return $this->settings;
    }

    public function setPreview(bool $preview): static
    {
        $this->preview = $preview;

        return $this;
    }

    public function setPostId(int $postid): static
    {
        $this->postid = $postid;

        return $this;
    }

    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }

    function context(string $key): Collection
    {
        return collect($this->context[$key] ?? []);
    }

    public function id(): string
    {
        return $this->settings['id'];
    }
}
