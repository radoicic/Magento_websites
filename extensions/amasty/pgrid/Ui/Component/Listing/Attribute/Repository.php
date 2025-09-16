<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Ui\Component\Listing\Attribute;

class Repository extends \Magento\Catalog\Ui\Component\Listing\Attribute\Repository
{
    protected function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder
            ->addFilter(
                'frontend_input',
                [
                    'textarea',
                    'text',
                    'weight',
                    'price',
                    'date',
                    'boolean',
                    'select',
                    'multiselect',
                    'media_image'
                ],
                'in'
            )->create();
    }
}
