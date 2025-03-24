<?php

declare(strict_types=1);

namespace Shop\Logger;

use Shop\Logger\Handler\HandlerInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;
use Throwable;

class Logger extends AbstractLogger implements ExtendedLoggerInterface
{
    private string $logLevel = LogLevel::INFO;

    private HandlerInterface $handler;

    public function setLogLevel(string $level): self
    {
        $this->logLevel = $level;

        return $this;
    }

    public function setHandler(HandlerInterface $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (!isset($this->logLevels()[$level])) {
            throw new InvalidArgumentException("Invalid log level: $level");
        }

        if ($this->shouldLog($level)) {
            $message = $this->interpolate($message, $context);
            $this->handler->handle($level, $message, $context);
        }
    }

    public function logException(Throwable $exception): void
    {
        $this->error($exception->getMessage() . PHP_EOL
            . '> Exception: {class}' . PHP_EOL
            . '> Line: {line}' . PHP_EOL
            . '> Code: {code}' . PHP_EOL
            . '{trace}',
            [
                'class' => get_class($exception),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTraceAsString(),
            ],
        );
    }

    private function shouldLog(string $level): bool
    {
        $levels = $this->logLevels();

        return $levels[$level] >= $levels[$this->logLevel];
    }

    /**
     * @return array<string, int>
     */
    private function logLevels(): array
    {
        return [
            LogLevel::DEBUG => 0,
            LogLevel::INFO => 1,
            LogLevel::NOTICE => 2,
            LogLevel::WARNING => 3,
            LogLevel::ERROR => 4,
            LogLevel::CRITICAL => 5,
            LogLevel::ALERT => 6,
            LogLevel::EMERGENCY => 7,
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    private function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }
}
