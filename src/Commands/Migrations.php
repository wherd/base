<?php

declare(strict_types=1);

namespace App\Commands;

use Wherd\Database\Connection;
use Wherd\Database\Fetch;
use Wherd\Foundation\System;
use RuntimeException;

class Migrations
{
    public function create(string ...$name): void
    {
        if (!defined('ROOT')) {
            throw new RuntimeException('ROOT constant is not defined');
        }

        if (empty($name)) {
            echo "Please provide a migration name.\n";
            return;
        }

        $name = array_map('ucfirst', $name);
        $class_name = implode('', $name);
        $path = ROOT . '/var/migrations/' . time() . '.' . $class_name . '.php';

        file_put_contents($path, "<?php\n\nuse \\Wherd\\Database\\Connection;\n\nclass $class_name\n{\n\tpublic function up(Connection \$db)\n\t{\n\t\n\t}\n\n\tpublic function down(Connection \$db)\n\t{\n\t\n\t}\n}\n");

        echo "New migration created: $path\n";
    }

    public function run(): void
    {
        if (!defined('ROOT')) {
            throw new RuntimeException('ROOT constant is not defined');
        }

        $app = System::getInstance();

        /** @var \Wherd\Database\Connection */
        $db = $app->providerOf('db');

        $migrations = $this->getRunnedMigrations($db);
        $files = $this->getFiles(ROOT . '/var/migrations/');

        $query = $db->prepare('INSERT INTO migrations (filename, timestamp) VALUES (?, ?)');

        foreach ($files as $filename) {
            if (in_array($filename, $migrations)) {
                continue;
            }

            include_once ROOT . '/var/migrations/' . $filename;

            $parts = explode('.', $filename);
            $class_name = $parts[1];
            $object = new $class_name();

            if (method_exists($object, 'up')) {
                $object->up($db);
            }

            $query->execute($filename, time());
            echo "Migrate {$class_name}\n";
        }

        echo "\nMigrations finished.\n";
    }

    public function rollback(): void
    {
        if (!defined('ROOT')) {
            throw new RuntimeException('ROOT constant is not defined');
        }

        $app = System::getInstance();

        /** @var \Wherd\Database\Connection */
        $db = $app->providerOf('db');

        $filename = $this->getLastRunnedMigration($db);

        if (!$filename) {
            echo "No migrations found.\n";
            return;
        }

        include_once ROOT . '/var/migrations/' . $filename;

        $parts = explode('.', $filename);
        $class_name = $parts[1];
        $object = new $class_name();
        
        if (method_exists($object, 'down')) {
            $object->down($db);
        }

        $db->prepare('DELETE FROM migrations WHERE filename=?', $filename)->execute();

        echo "Rollback {$class_name}.\n";
    }

    /** @return array<string> */
    protected function getFiles(string $path): array
    {
        $files = [];
        $filenames = scandir($path) ?: [];

        foreach ($filenames as $filename) {
            if ('.' === $filename || '..' === $filename) {
                continue;
            }

            if (is_dir($filename)) {
                $files += $this->getFiles($filename);
                continue;
            }

            if (str_ends_with($filename, '.php')) {
                $files[] = $filename;
            }
        }

        return $files;
    }

    protected function migrationTableExists(Connection $db): bool
    {
        return 'migrations' === $db->fetchColumn('SHOW TABLES LIKE "migrations"');
    }

    protected function createMigrationTable(Connection $db): void
    {
        $db->execute('CREATE TABLE `migrations` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `filename` VARCHAR(255) NOT NULL,
            `executed_at` DATETIME DEFAULT CURRENT_DATE
        )');
    }

    /** @return array<string> */
    protected function getRunnedMigrations(Connection $db): array
    {
        return array_column($db->fetchAll('SELECT `filename` FROM migrations'), 'filename');
    }

    protected function getLastRunnedMigration(Connection $db): string
    {
        return $db->fetchColumn('SELECT `filename` FROM `migrations` ORDER BY `id` DESC') ?: '';
    }
}
