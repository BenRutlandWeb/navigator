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

    protected string $description = 'List the registered commands.';

    protected function handle(): void
    {
        Collection::make($this->getSubcommands())
            ->map(function ($subcommand) {
                return $this->parseSubcommand($subcommand);
            })
            ->groupBy('group')
            ->sortKeys()
            ->each(function (Collection $subcommands, $key) {
                $this->headedList(Str::replace('-', ' ', $key), $subcommands->mapWithKeys(function ($value) {
                    return [$value['name'] => $value['description']];
                })->toArray());
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
