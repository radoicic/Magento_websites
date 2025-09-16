<?php
namespace Amasty\Pgrid\Model\Product;

use Magento\Framework\Data\OptionSourceInterface;

class Lowstock extends \Magento\Framework\DataObject implements OptionSourceInterface
{
    public const YES = 1;
    public const NO = 0;

    public function getOptionArray()
    {
        return [
            self::YES => __('Yes'),
            self::NO => __('No')
        ];
    }

    public function getAllOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
