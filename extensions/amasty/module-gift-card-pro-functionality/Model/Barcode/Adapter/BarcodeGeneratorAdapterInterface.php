<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Gift Card Pro Functionality for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\GiftCardProFunctionality\Model\Barcode\Adapter;

interface BarcodeGeneratorAdapterInterface
{
    /**
     * Return an HTML representation of the barcode containing the gift card code.
     *
     * @param string $giftcardCode
     * @return string
     * @throws \RuntimeException
     */
    public function getBarcode(string $giftcardCode): string;
}
