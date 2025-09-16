<?php

namespace TiDesign\Fundraiser\Model\Catalog\Category;

class CategoryIdsToArrayProcessor
{
    /**
     * @param array|string|null $categoryIds
     * @return int[]
     */
    public function execute($categoryIds, $toString = false)
    {
        $categoryIds = !is_array($categoryIds) ? explode(',', ($categoryIds ?: '')) : $categoryIds;
        return array_reduce($categoryIds, function ($carry, $categoryId) use ($toString) {
            $categoryId = trim((string)($categoryId ?: ''));
            if ($categoryId) {
                $carry[] = $toString ? (string)$categoryId : (int)$categoryId;
            }
            return $carry;
        }, []);
    }
}
