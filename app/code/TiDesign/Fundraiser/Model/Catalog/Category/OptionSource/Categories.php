<?php

namespace TiDesign\Fundraiser\Model\Catalog\Category\OptionSource;

use Magento\Catalog\Ui\Component\Product\Form\Categories\Options as CategoriesOptions;
use Magento\Framework\Data\OptionSourceInterface;

class Categories implements OptionSourceInterface
{
    protected $options = null;

    /**
     * @param CategoriesOptions $categoryOptions
     */
    public function __construct(
        protected CategoriesOptions $categoryOptions
    ) {
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $options = $this->categoryOptions->toOptionArray();
            $this->options = $this->toFlatArray($options);
        }
        return $this->options;
    }

    /**
     * @param array $options
     * @return array
     */
    private function toFlatArray($options)
    {
        $result = [];
        foreach ($options as $option) {
            $result[$option['value']] = ['value' => $option['value'], 'label' => $option['label']];
            if (!empty($option['optgroup'])) {
                $result = $result + $this->toFlatArray($option['optgroup']);
            }
        }
        return $result;
    }
}
