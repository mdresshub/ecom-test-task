<?php

declare(strict_types=1);

namespace Shop\Production\Workflow;

use Shop\Logger\ExtendedLoggerInterface;
use Shop\Production\Article;
use Shop\Production\State\GiftWrapped;
use Shop\Production\State\StateInterface;
use Psr\Log\InvalidArgumentException;

final class Workflow implements WorkflowInterface
{
    private int $position = 0;

    /**
     * @var array<int, class-string<StateInterface>>
     */
    private array $states = [];

    public function __construct(
        private ExtendedLoggerInterface $logger,
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function initWorkflow(Article $article): void
    {
        $this->setStates($article);
    }

    public function removeState(string $state): void
    {
        if (!\in_array($state, $this->states, true)) {
            return;
        }

        $this->states = \array_values(
            \array_filter(
                $this->states,
                static fn (string $filterState): bool => $filterState !== $state
            )
        );
    }

    public function count(): int
    {
        return count($this->states);
    }

    public function current(): string
    {
        return $this->states[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->states[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    private function setStates(Article $article): void
    {
        $this->validateType($article->getType());

        $this->states = TypeToStateMapping::getStatesByType($article->getType());

        if (!$article->hasGiftWrapping()) {
            $this->removeState(GiftWrapped::class);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateType(string $articleType): void
    {
        try {
            if (TypeToStateMapping::getStatesByType($articleType) === []) {
                throw new InvalidArgumentException('Unsupported workflow article type: ' . $articleType);
            }
        } catch (InvalidArgumentException $exception) {
            $this->logger->logException($exception);

            throw $exception;
        }
    }
}
