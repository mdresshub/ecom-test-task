<?php

declare(strict_types=1);

namespace Shop\Shipping\Entity;

final class Street
{
	public string $name;

	public string $number;

	public function __construct(string $name, string $number)
	{
		$this->name   = $name;
		$this->number = $number;
	}
}
