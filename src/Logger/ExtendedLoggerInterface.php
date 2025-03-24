<?php

declare(strict_types=1);

namespace Shop\Logger;

interface ExtendedLoggerInterface
{
    public function logException(\Throwable $exception): void;
}
