<?php

namespace Navigator\Database\Console\Commands;

use Navigator\Collections\Arr;
use Navigator\Console\GeneratorCommand;
use Navigator\Database\ModelType;

class MakeFactory extends GeneratorCommand
{
    protected string $type = 'Factory';

    protected string $signature = 'make:factory {name : The name of the factory}
                                        {--type=post : The type of factory to create: [post, term, comment, user]}
                                        {--force : Overwrite the factory if it exists}';

    protected string $description = 'Make a factory class.';

    protected function getStub(): string
    {
        $type = $this->option('type');

        if (!ModelType::tryFrom($type)) {
            $types = join(', ', Arr::enumValues(ModelType::class));

            $this->error("--type={$type} is invalid. Valid options are [{$types}].");
        }

        if ($type == 'post') {
            return __DIR__ . '/stubs/factory.post.stub';
        } elseif ($type == 'term') {
            return __DIR__ . '/stubs/factory.term.stub';
        } elseif ($type == 'comment') {
            return __DIR__ . '/stubs/factory.comment.stub';
        } elseif ($type == 'user') {
            return __DIR__ . '/stubs/factory.user.stub';
        }
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Database\\Factories';
    }
}
