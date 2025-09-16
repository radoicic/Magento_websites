<?php
/**
 * Address model interface.
 */

namespace CityBeach\Integration\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface AddressInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getCompany();

    /**
     * @param string $company
     */
    public function setCompany($company);

    /**
     * @return string
     */
    public function getLine1();

    /**
     * @param string $line1
     */
    public function setLine1($line1);

    /**
     * @return string
     */
    public function getLine2();

    /**
     * @param string $line2
     */
    public function setLine2($line2);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $city
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getPostcode();

    /**
     * @param string $postcode
     */
    public function setPostcode($postcode);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $phone
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getCountryName();

    /**
     * @param string $countryName
     */
    public function setCountryName($countryName);

    /**
     * @return string
     */
    public function getCountryCode();

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode);

    /**
     * @throws \Exception if validation of these object fails.
     */
    public function validate();
}
