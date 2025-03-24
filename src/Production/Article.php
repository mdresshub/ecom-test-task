<?php

declare(strict_types=1);

namespace Shop\Production;

use InvalidArgumentException;
use Shop\Logger\ExtendedLoggerInterface;
use Shop\Production\State\StateInterface;

class Article
{
	public const TYPE_POSTER_FRAMED = 'poster-framed';

	public const TYPE_PRINTED_GLASS = 'printed-glass';

	private bool $hasGiftWrapping = false;

    /**
     * @throws InvalidArgumentException Unknown article type
     */
    public function __construct(
        private ExtendedLoggerInterface $logger,
        private string $articleType,
        private StateInterface $state,
    ) {
        $this->validateType($articleType);
    }

	/**
	 * @return array<int, string>
	 */
	public static function getTypes(): array
	{
		return [
			self::TYPE_POSTER_FRAMED,
			self::TYPE_PRINTED_GLASS,
		];
	}

	private static function isTypeValid(string $articleType): bool
	{
		return \in_array($articleType, self::getTypes());
	}

	/**
	 * @throws InvalidArgumentException Unknown article type
	 */
	private function validateType(string $articleType): void
	{
        try {
            if (!self::isTypeValid($articleType)) {
                throw new InvalidArgumentException(
                    'Unknown article type given: ' . $articleType, 1626963396724
                );
            }
        } catch (InvalidArgumentException $exception) {
            $this->logger->logException($exception);

            throw $exception;
        }
	}

	public function hasGiftWrapping(): bool
	{
		return $this->hasGiftWrapping;
	}

	public function enableGiftWrapping(): self
	{
		$this->hasGiftWrapping = true;

		return $this;
	}

	public function getState(): StateInterface
	{
        return $this->state;
	}

    public function setState(StateInterface $state): self
    {
        $this->state = $state;

        return $this;
    }

	public function getType(): string
	{
		return $this->articleType;
	}
}
