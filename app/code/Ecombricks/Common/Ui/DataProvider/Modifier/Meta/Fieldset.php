<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Ui\DataProvider\Modifier\Meta;

/**
 * Field set UI data provider meta
 */
class Fieldset extends \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Component
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
                    'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                    'label' => '',
                    'collapsible' => true,
                    'opened' => false,
                    'visible' => true,
                    'sortOrder' => 0,
                ],
                $config
            ),
            $children
        );
    }
    
}