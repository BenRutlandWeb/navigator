<?php

namespace Navigator\Database\Console\Commands;

use Navigator\Collections\Arr;
use Navigator\Console\GeneratorCommand;
use Navigator\Database\ModelType;

class MakeModel extends GeneratorCommand
{
    protected string $type = 'Model';

    protected string $signature = 'make:model {name : The name of the model}
                                       {--force : Overwrite the model if it exists}';

    protected string $description = 'Make a model class.';

    protected function getStub(): string
    {
        $type = $this->ask('What type of model do you want to extend?', Arr::enumValues(ModelType::class), ModelType::POST->value);

        return match ($type) {
            ModelType::POST->value    => __DIR__ . '/stubs/model.post.stub',
            ModelType::TERM->value    => __DIR__ . '/stubs/model.term.stub',
            ModelType::COMMENT->value => __DIR__ . '/stubs/model.comment.stub',
            ModelType::USER->value    => __DIR__ . '/stubs/model.user.stub',
            default                   => __DIR__ . '/stubs/model.post.stub',
        };
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Models';
    }
}
