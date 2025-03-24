<?php

declare(strict_types=1);

namespace Shop\Production\State;

final class Initiated implements StateInterface
{
	public const TYPE = 'initiated';

	public function getType(): string
	{
		return self::TYPE;
	}
}
