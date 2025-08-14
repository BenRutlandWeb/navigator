<?php

namespace Navigator\Session\Console\Commands;

use Navigator\Console\Command;
use Navigator\Database\Connection;

class SessionTables extends Command
{
    protected string $signature = 'session:tables';

    protected string $description = 'Create the session database tables.';

    protected function handle(): void
    {
        $this->header('Navigator', 'Create the session database tables');

        $db = $this->app->get(Connection::class);

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $db->hide_errors();

        $charset = $db->get_charset_collate();

        $sql = "CREATE TABLE {$db->prefix}sessions (
                id VARCHAR(255) NOT NULL,
                payload TEXT NOT NULL,
                last_activity INT(11) NOT NULL,
                PRIMARY KEY  (id)
				) $charset;";

        dbDelta($sql);

        $this->success('Database tables created');
    }
}
