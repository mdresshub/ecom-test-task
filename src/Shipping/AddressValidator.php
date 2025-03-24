<?php

declare(strict_types=1);

namespace Shop\Shipping;

use JsonException;
use Shop\API\CustomerDataApiMock;
use Shop\API\Entity\Customer;
use Shop\Shipping\Entity\Street;

final class AddressValidator
{
    public function __construct(
        private CustomerDataApiMock $customerDataApiMock,
    ) {}

    /**
     * @return array<int, Customer>
     *
     * @throws JsonException
     */
	public function getAllCustomers(): array
	{
        $customerDataJson = $this->customerDataApiMock->getCustomerData();
		$customerDataArray = \json_decode($customerDataJson, true, 512, JSON_THROW_ON_ERROR);

		$customers = [];
		foreach ($customerDataArray as $customerData) {
		    $customers[] = new Customer(
		        $customerData['firstname'],
		        $customerData['lastname'],
		        $customerData['street'],
		        $customerData['city'],
		        $customerData['zipCode']
		    );
		}

		return $customers;
	}

    /**
     * @return array<int, Street>
     *
     * @throws JsonException
     */
    public function processCustomerStreet(): array
    {
        $customers = $this->getAllCustomers();

        $streets = [];
        foreach ($customers as $customer) {
            $streets[] = $this->splitStreet($customer);
        }

        return $streets;
    }

	/**
	 * Split a given street string from a customer into
	 * street name and house number.
	 *
	 * @param Customer $customer
	 * @return Street
	 */
	public function splitStreet(Customer $customer): Street
	{
        $street = $customer->getStreet();

        $matches = [];
        \preg_match('/^(\D+)?\s*(\d.*)?$/', $street, $matches);

        $streetName = isset($matches[1]) ? trim($matches[1]) : '';
        $houseNumber = isset($matches[2]) ? trim($matches[2]) : '';

        return new Street($streetName, $houseNumber);
	}
}
