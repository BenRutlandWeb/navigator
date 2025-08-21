<?php

namespace Navigator\Acf;

use Closure;
use Navigator\Foundation\Application;
use Navigator\View\ViewFactory;
use Throwable;

class AcfFactory
{
    public function __construct(protected Application $app, protected ViewFactory $view, protected string $path)
    {
        //
    }

    /** @param class-string<Block> $block */
    public function registerBlock(string $block): void
    {
        $block = $this->app->build($block);

        register_block_type("{$this->path}/{$block->name}", [
            'render_callback' => $this->renderCallback($block),
        ]);
    }

    protected function renderCallback(Block $block): Closure
    {
        return function (array $settings, string $content = '', bool $preview = false, int $postId = 0, WP_Block $wpBlock, array $context = []) use ($block): void {
            $block->setSettings($settings)
                ->setPreview($preview)
                ->setPostId($postId)
                ->setContext($context);

            try {
                $content = $this->view->file($settings['path'] . '/template.php', compact('block'));

                if ($preview) {
                    echo $content;
                } else {
                    echo sprintf('<div %s>%s</div>', get_block_wrapper_attributes(), $content);
                }
            } catch (Throwable $e) {
                printf('<p>%s</p>', $e->getMessage());
            }
        };
    }

    /** @param class-string<FieldGroup> $fieldGroup */
    public function registerFieldGroup(string $fieldGroup): void
    {
        $fieldGroup = new $fieldGroup();

        $fieldGroup->register();
    }
}
