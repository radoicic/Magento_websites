<?php
/**
 * Copyright © eComBricks. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace Ecombricks\InventoryCustomerSourceSelection\Data;

/**
 * Joint data
 */
class JointData
{
    /**
     * Parse
     * 
     * @param string $value
     * @return array
     */
    public function parse($value): array
    {
        $data = [];
        if (empty($value)) {
            return [];
        }
        foreach (explode('|', trim($value)) as $line) {
            $pieces = explode(':', trim($line));
            if (empty($pieces[0]) || empty($pieces[1])) {
                continue;
            }
            $data[trim($pieces[0])] = trim($pieces[1]);
        }
        return $data;
    }

    /**
     * Generate
     * 
     * @param array $data
     * @return string|null
     */
    public function generate($data): ?string
    {
        if (empty($data)) {
            return null;
        }
        $lines = [];
        foreach ($data as $key => $value) {
            $lines[] = $key.':'.$value;
        }
        return implode('|', $lines);
    }
}