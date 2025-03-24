<?php

declare(strict_types=1);

namespace Shop\Tests\Logger;

use Exception;
use Shop\Logger\ExtendedLoggerInterface;
use Shop\Logger\Logger;
use Shop\Logger\Handler\HandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Psr\Log\InvalidArgumentException;

final class LoggerTest extends TestCase
{
    private ExtendedLoggerInterface $logger;

    private HandlerInterface $handler;

    protected function setUp(): void
    {
        $this->handler = $this->createMock(HandlerInterface::class);
        $this->logger = (new Logger())
            ->setLogLevel(LogLevel::DEBUG)
            ->setHandler($this->handler);
    }

    public function testLogInfo(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo(LogLevel::INFO),
                $this->equalTo('This is an info message'),
                $this->equalTo(['user' => 'JohnDoe'])
            )
        ;

        $this->logger->log(LogLevel::INFO, 'This is an info message', ['user' => 'JohnDoe']);
    }

    public function testLogError(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo(LogLevel::ERROR),
                $this->equalTo('This is an error message'),
                $this->equalTo(['exception' => 'FileNotFound'])
            )
        ;

        $this->logger->log(LogLevel::ERROR, 'This is an error message', ['exception' => 'FileNotFound']);
    }

    public function testLogDebug(): void
    {
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo(LogLevel::DEBUG),
                $this->equalTo('This is a debug message'),
                $this->equalTo(['debug' => 'variable value'])
            )
        ;

        $this->logger->log(LogLevel::DEBUG, 'This is a debug message', ['debug' => 'variable value']);
    }

    public function testInvalidLogLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->logger->log('invalid_level', 'This is an invalid log level message');
    }

    public function testLogLevelFiltering(): void
    {
        $this->logger->setLogLevel(LogLevel::ERROR);

        $this->handler
            ->expects($this->never())
            ->method('handle');

        $this->logger->log(LogLevel::INFO, 'This message should not be logged');
    }

    public function testLogException(): void
    {
        $exception = new Exception('Test exception', 123);

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->equalTo(LogLevel::ERROR),
                $this->stringContains('Test exception'),
                $this->arrayHasKey('trace')
            )
        ;

        $this->logger->logException($exception);
    }

    public function testInterpolateMessage(): void
    {
        $reflection = new \ReflectionClass($this->logger);
        $method = $reflection->getMethod('interpolate');
        $method->setAccessible(true);

        $message = 'User {username} created';
        $context = ['username' => 'JohnDoe'];
        $result = $method->invokeArgs($this->logger, [$message, $context]);

        $this->assertSame('User JohnDoe created', $result);
    }
}
