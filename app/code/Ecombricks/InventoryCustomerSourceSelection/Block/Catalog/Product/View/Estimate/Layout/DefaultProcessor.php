<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Block\Catalog\Product\View\Estimate\Layout;

/**
 * Product estimate default layout processor
 */
class DefaultProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * Merger
     * 
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    private $merger;

    /**
     * Country collection factory
     * 
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * Region collection factory
     * 
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * Top destination countries
     * 
     * @var \Magento\Directory\Model\TopDestinationCountries
     */
    private $topDestinationCountries;

    /**
     * Locale format
     * 
     * @var \Magento\Framework\Locale\FormatInterface 
     */
    private $localeFormat;

    /**
     * Constructor
     * 
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $merger
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\TopDestinationCountries $topDestinationCountries
     * @return void
     */
    public function __construct(
        \Magento\Checkout\Block\Checkout\AttributeMerger $merger,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\TopDestinationCountries $topDestinationCountries
    )
    {
        $this->merger = $merger;
        $this->localeFormat = $localeFormat;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->topDestinationCountries = $topDestinationCountries;
    }

    /**
     * Process
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        $this->setDataProviderDictionaries($jsLayout);
        $this->mergeAddress($jsLayout);
        return $jsLayout;
    }
    
    /**
     * Get country options
     * 
     * @return array
     */
    private function getCountryOptions(): array
    {
        $countryCollection = $this->countryCollectionFactory->create();
        $countryCollection->loadByStore();
        $countryCollection->setForegroundCountries($this->topDestinationCountries->getTopDestinations());
        return $countryCollection->toOptionArray();
    }
    
    /**
     * Get region options
     * 
     * @return array
     */
    private function getRegionOptions(): array
    {
        $regionCollection = $this->regionCollectionFactory->create();
        $regionCollection->addAllowedCountriesFilter();
        return $regionCollection->toOptionArray();
    }
    
    /**
     * Set data provider dictionaries
     * 
     * @param array &$jsLayout
     * @return void
     */
    private function setDataProviderDictionaries(array &$jsLayout): void
    {
        $checkoutProvider =& $jsLayout['components']['checkoutProvider'];
        if (empty($checkoutProvider['dictionaries'])) {
            $checkoutProvider['dictionaries'] = [
                'country_id' => $this->getCountryOptions(),
                'region_id' => $this->getRegionOptions(),
            ];
        }
    }

    /**
     * Get address fields
     * 
     * @return array
     */
    private function getAddressFields(): array
    {
        return [
            'city' => [
                'visible' => false,
                'formElement' => 'input',
                'label' => __('City'),
                'value' =>  null,
            ],
            'country_id' => [
                'visible' => true,
                'formElement' => 'select',
                'label' => __('Country'),
                'options' => [],
                'value' => null,
            ],
            'region_id' => [
                'visible' => true,
                'formElement' => 'select',
                'label' => __('State/Province'),
                'options' => [],
                'value' => null,
            ],
            'postcode' => [
                'visible' => true,
                'formElement' => 'input',
                'label' => __('Zip/Postal Code'),
                'value' => null,
            ],
        ];
    }
    
    /**
     * Merge address
     * 
     * @param array &$jsLayout
     * @return void
     */
    private function mergeAddress(&$jsLayout): void
    {
        $address =& $jsLayout['components']['product-estimate']['children']['address'];
        if (empty($address['children'])) {
            return;
        }
        $address['children'] = $this->merger->merge($this->getAddressFields(), 'checkoutProvider', 'shippingAddress', $address['children']);
        $address['children']['region_id']['config']['skipValidation'] = true;
    }
}