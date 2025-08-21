<?php

namespace Navigator\Acf;

use Navigator\Acf\Models\Concerns\HasAcfFields;

abstract class Block implements BlockInterface
{
    use HasAcfFields;

    public string $name = '';

    protected array $settings = [];

    protected bool $preview = false;

    protected int $postid = 0;

    public function setSettings(array $settings): static
    {
        $this->settings = $settings;

        return $this;
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

    public function id(): string
    {
        return $this->settings['id'];
    }
}
