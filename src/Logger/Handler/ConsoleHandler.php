<?php

declare(strict_types=1);

namespace Shop\Logger\Handler;

class ConsoleHandler implements HandlerInterface
{
    public function handle(string $level, string $message, array $context = []): void
    {
        echo sprintf('[%s] %s: %s', date('Y-m-d H:i:s'), strtoupper($level), $message) . PHP_EOL;
    }
}
