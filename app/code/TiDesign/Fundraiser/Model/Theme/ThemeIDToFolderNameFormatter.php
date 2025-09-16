<?php

namespace TiDesign\Fundraiser\Model\Theme;

class ThemeIDToFolderNameFormatter
{
    /**
     * @param string $themeId
     * @return string
     */
    public function format($themeId)
    {
        $result = preg_replace('/[^a-zA-Z0-9 ]/', '', $themeId);
        $result = str_replace(' ', '_', $result);
        return strtolower($result);
    }
}
