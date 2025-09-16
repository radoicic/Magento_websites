<?php

namespace Meetanshi\Callforprice\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class NotificationType
 */
class NotificationType implements ArrayInterface
{
    const NOTIFICATION_TYPE_EMAIL=1;
    const NOTIFICATION_TYPE_SMS=2;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::NOTIFICATION_TYPE_EMAIL => __('Email'),
            self::NOTIFICATION_TYPE_SMS => __('SMS/Whatsapp'),
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionsArray = [];
        foreach ($this->toArray() as $key => $value) {
            $optionsArray[] = ['value' => $key, 'label' => __($value)];
        }
        return $optionsArray;
    }
}
