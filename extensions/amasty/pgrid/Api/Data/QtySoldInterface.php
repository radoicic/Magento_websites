<?php

namespace Amasty\Pgrid\Api\Data;

/**#@+
 * Constants defined for keys of data array
 */
interface QtySoldInterface
{
    public const QTY_SOLD_TABLE = 'amasty_pgrid_qty_sold';
    public const PRODUCT_ID = 'product_id';
    public const QTY_SOLD = 'qty_sold';

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @return int
     */
    public function getQtySold();

    /**
     * @param int $productId
     * @return \Amasty\Pgrid\Api\Data\QtySoldInterface
     */
    public function setProductId($productId);

    /**
     * @param int $qtySold
     * @return \Amasty\Pgrid\Api\Data\QtySoldInterface
     */
    public function setQtySold($qtySold);
}
