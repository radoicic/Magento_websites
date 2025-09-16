<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Ui\Component\Listing\Column;

use Amasty\Pgrid\Model\ThirdParty\ModuleChecker;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class QtyPerSource extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var ModuleChecker
     */
    private $moduleChecker;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ModuleChecker $moduleChecker,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->moduleChecker = $moduleChecker;
    }

    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        if (!$this->moduleChecker->isInventoryEnabled()) {
            $config['componentDisabled'] = true;
        }
        $this->setData('config', $config);
    }
}
