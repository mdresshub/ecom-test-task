<?php

declare(strict_types=1);

namespace Shop\Production\State;

final class Ordered implements StateInterface
{
	public const TYPE = 'ordered';

	public function getType(): string
	{
		return self::TYPE;
	}
}
