<?php

declare(strict_types=1);

namespace Shop\Shipping;

use Shop\API\Entity\Customer;
use Shop\Shipping\Entity\Street;

final class AddressValidator
{
	/**
	 * @return Customer[]
	 */
	public function getAllCustomers(): array
	{
		// TODO: Retrieve customers from \Shop\API\CustomerDataApiMock

		return [];
	}

	/**
	 * Split a given street string from a customer into
	 * street name and house number.
	 *
	 * @param Customer $customer
	 * @return Street
	 * @throws \Exception
	 */
	public function splitStreet(Customer $customer): Street
	{
		// TODO: Implement

		throw new \Exception('method not implemented', 1626964164621);
	}
}
