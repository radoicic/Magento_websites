<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Plugin\Block\Config\System\Config\Form;

/**
 * Configuration form field plugin
 */
class Field
{
    /**
     * Current source provider
     * 
     * @var \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface
     */
    private $currentSourceProvider;

    /**
     * Product metadata
     * 
     * @var \Magento\Framework\App\ProductMetadata
     */
    private $productMetadata;

    /**
     * Secure renderer
     * 
     * @var \Magento\Framework\View\Helper\SecureHtmlRenderer
     */
    private $secureRenderer;

    /**
     * Constructor
     * 
     * @param \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @return void
     */
    public function __construct(
        \Ecombricks\InventoryCommon\Api\CurrentSourceProviderInterface $currentSourceProvider,
        \Magento\Framework\App\ProductMetadata $productMetadata
    )
    {
        $this->currentSourceProvider = $currentSourceProvider;
        $this->productMetadata = $productMetadata;
        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->secureRenderer = $objectManager->get(\Magento\Framework\View\Helper\SecureHtmlRenderer::class);
        }
    }

    /**
     * Around render
     * 
     * @param \Magento\Config\Block\System\Config\Form\Field $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function aroundRender(
        \Magento\Config\Block\System\Config\Form\Field $subject,
        \Closure $proceed,
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    )
    {
        $this->beforeRender($element);
        $html = (string) $proceed($element);
        return $this->afterRender($html, $element);
    }

    /**
     * Check if inheritance checkbox is required
     * 
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return bool
     */
    private function isInheritCheckboxRequired(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return 
            $this->currentSourceProvider->getSourceCode() || 
            $element->getCanUseWebsiteValue() || 
            $element->getCanUseDefaultValue() || 
            $element->getCanRestoreToDefault();
    }
    
    /**
     * Get inheritance checkbox label
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    private function getInheritCheckboxLabel(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($this->currentSourceProvider->getSourceCode()) {
            $checkboxLabel  = __('Use default value');
            return $checkboxLabel;
        }
        $checkboxLabel = __('Use system value');
        if ($element->getCanUseDefaultValue()) {
            $checkboxLabel = __('Use Default');
        }
        if ($element->getCanUseWebsiteValue()) {
            $checkboxLabel = __('Use Website');
        }
        return $checkboxLabel;
    }
    
    /**
     * Remove use default cell
     * 
     * @param string $html
     * @return string
     */
    private function removeUseDefaultCell(string $html)
    {
        $openTag = '<td class="use-default"';
        $closeTag = '</td>';
        $openTagPos = strpos($html, $openTag);
        if ($openTagPos === false) {
            return $html;
        }
        $closeTagPos = strpos($html, $closeTag, $openTagPos);
        if ($closeTagPos === false) {
            return $html;
        }
        return substr($html, 0, $openTagPos).substr($html, $closeTagPos + strlen($closeTag));
    }
    
    /**
     * Render inheritance checkbox
     * 
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    private function renderInheritCheckbox(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $htmlId = $element->getHtmlId();
        $namePrefix = preg_replace('#\[value\](\[\])?$#', '', $element->getName());
        $checkedHtml = $element->getInherit() == 1 ? 'checked="checked"' : '';
        $disabled = $element->getIsDisableInheritance() == true ? ' disabled="disabled" readonly="1"' : '';
        $html = '<td class="use-default">';
        $html .= '<input id="'.$htmlId.'_inherit" name="'.$namePrefix;
        $html .= '[inherit]" type="checkbox" value="1" class="checkbox config-inherit" '.$checkedHtml.$disabled;
        if (version_compare($this->productMetadata->getVersion(), '2.4.0', '>=')) {
            $html .= '/>';
            $html .= $this->secureRenderer->renderEventListenerAsTag(
                'onclick',
                "toggleValueElements(this, Element.previous(this.parentNode))",
                'input#'.$htmlId.'_inherit'
            );
        } else {
            $html .= ' onclick="toggleValueElements(this, Element.previous(this.parentNode))"/> ';
        }
        $html .= '<label for="'.$htmlId.'_inherit" class="inherit">'.$this->getInheritCheckboxLabel($element).'</label>';
        $html .= '</td>';
        return $html;
    }
    
    /**
     * Replace inherit checkbox
     * 
     * @param string $html
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    private function replaceInheritCheckbox(string $html, \Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $this->removeUseDefaultCell($html);
        $newCheckboxHtml = ($this->isInheritCheckboxRequired($element)) ? $this->renderInheritCheckbox($element) : '';
        $hintPos = strpos($html, '<td class="">');
        if ($hintPos === false) {
            return $html;
        }
        return substr($html, 0, $hintPos).$newCheckboxHtml.substr($html, $hintPos);
    }
    
    /**
     * Before render
     * 
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return $this
     */
    private function beforeRender(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getInherit() == 1 && $this->isInheritCheckboxRequired($element)) {
            $element->setDisabled(true);
        }
        return $this;
    }
    
    /**
     * After render
     * 
     * @param string $html
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    private function afterRender(string $html, \Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (
            $element->getInherit() == 1 && 
            $this->isInheritCheckboxRequired($element)
        ) {
            $element->setDisabled(true);
        }
        return $this->replaceInheritCheckbox($html, $element);
    }
}