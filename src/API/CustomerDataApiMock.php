<?php

declare(strict_types=1);

namespace Shop\API;

class CustomerDataApiMock
{
	/**
	 * API Mock to simulate an api request to an external service.
	 * The api response is a json string of all customer data.
	 */
	public function getCustomerData(): string
	{
		return \file_get_contents('../../resources/files/customer_data.json');
	}
}
