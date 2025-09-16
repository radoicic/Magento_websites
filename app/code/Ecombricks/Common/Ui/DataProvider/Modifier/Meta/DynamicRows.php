<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\Common\Ui\DataProvider\Modifier\Meta;

/**
 * Dynamic rows UI data provider meta
 */
class DynamicRows
{
    
    /**
     * Component
     * 
     * @var \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Component
     */
    protected $component;
    
    /**
     * Container
     * 
     * @var \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Container
     */
    protected $container;
    
    /**
     * Constructor
     * 
     * @param \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Component $component
     * @param \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Container $container
     * @return void
     */
    public function __construct(
        \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Component $component,
        \Ecombricks\Common\Ui\DataProvider\Modifier\Meta\Container $container
    )
    {
        $this->component = $component;
        $this->container = $container;
    }
    
    /**
     * Create record meta
     * 
     * @param array $columns
     * @param bool $actionDelete
     * @return array
     */
    protected function createRecord(array $columns, bool $actionDelete): array
    {
        $children = [];
        foreach ($columns as $columnName => $column) {
            if (!empty($column['config']) && !empty($column['children'])) {
                $children[$columnName] = $this->container->create(
                    array_merge(
                        [
                            'showLabel' => false,
                            'component' => 'Magento_Ui/js/form/components/group',
                            'additionalClasses' => 'admin__field-container_'.$columnName,
                            
                        ],
                        $column['config']
                    ),
                    $column['children']
                );
                continue;
            }
            $children[$columnName] = $column;
        }
        if ($actionDelete) {
            $children['action_delete'] = $this->component->create(
                [
                    'componentType' => 'actionDelete',
                    'label' => null,
                    'fit' => true,
                ]
            );
        }
        return $this->component->create(
            [
                'componentType' => \Magento\Ui\Component\Container::NAME,
                'isTemplate' => true,
                'is_collection' => true,
                'component' => 'Magento_Ui/js/dynamic-rows/record',
                'dataScope' => '',
            ],
            $children
        );
    }
    
    /**
     * Create
     * 
     * @param array $config
     * @param array $columns
     * @param bool $actionDelete
     * @return array
     */
    public function create(array $config, array $columns, bool $actionDelete = true): array
    {
        return $this->component->create(
            array_merge(
                [
                    'componentType' => \Magento\Ui\Component\DynamicRows::NAME,
                    'itemTemplate' => 'record',
                    'renderDefaultRecord' => false,
                    'columnsHeader' => true,
                    'additionalClasses' => 'admin__field-wide',
                    'deleteProperty'=> 'is_delete',
                    'deleteValue' => '1',
                ],
                $config
            ),
            [ 'record' => $this->createRecord($columns, $actionDelete), ]
        );
    }
    
}