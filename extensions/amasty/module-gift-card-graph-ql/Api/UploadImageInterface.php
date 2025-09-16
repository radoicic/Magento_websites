<?php

namespace Amasty\GiftCardGraphQl\Api;

interface UploadImageInterface
{
    /**
     * Upload File
     *
     * @param \Amasty\GiftCardGraphQl\Api\Data\ImageContentInterface $imageContent
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Amasty\GiftCardGraphQl\Api\Data\ImageContentInterface
     */
    public function upload(\Amasty\GiftCardGraphQl\Api\Data\ImageContentInterface $imageContent);
}
