<?php

declare(strict_types=1);

namespace Shop\API\Entity;

final class Customer
{
	private string $city;

	private string $firstname;

	private string $lastname;

	private string $street;

	private string $zipCode;

	public function __construct(string $firstname, string $lastname, string $street, string $city, string $zipCode)
	{
		$this->firstname = $firstname;
		$this->lastname  = $lastname;
		$this->street    = $street;
		$this->city      = $city;
		$this->zipCode   = $zipCode;
	}

	public function getCity(): string
	{
		return $this->city;
	}

	public function getFirstname(): string
	{
		return $this->firstname;
	}

	public function getLastname(): string
	{
		return $this->lastname;
	}

	public function getStreet(): string
	{
		return $this->street;
	}

	public function getZipCode(): string
	{
		return $this->zipCode;
	}
}
