<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c)Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Block\Adminhtml\System\Config\Form\Field;

class Link extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * render html element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return void
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * return html for links
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return void
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return sprintf(
            '<a href ="%s" target="_blank" >%s</a>',
            rtrim('https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt'),
            __('Google Taxonomy With Id(s)')
        );
    }
}
