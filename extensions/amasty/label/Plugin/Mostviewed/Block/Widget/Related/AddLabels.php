<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Product Labels for Magento 2
*/

declare(strict_types=1);

namespace Amasty\Label\Plugin\Mostviewed\Block\Widget\Related;

use Amasty\Label\Model\LabelViewer;
use Amasty\Label\Model\ResourceModel\Label\Collection;
use Amasty\Mostviewed\Block\Widget\Related;

class AddLabels
{
    /**
     * @var LabelViewer
     */
    private $helper;

    public function __construct(
        LabelViewer $helper
    ) {
        $this->helper = $helper;
    }

    public function afterToHtml(
        Related $subject,
        string $result
    ): string {
        if (!$subject->getIsAmLabelObserved()) {
            $products = $subject->getProductCollection();

            if ($products) {
                foreach ($products as $product) {
                    $result .= $this->helper->renderProductLabel(
                        $product,
                        Collection::MODE_LIST,
                        true
                    );
                }

                $subject->setIsAmLabelObserved(true);
            }
        }

        return $result;
    }
}
