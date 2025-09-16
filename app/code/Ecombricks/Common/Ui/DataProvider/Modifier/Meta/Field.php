<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Ui\DataProvider\Modifier\Meta;

/**
 * Field UI data provider meta
 */
class Field extends \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Component
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
                    'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                    'dataType' => \Magento\Ui\Component\Form\Element\DataType\Text::NAME,
                ],
                $config
            ),
            $children
        );
    }
    
}