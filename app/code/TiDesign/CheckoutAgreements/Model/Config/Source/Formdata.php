<?php
namespace TiDesign\CheckoutAgreements\Model\Config\Source;

class Formdata implements \Magento\Framework\Option\ArrayInterface
{

    protected $options;

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [['label' => __('Please select'), 'value' => 0]];

			$this->options[] = ['label' => 'Business name and Premisses(full Address)','value' => 'business_name_and_premisses_full_address'];
			$this->options[] = ['label' => 'Client Name (printed)','value' => 'client_name_printed'];
			$this->options[] = ['label' => 'Client Home Address','value' => 'client_home_address'];
			$this->options[] = ['label' => 'Where Application is for a Ltd Company (enter names)','value' => 'where_application_is_for_a_ltd_company'];
			$this->options[] = ['label' => 'Full Name (Printed)','value' => 'full_name_printed'];
			$this->options[] = ['label' => 'Application for','value' => 'application_for'];
			$this->options[] = ['label' => 'Trade References','value' => 'trade_references'];
        }

        return $this->options;
    }

    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
