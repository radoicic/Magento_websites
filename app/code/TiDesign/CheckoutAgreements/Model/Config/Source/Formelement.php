<?php
namespace TiDesign\CheckoutAgreements\Model\Config\Source;

class Formelement implements \Magento\Framework\Option\ArrayInterface
{

    protected $options;

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [['label' => __('Please select'), 'value' => '']];

			$this->options[] = ['label' => 'Text Box','value' => 'text'];
			$this->options[] = ['label' => 'Text Area','value' => 'textarea'];
			$this->options[] = ['label' => 'Signature','value' => 'signature'];
			$this->options[] = ['label' => 'Upload','value' => 'upload'];
			$this->options[] = ['label' => 'Date','value' => 'date'];
			$this->options[] = ['label' => 'Time','value' => 'time'];
			$this->options[] = ['label' => 'Ip Address','value' => 'creation_ip'];
			$this->options[] = ['label' => 'Selection','value' => 'selection'];
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
