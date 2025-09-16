<?php

declare(strict_types=1);

namespace Amasty\GiftCardGraphQl\Model\Image;

use Amasty\GiftCardGraphQl\Api\Data\ImageContentInterface;
use Magento\Framework\DataObject;

class Content extends DataObject implements ImageContentInterface
{
    public function getBase64EncodedData(): string
    {
        return (string)$this->_getData(ImageContentInterface::BASE64_ENCODED_DATA);
    }

    public function setBase64EncodedData(string $base64EncodedData): ImageContentInterface
    {
        $this->setData(ImageContentInterface::BASE64_ENCODED_DATA, $base64EncodedData);

        return $this;
    }

    public function setExtension(string $extension): ImageContentInterface
    {
        $this->setData(ImageContentInterface::EXTENSION, $extension);

        return $this;
    }

    public function getExtension(): string
    {
        return (string)$this->_getData(ImageContentInterface::EXTENSION);
    }

    public function getFileNameWithExtension(): string
    {
        return (string)$this->_getData(ImageContentInterface::FILENAME_WITH_EXTENSION);
    }

    public function setFileNameWithExtension(string $filenameWithExtension): ImageContentInterface
    {
        $this->setData(ImageContentInterface::FILENAME_WITH_EXTENSION, $filenameWithExtension);

        return $this;
    }
}
