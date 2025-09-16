<?php

namespace Swissup\DeliveryDate\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Swissup\DeliveryDate\Model\ResourceModel\Deliverydate\CollectionFactory
     */
    protected $deliverydateCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $configValueFactory;

    /**
     * @var \Magento\Config\Model\Config\Backend\Serialized\ArraySerializedFactory
     */
    protected $serializedArrayFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serialize;

    /**
     * @param \Swissup\DeliveryDate\Model\ResourceModel\Deliverydate\CollectionFactory $deliverydateCollectionFactory
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Config\Model\Config\Backend\Serialized\ArraySerializedFactory $serializedArrayFactory
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     */
    public function __construct(
        \Swissup\DeliveryDate\Model\ResourceModel\Deliverydate\CollectionFactory $deliverydateCollectionFactory,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Config\Model\Config\Backend\Serialized\ArraySerializedFactory $serializedArrayFactory,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize
    ) {
        $this->deliverydateCollectionFactory = $deliverydateCollectionFactory;
        $this->configValueFactory = $configValueFactory;
        $this->serializedArrayFactory = $serializedArrayFactory;
        $this->serialize = $serialize;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $collection = $this->deliverydateCollectionFactory->create()
                ->addFieldToFilter('date', '0000-00-00 00:00:00');

            foreach ($collection as $item) {
                $item->setDate(null)->save();
            }
        }

        if (version_compare($context->getVersion(), '1.3.1', '<')) {
            $collection = $this->configValueFactory->create()->getCollection()
                ->addFieldToFilter('path', 'delivery_date/exclude/holidays');

            foreach ($collection as $config) {
                if (!$config->getValue()) {
                    continue;
                }

                $raw = $this->serialize->unserialize($config->getValue());
                $model = $this->serializedArrayFactory->create()->setValue($raw);
                $model->beforeSave();

                $config->setValue($model->getValue())->save();
            }
        }

        $setup->endSetup();
    }
}
