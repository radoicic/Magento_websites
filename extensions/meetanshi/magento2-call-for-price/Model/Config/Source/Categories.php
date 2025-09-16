<?php

namespace Meetanshi\Callforprice\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Categories
 */
class Categories implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $categoryCollection;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Categories constructor.
     * @param CollectionFactory $categoryCollection
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(CollectionFactory $categoryCollection, StoreManagerInterface $storeManager)
    {
        $this->categoryCollection = $categoryCollection;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function toOptionArray()
    {
        $categories = $this
            ->categoryCollection->create()
            ->addAttributeToSelect('*')
            ->setStore($this->storeManager->getStore());
        $options = [];
        foreach ($categories as $category) {
            if ($category->getName() != 'Root Catalog') {
                $options[] = [
                    'value' => $category->getId(),
                    'label' => $category->getName()

                ];
            }
        }
        return $options;
    }
}
