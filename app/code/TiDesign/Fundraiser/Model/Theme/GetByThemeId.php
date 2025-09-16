<?php

namespace TiDesign\Fundraiser\Model\Theme;

use TiDesign\Fundraiser\Model\ResourceModel\Theme as ThemeResource;
use TiDesign\Fundraiser\Model\ThemeFactory;

class GetByThemeId
{
    protected $themeIdsToTheme = [];

    /**
     * @param ThemeFactory $themeFactory
     * @param ThemeResource $themeResource
     */
    public function __construct(
        protected ThemeFactory $themeFactory,
        protected ThemeResource $themeResource
    ) {
    }

    /**
     * @param $themeId
     * @return \TiDesign\Fundraiser\Model\Theme
     */
    public function execute($themeId)
    {
        if (!isset($this->themeIdsToTheme[$themeId])) {
            $model = $this->themeFactory->create();
            $this->themeResource->load($model, $themeId, 'theme_id');
            $this->themeIdsToTheme[$themeId] = $model;
        }
        return $this->themeIdsToTheme[$themeId];
    }
}
