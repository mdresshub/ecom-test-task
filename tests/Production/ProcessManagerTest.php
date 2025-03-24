<?php

declare(strict_types=1);

namespace Shop\Tests\Production;

use Shop\Logger\ExtendedLoggerInterface;
use Shop\Logger\Logger;
use Shop\Production\Article;
use Shop\Production\Exception\InvalidStateTransferException;
use Shop\Production\ProcessManager;
use Shop\Production\State\Ordered;
use Shop\Production\State\Printed;
use Shop\Production\State\GiftWrapped;
use Shop\Production\State\Shipped;
use Shop\Production\Workflow\WorkflowInterface;
use PHPUnit\Framework\TestCase;

class ProcessManagerTest extends TestCase
{
    private ExtendedLoggerInterface $logger;

    private WorkflowInterface $workflow;

    private ProcessManager $processManager;

    private Article $article;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(Logger::class);
        $this->workflow = $this->createMock(WorkflowInterface::class);
        $this->processManager = new ProcessManager($this->logger, $this->workflow);
        $this->article = $this->createMock(Article::class);
    }

    public function testValidStateTransition(): void
    {
        $this->workflow
            ->expects($this->once())
            ->method('initWorkflow')
            ->with($this->article)
        ;

        $this->article
            ->expects($this->once())
            ->method('getState')
            ->willReturn(new Ordered())
        ;

        $this->workflow
            ->expects($this->once())
            ->method('valid')
            ->willReturn(true)
        ;

        $this->workflow
            ->expects($this->exactly(2))
            ->method('current')
            ->willReturnOnConsecutiveCalls(Ordered::class, Printed::class)
        ;

        $this->workflow
            ->expects($this->once())
            ->method('next')
        ;

        $this->article
            ->expects($this->once())
            ->method('setState')
            ->with($this->isInstanceOf(Printed::class))
        ;

        $this->article
            ->expects($this->once())
            ->method('getType')
            ->willReturn('poster-framed')
        ;

        $this->logger
            ->expects($this->once())
            ->method('debug')
            ->with(
                'Article of type {article_type} has changed its state from {old_state} to {new_state}.',
                [
                    'article_type' => 'poster-framed',
                    'old_state' => 'ordered',
                    'new_state' => 'printed',
                ]
            );

        $this->processManager->confirmAndMoveToState(new Printed(), $this->article);
    }

    public function testInvalidStateTransition(): void
    {
        $this->workflow
            ->expects($this->once())
            ->method('initWorkflow')
            ->with($this->article)
        ;

        $this->article
            ->expects($this->exactly(2))
            ->method('getState')
            ->willReturn(new Ordered())
        ;

        $this->workflow
            ->expects($this->once())
            ->method('valid')
            ->willReturn(true)
        ;

        $this->workflow
            ->expects($this->exactly(2))
            ->method('current')
            ->willReturnOnConsecutiveCalls(Ordered::class, Printed::class)
        ;

        $this->workflow
            ->expects($this->once())
            ->method('next')
        ;

        $this->article
            ->expects($this->once())
            ->method('getType')
            ->willReturn('poster-framed')
        ;

        $this->expectException(InvalidStateTransferException::class);

        $this->logger
            ->expects($this->once())
            ->method('logException')
            ->with($this->isInstanceOf(InvalidStateTransferException::class))
        ;

        $this->processManager->confirmAndMoveToState(new Shipped(), $this->article);
    }

    public function testGiftWrappedStateTransition(): void
    {
        $this->workflow
            ->expects($this->once())
            ->method('initWorkflow')
            ->with($this->article)
        ;

        $this->article
            ->expects($this->once())
            ->method('getState')
            ->willReturn(new Printed())
        ;

        $this->workflow
            ->expects($this->once())
            ->method('valid')
            ->willReturn(true)
        ;

        $this->workflow
            ->expects($this->exactly(2))
            ->method('current')
            ->willReturnOnConsecutiveCalls(Printed::class, GiftWrapped::class)
        ;

        $this->workflow
            ->expects($this->once())
            ->method('next')
        ;

        $this->article
            ->expects($this->once())
            ->method('setState')
            ->with($this->isInstanceOf(GiftWrapped::class))
        ;

        $this->article
            ->expects($this->exactly(2))
            ->method('getType')
            ->willReturn('poster-framed')
        ;

        $this->logger
            ->expects($this->exactly(2))
            ->method('debug')
            ->withConsecutive(
                [
                    'Article of type {article_type} has changed its state from {old_state} to {new_state}.',
                    [
                        'article_type' => 'poster-framed',
                        'old_state' => 'printed',
                        'new_state' => 'gift-wrapped',
                    ]
                ],
                [
                    'Article of type {article_type} has option gift wrapped.',
                    [
                        'article_type' => 'poster-framed',
                    ]
                ]
            );

        $this->processManager->confirmAndMoveToState(new GiftWrapped(), $this->article);
    }
}
