<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations;

/**
 * Reservation builder
 */
class ReservationBuilder extends \Magento\InventoryReservations\Model\ReservationBuilder 
    implements \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationBuilderInterface 
{
    /**
     * Source code
     * 
     * @var string
     */
    private $sourceCode;

    /**
     * SKU
     * 
     * @var string
     */
    private $sku;

    /**
     * Quantity
     * 
     * @var float
     */
    private $quantity;

    /**
     * Metadata
     * 
     * @var string
     */
    private $metadata;

    /**
     * Object manager
     * 
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Snake to camel case converter
     * 
     * @var \Magento\InventoryReservations\Model\SnakeToCamelCaseConverter
     */
    private $snakeToCamelCaseConverter;

    /**
     * Validation result factory
     * 
     * @var \Magento\Framework\Validation\ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * Constructor
     * 
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\InventoryReservations\Model\SnakeToCamelCaseConverter $snakeToCamelCaseConverter
     * @param \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory
     * @return void
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\InventoryReservations\Model\SnakeToCamelCaseConverter $snakeToCamelCaseConverter,
        \Magento\Framework\Validation\ValidationResultFactory $validationResultFactory
    )
    {
        parent::__construct(
            $objectManager,
            $snakeToCamelCaseConverter,
            $validationResultFactory
        );
        $this->objectManager = $objectManager;
        $this->snakeToCamelCaseConverter = $snakeToCamelCaseConverter;
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * Set source code
     * 
     * @param string $sourceCode
     * @return \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
     */
    public function setSourceCode(string $sourceCode): \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
    {
        $this->sourceCode = $sourceCode;
        return $this;
    }
    
    /**
     * Set SKU
     * 
     * @param string $sku
     * @return \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
     */
    public function setSku(string $sku): \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * Set quantity
     * 
     * @param float $quantity
     * @return \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
     */
    public function setQuantity(float $quantity): \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Set metadata
     * 
     * @param string|null $metadata
     * @return \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
     */
    public function setMetadata(string $metadata = null): \Magento\InventoryReservationsApi\Model\ReservationBuilderInterface
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Build
     * 
     * @return \Magento\InventoryReservationsApi\Model\ReservationInterface
     * @throws \Magento\Framework\Validation\ValidationException
     */
    public function build(): \Magento\InventoryReservationsApi\Model\ReservationInterface
    {
        $validationResult = $this->validate();
        if (!$validationResult->isValid()) {
            throw new \Magento\Framework\Validation\ValidationException(__('Validation error'), null, 0, $validationResult);
        }
        $data = [
            \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::RESERVATION_ID => null,
            \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SOURCE_CODE => $this->sourceCode,
            \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SKU => $this->sku,
            \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::QUANTITY => $this->quantity,
            \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::METADATA => $this->metadata,
        ];
        $arguments = $this->convertArrayKeysFromSnakeToCamelCase($data);
        $reservation = $this->objectManager->create(\Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::class, $arguments);
        $this->reset();
        return $reservation;
    }

    /**
     * Validate
     * 
     * @return \Magento\Framework\Validation\ValidationResult
     */
    private function validate(): \Magento\Framework\Validation\ValidationResult
    {
        $errors = [];
        if (null === $this->sourceCode) {
            $errors[] = __('"%field" can not be empty.', ['field' => \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SOURCE_CODE]);
        }
        if (null === $this->sku || '' === trim($this->sku)) {
            $errors[] = __('"%field" can not be empty.', ['field' => \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::SKU]);
        }
        if (null === $this->quantity) {
            $errors[] = __('"%field" can not be null.', ['field' => \Ecombricks\InventoryCustomerSourceSelection\Model\InventoryReservations\ReservationInterface::QUANTITY]);
        }
        return $this->validationResultFactory->create(['errors' => $errors]);
    }

    /**
     * Convert array keys from snake to camel case
     *
     * @param array $array
     * @return array
     */
    private function convertArrayKeysFromSnakeToCamelCase(array $array): array
    {
        $convertedArrayKeys = $this->snakeToCamelCaseConverter->convert(array_keys($array));
        return array_combine($convertedArrayKeys, array_values($array));
    }

    /**
     * Reset
     * 
     * @return void
     */
    private function reset(): void
    {
        $this->sourceCode = null;
        $this->sku = null;
        $this->quantity = null;
        $this->metadata = null;
    }
}