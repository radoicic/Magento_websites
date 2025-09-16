<?php

namespace TiDesign\Fundraiser\Model\ThemeCustomer\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const PENDING_APPROVAL = 'pending_approval';
    const APPROVED = 'approved';

    const EXPIRED = 'expired';

    public function toOptionArray()
    {
        return [
            [
                'value' => self::PENDING_APPROVAL,
                'label' => __('PENDING APPROVAL')
            ],
            [
                'value' => self::APPROVED,
                'label' => 'APPROVED'
            ],
            [
                'value' => self::EXPIRED,
                'label' => 'EXPIRED'
            ],
        ];
    }
}
