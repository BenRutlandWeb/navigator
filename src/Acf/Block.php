<?php

namespace Navigator\Acf;

use Navigator\View\ViewFactory;

abstract class Block
{
    protected string $name = '';

    protected string $blockPath = '';

    public function __construct(protected ViewFactory $view, protected string $path)
    {
        $this->blockPath = $this->path . '/' . $this->name;
    }

    public function register(): void
    {
        register_block_type($this->blockPath, ['render_callback' => [$this, 'render']]);
    }

    public function render(array $block, string $content = '', bool $preview = false, int $postId = 0): void
    {
        $block = new BlockHelper($block, $postId, $preview);

        $content = $this->view->file("{$this->blockPath}/template.php", compact('block'));

        if ($preview) {
            echo $content;
        } else {
            echo sprintf('<div %s>%s</div>', get_block_wrapper_attributes(), $content);
        }
    }
}
