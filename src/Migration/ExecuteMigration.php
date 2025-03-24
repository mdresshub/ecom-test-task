<?php

declare(strict_types=1);

namespace Shop\Migration;

use PDO;
use PDOException;

class ExecuteMigration
{
    private PDO $pdo;
    private string $dsn;
    private string $username;
    private string $password;

    public function __construct(string $dsn, string $username, string $password)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->connect();
    }

    private function connect(): void
    {
        try {
            $this->pdo = new PDO($this->dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            throw new PDOException("Connection failed: " . $exception->getMessage());
        }
    }

    public function execute(): void
    {
        try {
            $migration = new MigrateNormalized($this->pdo);
            $migration->migrate();
            echo "Migration completed successfully.";
        } catch (PDOException $exception) {
            echo "Migration failed: " . $exception->getMessage();
        }
    }
}
