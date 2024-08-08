<?php

namespace Navigator\Database;

use wpdb;

class Connection
{
    public function __construct(protected wpdb $connection)
    {
        //
    }

    public function tableWithPrefix(string $name): string
    {
        return $this->prefix . $name;
    }

    public function hasTable(string $name): string
    {
        $name = $this->tableWithPrefix($name);
        $query = $this->prepare('SHOW TABLES LIKE %s', $this->esc_like($name));

        if ($this->get_var($query) === $name) {
            return true;
        }

        return false;
    }

    public function query(string $query): int|bool
    {
        return $this->connection->query($query);
    }

    public function __call(string $method, array $parameters = []): mixed
    {
        return $this->connection->$method(...$parameters);
    }

    public function __isset(string $property): bool
    {
        return isset($this->connection->$property);
    }

    public function __get(string $property): mixed
    {
        return $this->connection->$property ?? null;
    }

    public function __set(string $property, mixed $value): void
    {
        $this->connection->$property = $value;
    }

    public function __unset(string $property): void
    {
        unset($this->connection->$property);
    }
}
