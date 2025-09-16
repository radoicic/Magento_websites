<?php

namespace Meetanshi\Callforprice\Ui\Component\Listing\Column;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Country
 */
class Country implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * Country constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->countryCollectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $countryCollection = $this->countryCollectionFactory->create();
        return $countryCollection->toOptionArray();
    }
}
