<?php

declare(strict_types=1);

namespace Shop\Tests\Production;

use Generator;
use Shop\Logger\Handler\ConsoleHandler;
use Shop\Logger\Logger;
use Shop\Production\Article;
use Shop\Production\Exception\InvalidStateTransferException;
use Shop\Production\ProcessManager;
use Shop\Production\State\Framed;
use Shop\Production\State\GiftWrapped;
use Shop\Production\State\Initiated;
use Shop\Production\State\Ordered;
use Shop\Production\State\Printed;
use Shop\Production\State\Shipped;
use Shop\Production\State\Sliced;
use Shop\Production\State\StateInterface;
use Shop\Production\Workflow\Workflow;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

final class ArticleTest extends TestCase
{
	private ProcessManager $manager;

	protected function setUp(): void
	{
        $logger = (new Logger())
            ->setLogLevel(LogLevel::DEBUG)
            ->setHandler(new ConsoleHandler())
        ;
		$this->manager = new ProcessManager($logger, new Workflow($logger));
	}

	/**
	 * @return Generator
	 */
	public function dataProviderGetPosterFramed(): Generator
	{
		yield [
			'default' => [
				new Ordered(),
				new Printed(),
				new Sliced(),
				new Framed(),
				new Shipped(),
			],
			false,
            'expectedStateCount' => 6,
		];

        yield [
			'stateGiftWrapped' => [
				new Ordered(),
				new Printed(),
				new Sliced(),
				new Framed(),
				new GiftWrapped(),
				new Shipped(),
			],
			true,
            'expectedStateCount' => 7,
		];
	}

