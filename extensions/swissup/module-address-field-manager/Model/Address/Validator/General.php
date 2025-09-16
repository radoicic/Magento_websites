<?php

namespace Swissup\AddressFieldManager\Model\Address\Validator;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\ValidatorInterface;
use Swissup\AddressFieldManager\Model\ResourceModel\Customer\Form\AddressAttribute\CollectionFactory;
use Laminas\Validator\ValidatorChain;

class General implements ValidatorInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    private $directoryData = null;

    /*
     * @var ValidatorChain
     */
    private $validatorChain;

    /**
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param ValidatorChain $validatorChain
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        \Magento\Directory\Helper\Data $directoryData,
        ValidatorChain $validatorChain
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->directoryData = $directoryData;
        $this->validatorChain = $validatorChain;
    }

    public function validate(AbstractAddress $address)
    {
        $errors = [];

        $collection = $this->collectionFactory->create();
        foreach ($collection as $attribute) {
            if (!$attribute->getIsRequired() || !$attribute->getIsVisible()) {
                continue;
            }

            $code  = $attribute->getAttributeCode();
            $value = $address->getData($code);
            if ('street' === $attribute->getAttributeCode()) {
                $value = $address->getStreetLine(1);
            }

            if (!$this->validatorChain->isValid($value, 'NotEmpty')) {
                $errors[] = __('%fieldName is a required field.', [
                    'fieldName' => $code
                ]);
            }
        }

        $havingOptionalZip = $this->directoryData->getCountriesWithOptionalZip();
        if (!in_array($address->getCountryId(), $havingOptionalZip)
            && !$this->validatorChain->isValid($address->getPostcode(), 'NotEmpty')
        ) {
            $errors[] = __('%fieldName is a required field.', [
                'fieldName' => 'postcode'
            ]);
        }

        return $errors;
    }
}
