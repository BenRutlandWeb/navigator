<?php

namespace Navigator\Foundation\Console\Commands;

use Navigator\Collections\Collection;
use Navigator\Console\Command;
use Navigator\Str\Str;
use WP_CLI;
use WP_CLI\Dispatcher\CompositeCommand;

class ListCommands extends Command
{
    protected string $signature = 'list';

    protected string $description = 'List commands.';

    protected function handle(): void
    {
        $this->line('Navigator');
        $this->newLine();

        $longest = 0;

        Collection::make($this->getSubcommands())
            ->map(function ($subcommand) use (&$longest) {
                if (($length = Str::length($subcommand->get_name())) > $longest) {
                    $longest = $length;
                }

                return $this->parseSubcommand($subcommand);
            })
            ->groupBy('group')
            ->sortKeys()
            ->each(function ($subcommands, $key) use ($longest) {
                $this->line("<comment>{$key}</comment>");
                $subcommands->each(function ($subcommand) use ($longest) {
                    $padded = Str::padRight($subcommand['name'], $longest + 2);
                    $this->line(" <info>{$padded}</info>{$subcommand['description']}");
                });
            });
    }

    /** @return array<int, CompositeCommand> */
    protected function getSubcommands(): array
    {
        [$command] = WP_CLI::get_runner()->find_command_to_run(['navigator']);

        return $command->get_subcommands();
    }

    protected function parseSubcommand(CompositeCommand $subcommand): array
    {
        $name = $subcommand->get_name();

        $group = ($parts = explode(':', $name)) && (isset($parts[1])) ? $parts[0] : 'Available commands:';

        return [
            'group'       => $group,
            'name'        => $name,
            'description' => $subcommand->get_shortdesc(),
        ];
    }
}
