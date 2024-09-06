<?php

namespace Navigator\Database\Console\Commands;

use Navigator\Collections\Arr;
use Navigator\Console\GeneratorCommand;
use Navigator\Str\Str;

class MakeModel extends GeneratorCommand
{
    protected string $type = 'Model';

    protected string $signature = 'make:model {name : The name of model}
                                       {--type=post : The type of model to extend: [post, term, comment, user]}
                                       {--force : Overwrite the model if it exists}';

    protected string $description = 'Make a model class.';

    protected function replaceClass(string $stub, string $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        $type = $this->option('type');

        if (!$this->validate($type)) {
            $this->error("--type={$type} is invalid. Valid options are [post, term, comment, user]");
        }

        return $stub;
    }

    protected function getStub(): string
    {
        if ($this->option('type') == 'post') {
            return __DIR__ . '/stubs/model.post.stub';
        } elseif ($this->option('type') == 'term') {
            return __DIR__ . '/stubs/model.term.stub';
        } elseif ($this->option('type') == 'comment') {
            return __DIR__ . '/stubs/model.comment.stub';
        } elseif ($this->option('type') == 'user') {
            return __DIR__ . '/stubs/model.user.stub';
        }
    }

    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Models';
    }

    public function validate(string $type): bool
    {
        return Arr::has(Str::lower(Str::trim($type)), ['post', 'term', 'comment', 'user']);
    }
}
