<?php

namespace TiDesign\Fundraiser\Model\Theme;

use TiDesign\Fundraiser\Model\ResourceModel\Theme as ThemeResource;
use TiDesign\Fundraiser\Model\ThemeCustomer\OptionSource\Status;

class SaveTheme
{
    /**
     * @param ThemeResource $themeResource
     */
    public function __construct(
        protected ThemeResource $themeResource
    ) {
    }

    /**
     * @param \TiDesign\Fundraiser\Model\Theme $theme
     * @return TiDesign\Fundraiser\Model\Theme
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute($theme)
    {
        if (!$theme->getStatus()) {
            $theme->setStatus(Status::PENDING_APPROVAL);
        }
        $this->themeResource->save($theme);
        return $theme;
    }
}