	/**
	 * @return Generator
	 */
	public function dataProviderGetPosterFramedInvalidTransition(): Generator
	{
		// Skipping one step Testcases
		yield 'Skipping Ordered' => [
			[
				new Printed(),
				new Sliced(),
				new Framed(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state initiated to printed',
            ],
		];
		yield 'Skipping Printed' => [
			[
				new Ordered(),
				new Sliced(),
				new Framed(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state ordered to sliced',
            ],
		];
		yield 'Skipping Sliced' => [
			[
				new Ordered(),
				new Printed(),
				new Framed(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state printed to framed',
            ],
		];
		yield 'Skipping Framed' => [
			[
				new Ordered(),
				new Printed(),
				new Sliced(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state sliced to gift-wrapped',
            ],
		];
		yield 'Skipping Giftwrapped' => [
			[
				new Ordered(),
				new Printed(),
				new Sliced(),
				new Framed(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state framed to shipped',
            ],
		];

		// Invalid Order Testcases
		yield 'GiftWrap before framing' => [
			[
				new Ordered(),
				new Printed(),
				new Sliced(),
				new GiftWrapped(),
				new Framed(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state sliced to gift-wrapped',
            ],
		];
		yield 'Slice before printing' => [
			[
				new Ordered(),
				new Sliced(),
				new Printed(),
				new Framed(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state ordered to sliced',
            ],
		];
		yield 'Frame before slicing' => [
			[
				new Ordered(),
				new Printed(),
				new Framed(),
				new Sliced(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state printed to framed',
            ],
		];
		yield 'Ship before giftwrapping' => [
			[
				new Ordered(),
				new Printed(),
				new Sliced(),
				new Framed(),
				new Shipped(),
				new GiftWrapped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state framed to shipped',
            ],
		];
		yield 'Giftwrapping twice' => [
			[
				new Ordered(),
				new Printed(),
				new Sliced(),
				new Framed(),
				new GiftWrapped(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type poster-framed from state gift-wrapped to gift-wrapped',
            ],
		];
	}

	/**
	 * @return Generator
	 */
	public function dataProviderGetPrintedGlass(): Generator
	{
		yield [
			'default' => [
				new Ordered(),
				new Printed(),
				new Shipped(),
			],
			false,
            'expectedStateCount' => 4,
		];

		yield [
			'stateGiftWrapped' => [
				new Ordered(),
				new Printed(),
				new GiftWrapped(),
				new Shipped(),
			],
			true,
            'expectedStateCount' => 5,
		];
	}

	/**
	 * @return Generator
	 */
	public function dataProviderGetPrintedGlassInvalidTransition(): Generator
	{
		// Skipping one step Testcases
		yield 'Skipping Ordered' => [
			[
				new Printed(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type printed-glass from state initiated to printed',
            ],
		];
		yield 'Skipping Printed' => [
			[
				new Ordered(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type printed-glass from state ordered to gift-wrapped',
            ],
		];
		yield 'Skipping Giftwrapped' => [
			[
				new Ordered(),
				new Printed(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type printed-glass from state printed to shipped',
            ],
		];

		// Duplicate Steps
		yield 'Printing Twice' => [
			[
				new Ordered(),
				new Printed(),
				new Printed(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type printed-glass from state printed to printed',
            ],
		];
		yield 'Giftwrap Twice' => [
			[
				new Ordered(),
				new Printed(),
				new GiftWrapped(),
				new GiftWrapped(),
				new Shipped(),
			],
            [
                'exception' => InvalidStateTransferException::class,
                'message' => 'Invalid state transition for article type printed-glass from state gift-wrapped to gift-wrapped',
            ],
		];
	}

	/**
	 * @param StateInterface[] $states
	 * @dataProvider dataProviderGetPosterFramed
	 */
	public function testPosterFramed(array $states, bool $hasGiftWrapping, int $expectedStateCount): void
	{
        $logger = (new Logger())
            ->setLogLevel(LogLevel::DEBUG)
            ->setHandler(new ConsoleHandler())
        ;
		$article = new Article($logger, Article::TYPE_POSTER_FRAMED, new Initiated());

		if ($hasGiftWrapping) {
			$article->enableGiftWrapping();
		}

        $this->assertCount(0, $this->manager->getWorkflow());

		foreach ($states as $state) {
			$this->manager->confirmAndMoveToState($state, $article);
		}

        $this->assertCount($expectedStateCount, $this->manager->getWorkflow());
	}

	/**
	 * @param StateInterface[] $states
	 * @dataProvider dataProviderGetPosterFramedInvalidTransition
	 */
	public function testPosterFramedInvalidStateTransitions(array $states, array $exception): void
	{
        $logger = (new Logger())
            ->setLogLevel(LogLevel::DEBUG)
            ->setHandler(new ConsoleHandler())
        ;
		$article = new Article($logger, Article::TYPE_POSTER_FRAMED, new Initiated());
		$article->enableGiftWrapping();

		$this->expectException($exception['exception']);
        $this->expectExceptionMessage($exception['message']);

		foreach ($states as $state) {
			$this->manager->confirmAndMoveToState($state, $article);
		}
	}

	/**
	 * @param StateInterface[] $states
	 * @dataProvider dataProviderGetPrintedGlass
	 */
	public function testPrintedGlass(array $states, bool $hasGiftWrapping, int $expectedStateCount): void
	{
        $logger = (new Logger())
            ->setLogLevel(LogLevel::DEBUG)
            ->setHandler(new ConsoleHandler())
        ;
		$article = new Article($logger, Article::TYPE_PRINTED_GLASS, new Initiated());

		if ($hasGiftWrapping) {
			$article->enableGiftWrapping();
		}

        $this->assertCount(0, $this->manager->getWorkflow());

		foreach ($states as $state) {
			$this->manager->confirmAndMoveToState($state, $article);
		}

        $this->assertCount($expectedStateCount, $this->manager->getWorkflow());
	}

	/**
	 * @param StateInterface[] $states
	 * @dataProvider dataProviderGetPrintedGlassInvalidTransition
	 */
	public function testPrintedGlassInvalidStateTransitions(array $states, $exception): void
	{
        $logger = (new Logger())
            ->setLogLevel(LogLevel::DEBUG)
            ->setHandler(new ConsoleHandler())
        ;
		$article = new Article($logger, Article::TYPE_PRINTED_GLASS, new Initiated());
		$article->enableGiftWrapping();

		$this->expectException($exception['exception']);
        $this->expectExceptionMessage($exception['message']);

		foreach ($states as $state) {
			$this->manager->confirmAndMoveToState($state, $article);
		}
	}
}
