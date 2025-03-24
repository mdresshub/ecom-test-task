<?php

declare(strict_types=1);

namespace Shop\Tests\Production;

use Shop\Production\Article;
use Shop\Production\Exception\InvalidStateTransferException;
use Shop\Production\ProcessManager;
use Shop\Production\State\Framed;
use Shop\Production\State\GiftWrapped;
use Shop\Production\State\Ordered;
use Shop\Production\State\Printed;
use Shop\Production\State\Shipped;
use Shop\Production\State\Sliced;
use Shop\Production\State\StateInterface;
use PHPUnit\Framework\TestCase;

final class ArticleTest extends TestCase
{
	private ProcessManager $manager;

	protected function setUp(): void
	{
		$this->manager = new ProcessManager();
	}

	/**
	 * @return \Generator
	 */
	public function dataProviderGetPosterFramed(): \Generator
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
		];
	}

	/**
	 * @return \Generator
	 */
	public function dataProviderGetPosterFramedInvalidTransition(): \Generator
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
		];
		yield 'Skipping Printed' => [
			[
				new Ordered(),
				new Sliced(),
				new Framed(),
				new GiftWrapped(),
				new Shipped(),
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
		];
		yield 'Skipping Framed' => [
			[
				new Ordered(),
				new Printed(),
				new Sliced(),
				new GiftWrapped(),
				new Shipped(),
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
		];
	}

	/**
	 * @return \Generator
	 */
	public function dataProviderGetPrintedGlass(): \Generator
	{
		yield [
			'default' => [
				new Ordered(),
				new Printed(),
				new Shipped(),
			],
			false,
		];

		yield [
			'stateGiftWrapped' => [
				new Ordered(),
				new Printed(),
				new GiftWrapped(),
				new Shipped(),
			],
			true,
		];
	}

	/**
	 * @return \Generator
	 */
	public function dataProviderGetPrintedGlassInvalidTransition(): \Generator
	{
		// Skipping one step Testcases
		yield 'Skipping Ordered' => [
			[
				new Printed(),
				new GiftWrapped(),
				new Shipped(),
			],
		];
		yield 'Skipping Printed' => [
			[
				new Ordered(),
				new GiftWrapped(),
				new Shipped(),
			],
		];
		yield 'Skipping Giftwrapped' => [
			[
				new Ordered(),
				new Printed(),
				new Shipped(),
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
		];
		yield 'Giftwrap Twice' => [
			[
				new Ordered(),
				new Printed(),
				new GiftWrapped(),
				new GiftWrapped(),
				new Shipped(),
			],
		];
	}

	/**
	 * @param StateInterface[] $states
	 * @dataProvider dataProviderGetPosterFramed
	 * @doesNotPerformAssertions
	 */
	public function testPosterFramed(array $states, bool $hasGiftWrapping): void
	{
		$article = new Article(Article::TYPE_POSTER_FRAMED);

		if ($hasGiftWrapping) {
			$article->enableGiftWrapping();
		}

		foreach ($states as $state) {
			$this->manager->confirmAndMoveToState($state, $article);
		}
	}

	/**
	 * @param StateInterface[] $states
	 * @dataProvider dataProviderGetPosterFramedInvalidTransition
	 */
	public function testPosterFramedInvalidStateTransitions(array $states): void
	{
		$article = new Article(Article::TYPE_POSTER_FRAMED);
		$article->enableGiftWrapping();

		$this->expectException(InvalidStateTransferException::class);

		foreach ($states as $state) {
			$this->manager->confirmAndMoveToState($state, $article);
		}
	}

	/**
	 * @param StateInterface[] $states
	 * @dataProvider dataProviderGetPrintedGlass
	 * @doesNotPerformAssertions
	 */
	public function testPrintedGlass(array $states, bool $hasGiftWrapping): void
	{
		$article = new Article(Article::TYPE_PRINTED_GLASS);

		if ($hasGiftWrapping) {
			$article->enableGiftWrapping();
		}

		foreach ($states as $state) {
			$this->manager->confirmAndMoveToState($state, $article);
		}
	}

	/**
	 * @param StateInterface[] $states
	 * @dataProvider dataProviderGetPrintedGlassInvalidTransition
	 */
	public function testPrintedGlassInvalidStateTransitions(array $states): void
	{
		$article = new Article(Article::TYPE_PRINTED_GLASS);
		$article->enableGiftWrapping();

		$this->expectException(InvalidStateTransferException::class);

		foreach ($states as $state) {
			$this->manager->confirmAndMoveToState($state, $article);
		}
	}
}
