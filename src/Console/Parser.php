<?php

namespace Navigator\Console;

use Exception;
use Navigator\Collections\Arr;
use Navigator\Str\Str;

class Parser
{
    public static function parse(string $expression): array
    {
        $name = static::name($expression);

        if (preg_match_all('/\{\s*(.*?)\s*\}/', $expression, $matches)) {
            if (count($matches[1])) {
                return Arr::merge([$name], static::parameters($matches[1]));
            }
        }

        return [$name, [], []];
    }

    protected static function name(string $expression): string
    {
        if (!preg_match('/[^\s]+/', $expression, $matches)) {
            throw new Exception('Unable to determine command name from signature.');
        }

        return $matches[0];
    }

    protected static function parameters(array $tokens): array
    {
        $arguments = [];

        $options = [];

        foreach ($tokens as $token) {
            if (preg_match('/-{2,}(.*)/', $token, $matches)) {
                $options[] = static::parseOption($matches[1]);
            } else {
                $arguments[] = static::parseArgument($token);
            }
        }

        return [$arguments, $options];
    }

    protected static function parseArgument(string $token): array
    {
        [$token, $description] = static::extractDescription($token);

        switch (true) {
            case Str::endsWith($token, '?'):
                return [
                    'type'        => 'positional',
                    'name'        => trim($token, '?'),
                    'description' => $description,
                    'optional'    => true,
                ];
            case preg_match('/(.+)\=(.+)/', $token, $matches):
                return [
                    'type'        => 'positional',
                    'name'        => $matches[1],
                    'description' => $description,
                    'default'     => $matches[2],
                ];
            default:
                return [
                    'type'        => 'positional',
                    'name'        => $token,
                    'description' => $description,
                ];
        }
    }

    protected static function parseOption(string $token): array
    {
        [$token, $description] = static::extractDescription($token);

        switch (true) {
            case Str::endsWith($token, '='):
                return [
                    'type'        => 'assoc',
                    'name'        => trim($token, '='),
                    'description' => $description,
                ];
            case preg_match('/(.+)\=(.+)/', $token, $matches):
                return [
                    'type'        => 'assoc',
                    'name'        => $matches[1],
                    'description' => $description,
                    'default'     => $matches[2],
                ];
            default:
                return [
                    'type'        => 'flag',
                    'name'        => $token,
                    'description' => $description,
                    'optional'    => true,
                ];
        }
    }

    protected static function extractDescription(string $token): array
    {
        $parts = preg_split('/\s+:\s+/', trim($token), 2);

        return count($parts) === 2 ? $parts : [$token, ''];
    }
}
