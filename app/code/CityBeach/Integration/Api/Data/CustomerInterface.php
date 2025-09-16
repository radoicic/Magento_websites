<?php
/**
 * Customer model interface.
 */

namespace CityBeach\Integration\Api\Data;

use \Magento\Framework\Api\ExtensibleDataInterface;

interface CustomerInterface extends ExtensibleDataInterface
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
    public function getEmail();

    /**
     * @param string $email
     */
    public function setEmail($email);


    /**
     * @throws \Exception if validation of these object fails.
     */
    public function validate();
}
