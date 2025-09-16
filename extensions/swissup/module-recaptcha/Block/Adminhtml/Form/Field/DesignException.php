<?php

namespace Swissup\Recaptcha\Block\Adminhtml\Form\Field;

class DesignException extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var CaptchaForm
     */
    protected $_groupRenderer;

    protected $_additionalJs;

    /**
     * Retrieve product attribute column renderer
     *
     * @return CaptchaForm
     */
    protected function _getGroupRenderer()
    {
        if (!$this->_groupRenderer) {
            $this->_groupRenderer = $this->getLayout()->createBlock(
                CaptchaForm::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_groupRenderer->setClass('captcha_form_select');
        }
        return $this->_groupRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'captcha_form',
            ['label' => __('Form'), 'renderer' => $this->_getGroupRenderer()]
        );
        $column = [
            'Type' => Rtype::class,
            'Theme' => Theme::class,
            'Size*' => Size::class,
            'Badge**' => Badge::class
        ];
        foreach ($column as $label => $class) {
            $parts = explode('\\', $class);
            $name = strtolower(end($parts));
            $this->addColumn($name, [
                'label' => __($label),
                'renderer' => $this->getLayout()->createBlock(
                    $class,
                    '',
                    ['data' => ['is_render_to_js_template' => true]]
                )
            ]);
        }
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add design exception');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getGroupRenderer()->calcOptionHash($row->getData('captcha_form'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }

    /**
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = parent::render($element);
        // Do not render label. And make value take two cells.
        $labelHtml = '<td class="label"><label for="' .
            $element->getHtmlId() . '"><span' .
            $this->_renderScopeLabel($element) . '>' .
            $element->getLabel() .
            '</span></label></td>';

        $html = str_replace($labelHtml, '', $html, $count);
        if ($count > 0) {
            $html = str_replace(
                '<td class="value">',
                '<td class="value" colspan="2" style="padding: 2.2rem 2rem 2.2rem 0">',
                $html
            );
        }

        return $html . $this->getToggleJs();
    }

    public function getToggleJS()
    {
        if (!$this->_additionalJs) {
            $this->_additionalJs = $this->getLayout()->createBlock(
                \Magento\Framework\View\Element\Template::class,
                '',
                [
                    'data' => [
                        'template' => 'Swissup_Recaptcha::design-exceptions/additional-js.phtml'
                    ]
                ]
            );
        }

        return $this->_additionalJs->toHtml();
    }
}
