<?php

namespace Meetanshi\Callforprice\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Apiprovider
 */
class Apiprovider implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'msg91', 'label' => __('Msg91')],
            ['value' => 'textlocal', 'label' => __('Text Local')],
            ['value' => 'twilio', 'label' => __('Twilio')],
            ['value' => 'twilio_whatsapp', 'label' => __('Twilio WhatsApp')],
            ['value' => 'other', 'label' => __('Other')]
        ];
    }
}
