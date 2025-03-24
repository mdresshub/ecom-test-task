<?php

declare(strict_types=1);

namespace Shop\Logger\Handler;

interface HandlerInterface
{
    public function handle(string $level, string $message, array $context = []): void;
}
