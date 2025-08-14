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
        $type = $this->ask('What type of factory do you want to create?', Arr::enumValues(ModelType::class), ModelType::POST->value);

        return match ($type) {
            ModelType::POST->value => __DIR__ . '/stubs/factory.post.stub',
            ModelType::TERM->value => __DIR__ . '/stubs/factory.term.stub',
            ModelType::COMMENT->value => __DIR__ . '/stubs/factory.comment.stub',
            ModelType::USER->value => __DIR__ . '/stubs/factory.user.stub',
            default => __DIR__ . '/stubs/factory.post.stub',
        };
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Database\\Factories';
    }
}
