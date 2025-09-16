<?php
declare(strict_types=1);

namespace Amasty\Pgrid\Plugin\Setup\Model\FixtureGenerator\EntityGeneratorFactory;

use Magento\Setup\Model\FixtureGenerator\EntityGenerator;
use Magento\Setup\Model\FixtureGenerator\EntityGeneratorFactory;

class UpdateCustomTableMapPlugin
{
    /**
     * Inject amasty_pgrid_qty_sold table data to FixtureGenerator\EntityGeneratorFactory arguments.
     *
     * @param EntityGeneratorFactory $subject
     * @param array $data
     * @return array
     */
    public function beforeCreate(
        EntityGeneratorFactory $subject,
        array $data
    ): array {
        $data['customTableMap']['amasty_pgrid_qty_sold'] = [
            'entity_id_field' => EntityGenerator::SKIP_ENTITY_ID_BINDING,
            'handler' => function ($productId, $entityNumber, $fixture, $binds) {
                foreach ($binds as &$bind) {
                    $bind['product_id'] = $productId + 1;
                }
                return $binds;
            },
        ];

        return [$data];
    }
}
