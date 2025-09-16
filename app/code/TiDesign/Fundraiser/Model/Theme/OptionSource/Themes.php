<?php

namespace TiDesign\Fundraiser\Model\Theme\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use TiDesign\Fundraiser\Model\ResourceModel\Theme\CollectionFactory;

class Themes implements OptionSourceInterface
{
    protected $options = [];

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        protected CollectionFactory $collectionFactory
    ) {
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options) {
            return $this->options;
        }
        /** @var \TiDesign\Fundraiser\Model\ResourceModel\Theme\Collection $themes */
        $themes = $this->collectionFactory->create();
        foreach ($themes as $theme) {
            $this->options[] = ['value' => $theme->getThemeId(), 'label' => $theme->getThemeName()];
        }
        return $this->options;
    }
}
