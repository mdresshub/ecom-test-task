<?php

declare(strict_types=1);

namespace Shop\Production\State;

final class Shipped implements StateInterface
{
	public const TYPE = 'shipped';

	public function getType(): string
	{
		return self::TYPE;
	}
}
