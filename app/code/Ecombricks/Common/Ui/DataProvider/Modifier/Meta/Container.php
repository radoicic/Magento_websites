<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Ui\DataProvider\Modifier\Meta;

/**
 * Container UI data provider meta
 */
class Container extends \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Component
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
                    'componentType' => \Magento\Ui\Component\Container::NAME,
                    'formElement' => \Magento\Ui\Component\Container::NAME,
                    'breakLine' => 'false',
                ],
                $config
            ),
            $children
        );
    }
    
}