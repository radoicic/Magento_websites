<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Controller\Adminhtml\Product\Mui;

use Amasty\Label\Controller\Adminhtml\Label\Edit;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Ui\Controller\Adminhtml\Index\Render as MagentoUiRenderController;

class Render extends MagentoUiRenderController implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Edit::ADMIN_RESOURCE;
}
