<?php

namespace TiDesign\Fundraiser\Model\Theme;

class CurrentThemeIdStorage
{
    protected $themeId = null;

    public function setThemeId($themeId)
    {
        $this->themeId = $themeId;
    }

    public function getThemeId()
    {
        return $this->themeId;
    }
}
