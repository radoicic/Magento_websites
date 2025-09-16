<?php
namespace TiDesign\Consignment\Model\Config;
use Magento\Framework\Option\ArrayInterface;

class Frequency implements ArrayInterface
{
 public function toOptionArray()
 {
  return [
    [
		'value' => '', 
		'label' => __('Disabled')
	],
    [
		'value' => '*/5 * * * *', 
		'label' => __('Every 5 minutes')
	],
    [
		'value' => '*/30 * * * *', 
		'label' => __('Every 30 minutes')
	],
    [
		'value' => '0 */1 * * *', 
		'label' => __('Every 1 hour')
	],
    [
		'value' => '0 */3 * * *', 
		'label' => __('Every 3 hours')
	],
    [
		'value' => '0 */6 * * *', 
		'label' => __('Every 6 hours')
	],
    [
		'value' => '0 */12 * * *', 
		'label' => __('Every 12 hours')
	],
    [
		'value' => '0 0 */1 * *', 
		'label' => __('Every day')
	]
  ];
 }
}