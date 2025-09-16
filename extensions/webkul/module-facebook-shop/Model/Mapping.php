<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FacebookShop
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\FacebookShop\Model;

use Magento\Framework\Model\AbstractModel;
use Webkul\FacebookShop\Api\Data\FacebookShopMappingInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * FacebookShop Mapping Model.
 *
 */
class Mapping extends AbstractModel implements FacebookShopMappingInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Facebook Shop cache tag.
     */
    const CACHE_TAG = 'wk_fb_magento_attribute_mapping';

    /**
     * @var string
     */
    protected $_cacheTag = 'wk_fb_magento_attribute_mapping';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'wk_fb_magento_attribute_mapping';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\FacebookShop\Model\ResourceModel\Mapping::class);
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteProduct();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route Product.
     *
     * @return \Webkul\FacebookShop\Model\Mapping
     */
    public function noRouteProduct()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\FacebookShop\Api\Data\FacebookShopMappingInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
