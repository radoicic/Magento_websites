<?php

namespace Swissup\Firecheckout\Helper\Config;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Swissup\Firecheckout\Model\Config\Source\AgreementsPosition;

class Agreements extends AbstractHelper
{
    /**
     * @var string
     */
    const CONFIG_PATH_TITLE = 'firecheckout/agreements/title';

    /**
     * @var string
     */
    const CONFIG_PATH_POSITION = 'firecheckout/agreements/position';

    /**
     * @var \Swissup\Firecheckout\Helper\Data $firecheckoutHelper
     */
    private $firecheckoutHelper;

    /**
     * @var \TiDesign\Consignment\Helper\Consignment $consignmetHelper
     */
    private $consignmetHelper;

    /**
     * @param Context $context
     * \Swissup\Firecheckout\Helper\Data $firecheckoutHelper
     */
    public function __construct(
        Context $context,
        \Swissup\Firecheckout\Helper\Data $firecheckoutHelper,
        \TiDesign\Consignment\Helper\Consignment $consignmetHelper
    ) {
        parent::__construct($context);

        $this->firecheckoutHelper = $firecheckoutHelper;
        $this->consignmetHelper = $consignmetHelper;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->firecheckoutHelper->getConfigValue(self::CONFIG_PATH_POSITION);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->firecheckoutHelper->getConfigValue(self::CONFIG_PATH_TITLE);
    }

    /**
     * @return boolean
     */
    public function isMoverDisabled()
    {
        return $this->getPosition() === AgreementsPosition::VALUE_EMTPY;
    }

    /**
     * @return array
     */
    public function getMoverJsConfig()
    {
        return [
            'title' => $this->getTitle(),
            'position' => $this->getPosition(),
            'display' => $this->consignmetHelper->getDisplayAgreement(),
        ];
    }
}
