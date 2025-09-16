<?php

namespace Amasty\GiftCardGraphQl\Api\Data;

interface ImageContentInterface
{
    const BASE64_ENCODED_DATA = 'base64_encoded_data';
    const EXTENSION = 'extension';
    const FILENAME_WITH_EXTENSION = 'name_with_extension';

    /**
     * @return string
     */
    public function getBase64EncodedData(): string;

    /**
     * @param string $base64EncodedData
     *
     * @return ImageContentInterface
     */
    public function setBase64EncodedData(string $base64EncodedData): ImageContentInterface;

    /**
     * @return string
     */
    public function getFileNameWithExtension(): string;

    /**
     * @param string $filenameWithExtension
     *
     * @return ImageContentInterface
     */
    public function setFileNameWithExtension(string $filenameWithExtension): ImageContentInterface;

    /**
     * @param string $extension
     *
     * @return ImageContentInterface
     */
    public function setExtension(string $extension): ImageContentInterface;

    /**
     * @return string
     */
    public function getExtension(): string;
}
