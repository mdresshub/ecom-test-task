<?php

declare(strict_types=1);

namespace Shop\Logger\Handler;

class FileHandler implements HandlerInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle(string $level, string $message, array $context = []): void
    {
        file_put_contents(
            $this->filePath,
            sprintf(
                '[%s] %s: %s',
                date('Y-m-d H:i:s'),
                strtoupper($level),
                $message
            ) . PHP_EOL,
            FILE_APPEND
        );
    }
}
