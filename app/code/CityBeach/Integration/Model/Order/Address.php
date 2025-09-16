<?php

namespace CityBeach\Integration\Model\Order;

use CityBeach\Integration\Api\Data\AddressInterface;

class Address implements AddressInterface
{
    /**
     * @var string $firstName
     */
    private $firstName;

    /**
     * @var string $lastName
     */
    private $lastName;

    /**
     * @var string $company
     */
    private $company;

    /**
     * @var string $line1
     */
    private $line1;

    /**
     * @var string $line2
     */
    private $line2;

    /**
     * @var string $city
     */
    private $city;

    /**
     * @var string $state
     */
    private $state;

    /**
     * @var string $postcode
     */
    private $postcode;

    /**
     * @var string $phone
     */
    private $phone;

    /**
     * @var string $countryName
     */
    private $countryName;

    /**
     * @var string $countryCode
     */
    private $countryCode;

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * @param string $line1
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;
    }

    /**
     * @return string
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * @param string $line2
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * @param string $countryName
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;
    }

    public function validate()
    {
        if (empty($this->countryName) || empty($this->countryCode)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Missing required property on address'));
        }
    }

    public function toArray()
    {
        return array(
            'firstname' => $this->firstName,
            'lastname' => $this->lastName,
            'company' => $this->company,
            'street' => [$this->line1, $this->line2],
            'city' => $this->city,
            'region' => $this->state,
            'postcode' => $this->postcode,
            'telephone' => $this->phone,
            'country_id' => $this->countryCode,
        );
    }
}
