<?php

declare(strict_types=1);

namespace Shop\Production\State;

final class Framed implements StateInterface
{
	public const TYPE = 'framed';

	public function getType(): string
	{
		return self::TYPE;
	}
}
