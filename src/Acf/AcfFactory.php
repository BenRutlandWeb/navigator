<?php

namespace Navigator\Acf;

use Navigator\View\ViewFactory;

class AcfFactory
{
    public function __construct(protected ViewFactory $view, protected string $path)
    {
        //
    }

    /** @param class-string<Block> $block */
    public function registerBlock(string $block): void
    {
        $block = new $block($this->view, $this->path);

        $block->register();
    }

    /** @param class-string<FieldGroup> $fieldGroup */
    public function registerFieldGroup(string $fieldGroup): void
    {
        $fieldGroup = new $fieldGroup();

        $fieldGroup->register();
    }
}
