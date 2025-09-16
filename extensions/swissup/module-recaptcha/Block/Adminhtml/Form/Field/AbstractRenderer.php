<?php

namespace Swissup\Recaptcha\Block\Adminhtml\Form\Field;

abstract class AbstractRenderer extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @param \Magento\Framework\View\Element\Context  $context
     * @param \Magento\Framework\Option\ArrayInterface $source
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Option\ArrayInterface $source,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->source = $source;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setInputName($name)
    {
        return $this->setName($name);
    }

    public function setInputId($id)
    {
        return $this->setId($id);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->source->toOptionArray() as $option) {
                $this->addOption($option['value'], addslashes($option['label']));
            }
        }

        return parent::_toHtml();
    }
}
