<?php

/**
 * Product:       Xtento_CustomOrderNumber (2.1.7)
 * ID:            ujUJn/Vth6o24dYqG/9u9lsOOEMJEJPHsCxn5DbLMtc=
 * Packaged:      2018-10-11T07:25:23+00:00
 * Last Modified: 2016-01-27T15:35:28+00:00
 * File:          app/code/Xtento/CustomOrderNumber/Observer/SalesOrderShipmentSaveBeforeObserver.php
 * Copyright:     Copyright (c) 2018 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\CustomOrderNumber\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderShipmentSaveBeforeObserver extends AbstractObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->updateIncrementId($observer->getShipment(), self::TYPE_SHIPMENT);
    }

    /**
     * @param $object \Magento\Sales\Model\Order
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
     */
    public function getCollectionForOrder($object)
    {
        return $object->getShipmentsCollection();
    }
}
