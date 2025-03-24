<?php

declare(strict_types=1);

namespace Shop\Logger\Handler;

use JsonException;
use PDO;

class DatabaseHandler implements HandlerInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @throws JsonException
     */
    public function handle(string $level, string $message, array $context = []): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO logs (level, message, context, created_at) VALUES (:level, :message, :context, :created_at)');
        $stmt->execute([
            ':level' => $level,
            ':message' => $message,
            ':context' => json_encode($context, JSON_THROW_ON_ERROR),
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
