<?php
namespace TiDesign\Permissions\Plugin;

use Magento\Catalog\Ui\Component\Product\MassAction as ProductMassAction;
use Magento\Framework\AuthorizationInterface;

class MassAction
{
    private $authorization;

    public function __construct(
        AuthorizationInterface $authorization
    ) {
        $this->authorization = $authorization;
    }
	
    public function afterIsActionAllowed(ProductMassAction $subject, $result, $actionType)
    {
		if ($actionType == 'delete'){
			$isAllowed2 = $this->authorization->isAllowed('TiDesign_Permissions::delete_product');
			return $isAllowed2;
		}
		return $result;
    }
}