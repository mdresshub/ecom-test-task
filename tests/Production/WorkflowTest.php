<?php

declare(strict_types=1);

namespace Shop\Tests\Production;

use Shop\Logger\ExtendedLoggerInterface;
use Shop\Logger\Logger;
use Shop\Production\Article;
use Shop\Production\State\Framed;
use Shop\Production\State\GiftWrapped;
use Shop\Production\State\Initiated;
use Shop\Production\State\Ordered;
use Shop\Production\State\Printed;
use Shop\Production\State\Shipped;
use Shop\Production\State\Sliced;
use Shop\Production\Workflow\Workflow;
use Shop\Production\Workflow\WorkflowInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;

final class WorkflowTest extends TestCase
{
    private WorkflowInterface $workflow;
    private ExtendedLoggerInterface $logger;
    private Article $article;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(Logger::class);
        $this->workflow = new Workflow($this->logger);
        $this->article = $this->createMock(Article::class);
    }

    public function testInitWorkflowWithValidArticle(): void
    {
        $this->article
            ->expects($this->exactly(2))
            ->method('getType')
            ->willReturn(Article::TYPE_POSTER_FRAMED)
        ;

        $this->article
            ->expects($this->once())
            ->method('hasGiftWrapping')
            ->willReturn(true)
        ;

        $this->workflow->initWorkflow($this->article);

        $this->assertCount(7, $this->workflow);
        $this->assertSame(Initiated::class, $this->workflow->current());
    }

    public function testInitWorkflowWithInvalidArticleType(): void
    {
        $this->article
            ->expects($this->once())
            ->method('getType')
            ->willReturn('invalid_type')
        ;

        $this->logger
            ->expects($this->once())
            ->method('logException')
            ->with($this->isInstanceOf(InvalidArgumentException::class))
        ;

        $this->expectException(InvalidArgumentException::class);

        $this->workflow->initWorkflow($this->article);
    }

    public function testRemoveState(): void
    {
        $this->article
            ->expects($this->exactly(2))
            ->method('getType')
            ->willReturn(Article::TYPE_POSTER_FRAMED)
        ;

        $this->article
            ->expects($this->once())
            ->method('hasGiftWrapping')
            ->willReturn(true)
        ;

        $this->workflow->initWorkflow($this->article);
        $this->workflow->removeState(GiftWrapped::class);

        $this->assertCount(6, $this->workflow);
        $this->assertNotContains(GiftWrapped::class, $this->workflow);
    }

    public function testWorkflowIteration(): void
    {
        $this->article
            ->expects($this->exactly(2))
            ->method('getType')
            ->willReturn(Article::TYPE_POSTER_FRAMED)
        ;

        $this->article
            ->expects($this->once())
            ->method('hasGiftWrapping')
            ->willReturn(true)
        ;

        $this->workflow->initWorkflow($this->article);

        $expectedStates = [
            Initiated::class,
            Ordered::class,
            Printed::class,
            Sliced::class,
            Framed::class,
            GiftWrapped::class,
            Shipped::class,
        ];

        foreach ($expectedStates as $expectedState) {
            $this->assertTrue($this->workflow->valid());
            $this->assertSame($expectedState, $this->workflow->current());
            $this->workflow->next();
        }

        $this->assertFalse($this->workflow->valid());
    }

    public function testValidateTypeWithValidType(): void
    {
        $this->article
            ->expects($this->once())
            ->method('getType')
            ->willReturn(Article::TYPE_POSTER_FRAMED)
        ;

        $reflection = new \ReflectionClass($this->workflow);
        $method = $reflection->getMethod('validateType');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($this->workflow, [$this->article->getType()]));
    }

    public function testValidateTypeWithInvalidType(): void
    {
        $this->article
            ->expects($this->once())
            ->method('getType')
            ->willReturn('invalid_type')
        ;

        $reflection = new \ReflectionClass($this->workflow);
        $method = $reflection->getMethod('validateType');
        $method->setAccessible(true);

        $this->logger
            ->expects($this->once())
            ->method('logException')
            ->with($this->isInstanceOf(InvalidArgumentException::class))
        ;

        $this->expectException(InvalidArgumentException::class);

        $method->invokeArgs($this->workflow, [$this->article->getType()]);
    }
}
