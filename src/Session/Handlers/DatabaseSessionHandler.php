<?php

namespace Navigator\Session\Handlers;

use Navigator\Database\Connection;
use SessionHandlerInterface;

class DatabaseSessionHandler implements SessionHandlerInterface
{
    protected string $table;

    public function __construct(protected Connection $db)
    {
        $this->table = $db->prefix . 'sessions';
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
        $session = $this->db->get_row(
            $this->db->prepare("SELECT * FROM {$this->table} WHERE id = %s", $id),
            OBJECT
        );

        return $session ? base64_decode($session->payload) : '';
    }

    public function write(string $id, string $data): bool
    {
        return $this->db->replace($this->table, [
            'id'            => $id,
            'payload'       => base64_encode($data),
            'last_activity' => time(),
        ]);
    }

    public function destroy(string $id): bool
    {
        return $this->db->delete($this->table, ['id' => $id]) ? true : false;
    }

    public function gc(int $lifetime): int|false
    {
        return $this->db->query(
            $this->db->prepare("DELETE FROM {$this->table} WHERE last_activity < %i", time() - $lifetime)
        );
    }
}
