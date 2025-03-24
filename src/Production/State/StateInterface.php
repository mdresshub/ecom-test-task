<?php

declare(strict_types=1);

namespace Shop\Production\State;

interface StateInterface
{
	public function getType(): string;
}
