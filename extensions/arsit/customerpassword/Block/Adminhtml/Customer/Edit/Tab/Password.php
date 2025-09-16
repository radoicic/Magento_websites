<?php
/**
 * Arsit_CustomerPassword
 *
 * @category    Arsit
 * @package     Arsit_CustomerPassword
 * @copyright   Copyright (c) 2016 arsit.ru
 * @author      developer@arsit.ru
 */

namespace Arsit\CustomerPassword\Block\Adminhtml\Customer\Edit\Tab;

use \Magento\Backend\Block\Template\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Data\FormFactory;
use \Arsit\CustomerPassword\Helper\Data;

/**
 * Customer account form block
 */
class Password extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{

    /**
     * @var \Arsit\CustomerPassword\Helper\Data
     */
    protected $customerPasswordData;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Data $customerPasswordData,
        array $data = []
    ) {
        $this->customerPasswordData = $customerPasswordData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Set Password');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Set Password');
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Initialize the form.
     *
     * @return $this
     */
    public function initForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }

        /**@var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_' . $this->customerPasswordData->getElementKey());

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Set Password')]);

        $fieldset->addField(
            $this->customerPasswordData->getElementKey(),
            'text',
            [
                'label' => __('Set Password'),
                'name' => $this->customerPasswordData->getElementKey(),
                'data-form-part' => $this->getData('target_form')
            ]
        );

        $this->setForm($form);
        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->initForm();
        return parent::_toHtml();
    }
}
