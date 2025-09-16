<?php
namespace Amasty\Pgrid\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentInterface;

class InlineEditUpdater
{

    /**
     * @var string[]
     */
    protected $editorTypes = [
        'textarea' => 'textarea',
        'text' => 'text',
        'weight' => 'text',
        'price' => 'text',
        'date' => 'date',
        'select' => 'select',
        'boolean' => 'select',
        'multiselect' => 'multiselect'
    ];

    /**
     * @var string[]
     */
    protected $validationRules = [
        'weight' => 'validate-zero-or-greater',
        'price' => 'validate-zero-or-greater'
    ];

    /**
     * Add editor config
     *
     * @param UiComponentInterface $column
     * @param string $frontendInput
     * @param array $validationRules
     * @param bool|false $isRequired
     * @return UiComponentInterface
     */
    public function applyEditing(
        UiComponentInterface $column,
        $frontendInput,
        $frontendClass,
        $isRequired = false
    ) {
        if (array_key_exists($frontendInput, $this->editorTypes)) {
            $config = $column->getConfiguration();
            $editorType = $this->editorTypes[$frontendInput];

            if (!(isset($config['editor']) && isset($config['editor']['editorType']))) {
                $config['editor'] = [
                    'editorType' => $editorType
                ];
            }

            $validationRules = $this->validationRules[$frontendInput] ?? [];

            if (!empty($config['editor']['validation'])) {
                $validationRules = array_merge($config['editor']['validation'], $validationRules);
            }

            $config['editor']['validation'] = $validationRules;

            $column->setData('config', $config);
        }
        return $column;
    }
}
