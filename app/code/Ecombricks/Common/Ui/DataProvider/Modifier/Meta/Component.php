<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Ui\DataProvider\Modifier\Meta;

/**
 * Component UI data provider meta
 */
class Component
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
        $meta = [
            'arguments' => [
                'data' => [
                    'config' => $config,
                ],
            ],
        ];
        if (!empty($children)) {
            $meta['children'] = $children;
        }
        return $meta;
    }
    
}