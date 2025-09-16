<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Block\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

class GenerateCsv extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * button template constant
     */
    const BUTTON_TEMPLATE = 'system/config/button/generatecsv.phtml';

    /**
     * Set template to itself.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }

    /**
     * Render button.
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return ajax url for button.
     * @return string
     */
    public function getAjaxCheckUrl()
    {
        return $this->getUrl('facebookshop/csv/export');
    }

    /**
     * Get the button and scripts contents.
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->addData(
            [
                'id' => 'generate_csv_button',
                'button_label' => __('Generate Csv Manually'),
                'onclick' => 'javascript:check(); return false;',
            ]
        );
        return $this->_toHtml();
    }
}
