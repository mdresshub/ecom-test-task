<?php

declare(strict_types=1);

namespace Shop\Production\State;

final class Printed implements StateInterface
{
	public const TYPE = 'printed';

	public function getType(): string
	{
		return self::TYPE;
	}
}
