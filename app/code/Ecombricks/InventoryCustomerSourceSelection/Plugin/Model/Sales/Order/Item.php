<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Model\Sales\Order;

/**
 * Order item plugin
 */
class Item extends \Ecombricks\Common\Plugin\Plugin
{
    /**
     * Get source by source code
     * 
     * @var \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode
     */
    private $getSourceBySourceCode;

    /**
     * Order item wrapper factory
     * 
     * @var \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory 
     */
    private $orderItemWrapperFactory;

    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory
     * @param \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode
     * @param \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\DataObject\WrapperFactory $wrapperFactory,
        \Ecombricks\InventoryCommon\Model\GetSourceBySourceCode $getSourceBySourceCode,
        \Ecombricks\InventoryCustomerSourceSelection\Wrapper\Model\Sales\Order\ItemFactory $orderItemWrapperFactory
    )
    {
        parent::__construct($wrapperFactory);
        $this->getSourceBySourceCode = $getSourceBySourceCode;
        $this->orderItemWrapperFactory = $orderItemWrapperFactory;
    }
    
    /**
     * After after load
     * 
     * @param \Magento\Sales\Model\Order\Item $subject
     * @param \Magento\Sales\Model\Order\Item $result
     * @return \Magento\Sales\Model\Order\Item
     */
    public function afterAfterLoad(
        \Magento\Sales\Model\Order\Item $subject,
        $result
    )
    {
        $this->setSubject($subject);
        $this->orderItemWrapperFactory->create($subject)->afterLoad();
        return $result;
    }

    /**
     * After before save
     * 
     * @param \Magento\Sales\Model\Order\Item $subject
     * @param \Magento\Sales\Model\Order\Item $result
     * @return \Magento\Sales\Model\Order\Item
     */
    public function afterBeforeSave(
        \Magento\Sales\Model\Order\Item $subject,
        $result
    )
    {
        $this->setSubject($subject);
        $sourceCode = $this->orderItemWrapperFactory->create($subject)->getSourceCode();
        if (!$sourceCode) {
            return $result;
        }
        if (!$this->getSourceBySourceCode->execute((string) $sourceCode)) {
            throw new \Magento\Framework\Exception\ValidatorException(__('Invalid source code.'));
        }
        return $result;
    }

    /**
     * After after save
     * 
     * @param \Magento\Sales\Model\Order\Item $subject
     * @param \Magento\Sales\Model\Order\Item $result
     * @return \Magento\Sales\Model\Order\Item
     */
    public function afterAfterSave(
        \Magento\Sales\Model\Order\Item $subject,
        $result
    )
    {
        $this->setSubject($subject);
        $this->orderItemWrapperFactory->create($subject)->afterSave();
        return $result;
    }

    /**
     * After before delete
     *
     * @param \Magento\Sales\Model\Order\Item $subject
     * @param \Magento\Sales\Model\Order\Item $result
     * @return \Magento\Sales\Model\Order\Item
     */
    public function afterBeforeDelete(
        \Magento\Sales\Model\Order\Item $subject,
        $result
    )
    {
        $this->setSubject($subject);
        $this->orderItemWrapperFactory->create($subject)->beforeDelete();
        return $result;
    }
    
    /**
     * After get product options
     * 
     * @param \Magento\Sales\Model\Order\Item $subject
     * @param array $result
     * @return array
     */
    public function afterGetProductOptions(
        \Magento\Sales\Model\Order\Item $subject,
        $result
    )
    {
        $sourceCode = (string) $this->orderItemWrapperFactory->create($subject)->getSourceCode();
        if (empty($sourceCode)) {
            return $result;
        }
        if (empty($result['additional_options'])) {
            $result['additional_options'] = [];
        }
        $source = $this->getSourceBySourceCode->execute($sourceCode);
        $result['additional_options'] = $this->appendSourceOption(
            $sourceCode,
            $source ? $source->getName() : $sourceCode,
            $result['additional_options']
        );
        return $result;
    }

    /**
     * Append source option
     * 
     * @param string $sourceCode
     * @param string $sourceName
     * @param array $options
     * @return array
     */
    private function appendSourceOption(string $sourceCode, string $sourceName, array $options): array
    {
        $isOptionFound = false;
        foreach ($options as $option) {
            if (
                isset($option['type']) && 
                isset($option['source_code']) && 
                $option['type'] === 'source' && 
                $option['source_code'] === $sourceCode
            ) {
                $isOptionFound = true;
                break;
            }
        }
        if ($isOptionFound) {
            return $options;
        }
        $options[] = [
            'label' => __('Source'),
            'value' => $sourceName,
            'type' => 'source',
            'source_code' => $sourceCode,
        ];
        return $options;
    }
}