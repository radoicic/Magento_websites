<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Model\Mview\View;

use Magento\Framework\Mview\View\Subscription as MviewSubscription;

class Subscription extends MviewSubscription
{
    public function create(bool $save = true): Subscription
    {
        if ($this->isSubscriptionTableExist()) {
            parent::create($save);
        }

        return $this;
    }

    public function remove(): Subscription
    {
        if ($this->isSubscriptionTableExist()) {
            parent::remove();
        }

        return $this;
    }

    public function isSubscriptionTableExist(): bool
    {
        return $this->resource->getConnection()->isTableExists($this->resource->getTableName($this->tableName));
    }
}
