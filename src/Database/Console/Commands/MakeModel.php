<?php

namespace Navigator\Database\Console\Commands;

use Navigator\Collections\Arr;
use Navigator\Console\GeneratorCommand;
use Navigator\Database\ModelType;

class MakeModel extends GeneratorCommand
{
    protected string $type = 'Model';

    protected string $signature = 'make:model {name : The name of model}
                                       {--type=post : The type of model to extend: [post, term, comment, user]}
                                       {--force : Overwrite the model if it exists}';

    protected string $description = 'Make a model class.';

    protected function getStub(): string
    {
        $type = $this->option('type');

        if (!ModelType::tryFrom($type)) {
            $types = join(', ', Arr::enumValues(ModelType::class));

            $this->error("--type={$type} is invalid. Valid options are [{$types}].");
        }

        if ($type == 'post') {
            return __DIR__ . '/stubs/model.post.stub';
        } elseif ($type == 'term') {
            return __DIR__ . '/stubs/model.term.stub';
        } elseif ($type == 'comment') {
            return __DIR__ . '/stubs/model.comment.stub';
        } elseif ($type == 'user') {
            return __DIR__ . '/stubs/model.user.stub';
        }
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Models';
    }
}
