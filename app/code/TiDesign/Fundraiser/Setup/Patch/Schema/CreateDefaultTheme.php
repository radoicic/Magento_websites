<?php

namespace TiDesign\Fundraiser\Setup\Patch\Schema;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use TiDesign\Fundraiser\Model\Theme\GetByThemeId;
use TiDesign\Fundraiser\Model\Theme\OptionSource\AnchorTypes;
use TiDesign\Fundraiser\Model\Theme\OptionSource\EditorTypes;
use TiDesign\Fundraiser\Model\Theme\SaveTheme;

class CreateDefaultTheme implements SchemaPatchInterface
{
    const DEFAULT_THEME_ID = 'Theme1';

    /**
     * @param SchemaSetupInterface $schemaSetup
     * @param GetByThemeId $getByThemeId
     * @param SaveTheme $saveTheme
     * @param Json $serializer
     */
    public function __construct(
        protected SchemaSetupInterface $schemaSetup,
        protected GetByThemeId $getByThemeId,
        protected SaveTheme $saveTheme,
        protected Json $serializer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();

        $theme = $this->getByThemeId->execute(self::DEFAULT_THEME_ID);

        if (!$theme->getId()) {
            $theme->setThemeId(self::DEFAULT_THEME_ID);
            $theme->setThemeName(self::DEFAULT_THEME_ID);
            $theme->setConfigData($this->getDefaultThemeConfigData());

            $this->saveTheme->execute($theme);
        }

        $this->schemaSetup->endSetup();
    }

    /**
     * @return bool|string
     */
    private function getDefaultThemeConfigData()
    {
        $anchorsData = [
            'anchors' => $this->getDefaultAnchorsData(),
            'editors' => $this->getDefaultEditorsData()
        ];
        return $this->serializer->serialize($anchorsData);
    }

    /**
     * @return array[]
     */
    private function getDefaultAnchorsData()
    {
        $data = [
            ['#btn-1', AnchorTypes::TYPE_DEFAULT, '.theme-container .theme-header'],
            ['#btn-2', AnchorTypes::TYPE_AREA, '.theme-header-logo img'],
            ['#btn-3', AnchorTypes::TYPE_DEFAULT, '.theme-header-title'],
            ['#btn-3-1', AnchorTypes::TYPE_DEFAULT, '.theme-header-title'],
            ['#btn-4', AnchorTypes::TYPE_POINT, '.theme-header-title span'],
            ['#btn-5', AnchorTypes::TYPE_AREA, '.body-right img'],
            ['#btn-6', AnchorTypes::TYPE_POINT, '.body-left > div:nth-of-type(1) > span'],
            ['#btn-7', AnchorTypes::TYPE_POINT, '.body-left > div:nth-of-type(4) > span:nth-of-type(n+2)'],
            ['#btn-8', AnchorTypes::TYPE_POINT, '.body-left > div:nth-of-type(5) > span:nth-of-type(5)'],
            ['#btn-9', AnchorTypes::TYPE_POINT, '.body-left > div:nth-of-type(6) > span:nth-of-type(even)'],
            ['#btn-10', AnchorTypes::TYPE_POINT, '.body-left > div.text.date > span'],
        ];
        return array_map(function ($data) {
            [$selector, $type, $targetSelector] = $data;
            return ['selector' => $selector, 'type' => $type, 'target_selector' => $targetSelector];
        }, $data);
    }

    /**
     * @return array[]
     */
    private function getDefaultEditorsData()
    {
        return [
            'editor_01' => [
                'type' => EditorTypes::TYPE_COLOR_PICKER,
                'selector' => '#btn-1',
                'target_selector' => '.theme-container .theme-header',
                'css_attribute' => 'background-color',
                'following_selectors' => [
                    [
                        'target_selector' => '.theme-container .theme-footer svg path',
                        'css_attribute' => 'fill'
                    ]
                ]
            ],
            'editor_02' => [
                'type' => EditorTypes::TYPE_IMAGE_UPLOADER,
                'selector' => '#btn-2',
                'target_selector' => '.theme-header-logo > img',
                'max_file_size' => 2,
                'allow_file_extensions' => ['jpg', 'jpeg', 'png']
            ],
            'editor_03' => [
                'type' => EditorTypes::TYPE_COLOR_PICKER,
                'selector' => '#btn-3',
                'target_selector' => '.theme-container .theme-header .theme-header-title svg path',
                'css_attribute' => 'fill'
            ],
            'editor_03-1' => [
                'type' => EditorTypes::TYPE_COLOR_PICKER,
                'selector' => '#btn-3-1',
                'target_selector' => '.theme-container .theme-header .theme-header-title span',
                'css_attribute' => 'color'
            ],
            'editor_04' => [
                'type' => EditorTypes::TYPE_TEXT_EDITOR,
                'selector' => '#btn-4',
                'target_selectors' => '.theme-container .theme-header .theme-header-title span',
                'attributes' => [
                    [
                        'maxlength' => 18
                    ],
                    [
                        'maxlength' => 18
                    ]
                ]
            ],
            'editor_05' => [
                'type' => EditorTypes::TYPE_IMAGE_UPLOADER,
                'selector' => '#btn-5',
                'target_selector' => '.theme-body > .body-right > img',
                'max_file_size' => 2,
                'allow_file_extensions' => ['jpg', 'jpeg', 'png']
            ],
            'editor_06' => [
                'type' => EditorTypes::TYPE_TEXT_EDITOR,
                'selector' => '#btn-6',
                'target_selectors' => '.body-left > div:nth-of-type(1) > span',
                'attributes' => [
                    [
                        'maxlength' => 50
                    ],
                    [
                        'maxlength' => 50
                    ]
                ]
            ],
            'editor_07' => [
                'type' => EditorTypes::TYPE_TEXT_EDITOR,
                'selector' => '#btn-7',
                'target_selectors' => '.body-left > div:nth-of-type(4) > span:nth-of-type(n+2)',
                'attributes' => [
                    [
                        'maxlength' => 32
                    ],
                    [
                        'maxlength' => 50
                    ]
                ]
            ],
            'editor_08' => [
                'type' => EditorTypes::TYPE_TEXT_EDITOR,
                'selector' => '#btn-8',
                'target_selectors' => '.body-left > div:nth-child(5) > span:nth-of-type(5)',
                'attributes' => [
                    [
                        'maxlength' => 30
                    ]
                ]
            ],
            'editor_09' => [
                'type' => EditorTypes::TYPE_TEXT_EDITOR,
                'selector' => '#btn-9',
                'target_selectors' => '.body-left > div:nth-child(6) > span:nth-of-type(even)',
                'attributes' => [
                    [
                        'maxlength' => 16
                    ],
                    [
                        'maxlength' => 16
                    ],
                    [
                        'maxlength' => 50
                    ],
                ]
            ],
            'editor_10' => [
                'type' => EditorTypes::TYPE_TEXT_EDITOR,
                'selector' => '#btn-10',
                'target_selectors' => '.body-left > div.text.date > span',
                'attributes' => [
                    [
                        'maxlength' => 36
                    ]
                ]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
