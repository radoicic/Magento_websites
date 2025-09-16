<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Field;

/**
 * Boolean field UI data provider meta
 */
class BooleanField extends \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Field
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
                    'formElement' => \Magento\Ui\Component\Form\Element\Checkbox::NAME,
                    'dataType' => \Magento\Ui\Component\Form\Element\DataType\Number::NAME,
                    'prefer' => 'toggle',
                    'valueMap' => [ 'false' => '0', 'true' => '1', ],
                ],
                $config
            ),
            $children
        );
    }
    
}