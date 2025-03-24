<?php

declare(strict_types=1);

namespace Shop\Tests\Shipping;

use JsonException;
use Shop\API\CustomerDataApiMock;
use Shop\API\Entity\Customer;
use Shop\Shipping\AddressValidator;
use Shop\Shipping\Entity\Street;
use PHPUnit\Framework\TestCase;

final class AddressValidatorTest extends TestCase
{
    private AddressValidator $addressValidator;

    protected function setUp(): void
    {
        $this->customerDataApiMock = $this->createMock(CustomerDataApiMock::class);
        $this->addressValidator = new AddressValidator($this->customerDataApiMock);
    }

    public function testGetAllCustomers(): void
    {
        $customerDataJson = '[{"firstname":"John","lastname":"Doe","street":"Main St 123","city":"Anytown","zipCode":"12345"}]';

        $this->customerDataApiMock
            ->expects($this->once())
            ->method('getCustomerData')
            ->willReturn($customerDataJson)
        ;

        $customers = $this->addressValidator->getAllCustomers();

        $this->assertCount(1, $customers);
        $this->assertInstanceOf(Customer::class, $customers[0]);
        $this->assertSame('John', $customers[0]->getFirstname());
        $this->assertSame('Doe', $customers[0]->getLastname());
        $this->assertSame('Main St 123', $customers[0]->getStreet());
        $this->assertSame('Anytown', $customers[0]->getCity());
        $this->assertSame('12345', $customers[0]->getZipCode());
    }

    public function testProcessCustomerStreet(): void
    {
        $customerDataJson = '[{"firstname":"John","lastname":"Doe","street":"Main St 123","city":"Anytown","zipCode":"12345"}]';

        $this->customerDataApiMock
            ->expects($this->once())
            ->method('getCustomerData')
            ->willReturn($customerDataJson)
        ;

        $streets = $this->addressValidator->processCustomerStreet();

        $this->assertCount(1, $streets);
        $this->assertInstanceOf(Street::class, $streets[0]);
        $this->assertSame('Main St', $streets[0]->name);
        $this->assertSame('123', $streets[0]->number);
    }

    public function testSplitStreetWithHouseNumber(): void
    {
        $customer = new Customer('John', 'Doe', 'Main St 123', 'Anytown', '12345');

        $street = $this->addressValidator->splitStreet($customer);

        $this->assertInstanceOf(Street::class, $street);
        $this->assertSame('Main St', $street->name);
        $this->assertSame('123', $street->number);
    }

    public function testSplitStreetWithoutHouseNumber(): void
    {
        $customer = new Customer('John', 'Doe', 'Am Elfenholt', 'Anytown', '12345');

        $street = $this->addressValidator->splitStreet($customer);

        $this->assertInstanceOf(Street::class, $street);
        $this->assertSame('Am Elfenholt', $street->name);
        $this->assertSame('', $street->number);
    }

    public function testSplitStreetWithComplexStreetName(): void
    {
        $customer = new Customer('John', 'Doe', 'Wald a.A. 125', 'Anytown', '12345');

        $street = $this->addressValidator->splitStreet($customer);

        $this->assertInstanceOf(Street::class, $street);
        $this->assertSame('Wald a.A.', $street->name);
        $this->assertSame('125', $street->number);
    }

    public function testSplitStreetWithComplexHousenumber(): void
    {
        $customer = new Customer('John', 'Doe', 'Rosenheimerstr. 145 e+f', 'Anytown', '12345');

        $street = $this->addressValidator->splitStreet($customer);

        $this->assertInstanceOf(Street::class, $street);
        $this->assertSame('Rosenheimerstr.', $street->name);
        $this->assertSame('145 e+f', $street->number);
    }

    public function testGetAllCustomersThrowsJsonException(): void
    {
        $this->customerDataApiMock
            ->expects($this->once())
            ->method('getCustomerData')
            ->willReturn('invalid_json')
        ;

        $this->expectException(JsonException::class);

        $this->addressValidator->getAllCustomers();
    }
}
