<?php
/*
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright(c)Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FacebookShop\Model\Attribute\Backend;

class ValidateFbClass extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        $this->validateFbAttributes($object);

        return parent::beforeSave($object);
    }

    /**
     * Validate fb attributes
     *
     * @param \Magento\Framework\DataObject $object
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateFbAttributes($object)
    {
        /** @var string $attributeCode */
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $isFacebookProduct = $object->getData('is_facebook_product');
        if (empty($isFacebookProduct)) {
            return true;
        }
        $validateAttributes = ['google_product_category','fb_product_brand'];
        if (in_array($attributeCode, $validateAttributes)) {
            $value = $object->getData($attributeCode);
            if (strpos($value, '<script>') !== false) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The value of attribute "%1" is invalid', $attributeCode)
                );
            }
            if (empty($value)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The value of attribute "%1" is required', $attributeCode)
                );
            }

        }
        return true;
    }
}
