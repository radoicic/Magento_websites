<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Field;

/**
 * File field UI data provider meta
 */
class FileField extends \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Component
{
    
    /**
     * Create
     * 
     * @param array $config
     * @param array $children
     * @return array
     */
    public function create(array $config = [], array $children = []): array
    {
        return parent::create(
            array_merge(
                [
                    //'formElement' => 'fileUploader',
                    //'componentType' => 'fileUploader',
                    'formElement' => 'input',
                    'componentType' => 'field',
                    'component' => 'Ecombricks_Common/js/components/file-uploader',
                    'elementTmpl' => 'Ecombricks_Common/components/file-uploader',
                ],
                $config
            ),
            $children
        );
    }
    
}