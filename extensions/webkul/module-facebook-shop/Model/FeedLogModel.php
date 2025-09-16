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
use Webkul\FacebookShop\Api\Data\FacebookShopFeedLogModelInterface;
use Magento\Framework\DataObject\IdentityInterface;

class FeedLogModel extends AbstractModel implements FacebookShopFeedLogModelInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Facebook Shop Csv Logs cache tag.
     */
    const CACHE_TAG = 'wk_facebookshop_csvlogs';

    /**
     * @var string
     */
    protected $_cacheTag = 'wk_facebookshop_csvlogs';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'wk_facebookshop_csvlogs';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init(\Webkul\FacebookShop\Model\ResourceModel\FeedLogModel::class);
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
     * @return \Webkul\FacebookShop\Model\FeedLogModel
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
     * @return \Webkul\FacebookShop\Api\Data\FacebookShopFeedLogModelInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
