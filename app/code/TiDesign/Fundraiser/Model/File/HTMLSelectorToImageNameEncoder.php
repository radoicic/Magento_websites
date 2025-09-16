<?php

namespace TiDesign\Fundraiser\Model\File;

class HTMLSelectorToImageNameEncoder
{
    const CHARACTER_REPLACEMENTS = [
        '+' => '_',
        '/' => '-',
        '=' => ''
    ];

    /**
     * @param $htmlSelector
     * @return array|string|string[]
     */
    public function encode($htmlSelector)
    {
        $fileName = base64_encode($htmlSelector);
        foreach (self::CHARACTER_REPLACEMENTS as $key => $value) {
            $fileName = str_replace($key, $value, $fileName);
        }
        return $fileName;
    }
}
