<?php
namespace TiDesign\Videobackground\Model\Source\Staticpages;

class Pagelist implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Home page')],
            ['value' => 2, 'label' => __('Checkout')],
            ['value' => 3, 'label' => __('Cart')],
            ['value' => 4, 'label' => __('Contact')],
        ];
    }
}