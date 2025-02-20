<?php

namespace Navigator\Session\Handlers;

use SessionHandlerInterface;

class ArraySessionHandler implements SessionHandlerInterface
{
    public function __construct(protected array $store = [])
    {
        //
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string
    {
        return ($this->store[$id] ?? null) ? $this->store[$id]['payload'] : '';
    }

    public function write(string $id, string $data): bool
    {
        $this->store[$id] = [
            'id'            => $id,
            'payload'       => $data,
            'last_activity' => time(),
        ];

        return true;
    }

    public function destroy(string $id): bool
    {
        unset($this->store[$id]);

        return true;
    }

    public function gc(int $lifetime): int
    {
        $deleted = 0;

        foreach ($this->store as $id => $session) {
            if ($session['last_activity'] < time() - $lifetime) {
                $this->destroy($id);
                $deleted++;
            }
        }

        return $deleted;
    }
}
