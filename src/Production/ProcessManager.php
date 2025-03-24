<?php

declare(strict_types=1);

namespace Shop\Production;

use Shop\Production\Exception\InvalidStateTransferException;
use Shop\Production\State\StateInterface;

final class ProcessManager
{
	/**
	 * Confirm whether the given state is valid for the article.
	 * If the state is a valid next state, move the article to the new state.
	 * @throws InvalidStateTransferException
	 */
	public function confirmAndMoveToState(StateInterface $state, Article $article): void
	{
		// TODO: Implement
	}
}
