<?php

declare(strict_types=1);

namespace Shop\Production;

use Shop\Logger\ExtendedLoggerInterface;
use Shop\Production\Exception\InvalidStateTransferException;
use Shop\Production\State\GiftWrapped;
use Shop\Production\State\StateInterface;
use Shop\Production\Workflow\Workflow;
use Shop\Production\Workflow\WorkflowInterface;

final class ProcessManager
{
    public function __construct(
        private ExtendedLoggerInterface $logger,
        private WorkflowInterface $workflow,
    ) {}

    /**
     * Confirm whether the given state is valid for the article.
     * If the state is a valid next state, move the article to the new state.
     * @throws InvalidStateTransferException
     */
    public function confirmAndMoveToState(StateInterface $state, Article $article): void
    {
        $this->workflow->initWorkflow($article);
        $currentState = $article->getState();
        $newState = $state;

        try {
            while ($this->workflow->valid()) {
                if ($this->workflow->current() === get_class($currentState)) {
                    $this->workflow->next();

                    if ($this->workflow->current() === get_class($newState)) {
                        $article->setState($newState);
                        $this->logStateChange($article, $currentState, $newState);

                        return;
                    }

                    $this->throwInvalidStateTransferException($newState, $article);
                }

                $this->workflow->next();
            }

            $this->throwInvalidStateTransferException($state, $article);
        } catch (InvalidStateTransferException $exception) {
            $this->logger->logException($exception);

            throw $exception;
        }
    }

    public function getWorkflow(): Workflow
    {
        return $this->workflow;
    }

    /**
     * @throws InvalidStateTransferException
     */
    private function throwInvalidStateTransferException(StateInterface $state, Article $article): void
    {
        throw new InvalidStateTransferException(
            sprintf(
                'Invalid state transition for article type %s from state %s to %s',
                $article->getType(),
                $article->getState()->getType(),
                $state->getType()
            )
        );
    }

    private function logStateChange(Article $article, StateInterface $currentState, StateInterface $newState): void
    {
        $this->logger->debug('Article of type {article_type} has changed its state '
                . 'from {old_state} to {new_state}.', [
            'article_type' => $article->getType(),
            'old_state' => $currentState->getType(),
            'new_state' => $newState->getType(),
        ]);

        if ($newState instanceof GiftWrapped) {
            $this->logger->debug('Article of type {article_type} has option gift wrapped.', [
                'article_type' => $article->getType(),
            ]);
        }
    }
}
