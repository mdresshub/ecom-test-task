<?php

declare(strict_types=1);

namespace Shop\Tests\Production;

use Shop\API\Entity\Customer;
use Shop\Shipping\AddressValidator;
use Shop\Shipping\Entity\Street;
use PHPUnit\Framework\TestCase;

final class AddressTest extends TestCase
{
	/**
	 * @dataProvider dataProviderCustomerAddresses
	 */
	public function testAddressSplit(Customer $customer, Street $expectedStreet): void
	{
		$addressValidator = new AddressValidator();
		$street           = $addressValidator->splitStreet($customer);

		self::assertEquals($expectedStreet->name, $street->name);
		self::assertEquals($expectedStreet->number, $street->number);
	}

	/**
	 * @return \Generator
	 */
	public function dataProviderCustomerAddresses(): \Generator
	{
		yield 'Customer 1: Barbara Müller' => [
			new Customer('', '', 'Einsteinstr. 7', '', ''),
			new Street('Einsteinstr.', '7'),
		];
		yield 'Customer 2: Michael Scholtz' => [
			new Customer('', '', 'Einsteinstrasse 7', '', ''),
			new Street('Einsteinstrasse', '7'),
		];
		yield 'Customer 3: Ines Ostermann' => [
			new Customer('', '', 'Curd-Jürgens-Str. 30', '', ''),
			new Street('Curd-Jürgens-Str.', '30'),
		];
		yield 'Customer 4: Fabian Kruger' => [
			new Customer('', '', 'Perlcherstr.88 1', '', ''),
			new Street('Perlcherstr.', '88 1'),
		];
		yield 'Customer 5: Jana Neumann' => [
			new Customer('', '', 'Rosenheimerstr. 14e-f', '', ''),
			new Street('Rosenheimerstr.', '14e-f'),
		];
		yield 'Customer 6: Anja Maier' => [
			new Customer('', '', 'Bei Fußenkreuz 36', '', ''),
			new Street('Bei Fußenkreuz', '36'),
		];
		yield 'Customer 7: Tom Wexler' => [
			new Customer('', '', 'Sankt Georgs Kirchhof 26', '', ''),
			new Street('Sankt Georgs Kirchhof', '26'),
		];
		yield 'Customer 8: Jonas Kuhn' => [
			new Customer('', '', 'Mallertshofener Strasse 36c', '', ''),
			new Street('Mallertshofener Strasse', '36c'),
		];
		yield 'Customer 9: Sven Schweitzer' => [
			new Customer('', '', 'Rosenheimerstr. 145 e+f', '', ''),
			new Street('Rosenheimerstr.', '145 e+f'),
		];
		yield 'Customer 10: Nicole Bohm' => [
			new Customer('', '', 'Hof 151', '', ''),
			new Street('Hof', '151'),
		];
		yield 'Customer 11: Marie Fenstermach' => [
			new Customer('', '', 'Wald a.A. 125', '', ''),
			new Street('Wald a.A.', '125'),
		];
		yield 'Customer 12: Silke Pabst' => [
			new Customer('', '', 'Lindenhof 0', '', ''),
			new Street('Lindenhof', '0'),
		];
		yield 'Customer 13: Daniela Frey' => [
			new Customer('', '', 'Am Elfenholt', '', ''),
			new Street('Am Elfenholt', ''),
		];
		yield 'Customer 14: Lukas Abt' => [
			new Customer('', '', 'Am Schießberg 35 357', '', ''),
			new Street('Am Schießberg', '35 357'),
		];
		yield 'Customer 15: Janina Koch' => [
			new Customer('', '', 'Idlhofgasse 16A-1', '', ''),
			new Street('Idlhofgasse', '16A-1'),
		];
		yield 'Customer 16: Steffen Zimmermann' => [
			new Customer('', '', 'Kreisbacherstrasse 3\/1\/19', '', ''),
			new Street('Kreisbacherstrasse', '3\/1\/19'),
		];
		yield 'Customer 17: Kristin Walter' => [
			new Customer('', '', 'Höpflergasse 6 \/ 18', '', ''),
			new Street('Höpflergasse', '6 \/ 18'),
		];
	}
}
