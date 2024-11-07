<?php

namespace Navigator\Console;

use Navigator\Collections\Arr;
use Navigator\Console\Parser;
use Navigator\Console\ProgressBar;
use Navigator\Foundation\Application;
use WP_CLI;

use function WP_CLI\Utils\format_items as wpcli_format_items;

abstract class Command
{
    protected string $signature = '';

    protected string $name = '';

    protected string $description = '';

    protected array $synopsis = [];

    protected array $allowedArguments = [];

    protected array $arguments = [];

    protected array $options = [];

    public function __construct(protected Application $app)
    {
        $this->parseSignature();
    }

    public function register(): void
    {
        WP_CLI::add_command('navigator ' . $this->name, $this, $this->synopsis);
    }

    protected function parseSignature(): void
    {
        [$name, $arguments, $options] = Parser::parse($this->signature);

        $this->name = $name;

        $this->allowedArguments = $arguments;

        $this->synopsis = [
            'shortdesc' => $this->description,
            'synopsis'  => Arr::merge($arguments, $options),
        ];
    }

    public function __invoke(array $args, array $options): void
    {
        $this->arguments = $this->parseArguments($args);

        $this->options = $options;

        $this->handle();
    }

    abstract protected function handle(): void;

    protected function parseArguments(array $args): array
    {
        $arguments = [];

        foreach ($this->allowedArguments as $index => $argument) {
            $arguments[$argument['name']] = $args[$index];
        }
        return $arguments;
    }

    protected function arguments(): array
    {
        return $this->arguments;
    }

    protected function argument(string $key): mixed
    {
        return $this->arguments[$key] ?? null;
    }

    protected function options(): array
    {
        return $this->options;
    }

    protected function option(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    protected function colorize(string $message): string
    {
        $map = [
            'error'    => "\033[41m",
            'info'     => "\033[32m",
            'warn'     => "\033[33m",
            'comment'  => "\033[33m",
            'question' => "\033[30m\033[106m",
        ];

        $message = preg_replace_callback('#<(.+?)>#', function ($match) use ($map) {
            return $map[$match[1]] ?? $match[0];
        }, $message);

        return preg_replace('#</(.+?)>#', "\033[0m", $message);
    }

    protected function success(string $message): Command
    {
        WP_CLI::success($this->colorize("<info>{$message}</info>"));

        return $this;
    }

    protected function warning(string $message): Command
    {
        WP_CLI::warning($this->colorize("<warn>{$message}</warn>"));

        return $this;
    }

    protected function error(string $message): void
    {
        WP_CLI::error($this->colorize("<error>{$message}</error>"));
    }

    protected function line(string $message): Command
    {
        WP_CLI::log($this->colorize($message));

        return $this;
    }

    protected function newLine(int $lines = 1): Command
    {
        while ($lines > 0) {
            $this->line('');
            $lines--;
        }
        return $this;
    }

    protected function table(array $headers, array $data): Command
    {
        wpcli_format_items('table', $data, $headers);

        return $this;
    }

    public function createProgressBar(int $count): ProgressBar
    {
        return new ProgressBar($count);
    }

    protected function confirm(string $question, bool $skip = false): bool
    {
        if (!$skip) {
            $answer = $this->ask($question . ' [y/n]');

            return $answer  == 'y' || $answer === 'yes';
        }
        return true;
    }

    protected function ask(string $question): string
    {
        fwrite(STDOUT, $question . ' ');

        return trim(fgets(STDIN));
    }

    protected function terminate(?string $message = null): void
    {
        if ($message) {
            $this->line($message);
        }
        die;
    }

    protected function call(string $command, array $arguments = []): bool
    {
        $return = WP_CLI::runcommand($command, [
            'return'       => true,
            'launch'       => false,
            'exit_error'   => false,
            'command_args' => Arr::keys(Arr::filter($arguments)),
        ]);

        echo $return;

        return $return ? true : false;
    }

    protected function callSilently(string $command, array $arguments = []): bool
    {
        $return = WP_CLI::runcommand($command, [
            'return'       => true,
            'launch'       => false,
            'exit_error'   => false,
            'command_args' => Arr::keys(Arr::filter($arguments)),
        ]);

        return $return ? true : false;
    }
}
