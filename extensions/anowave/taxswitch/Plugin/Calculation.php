<?php
/**
 * Anowave Magento 2 Tax Switcher
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_TaxSwitch
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
namespace Anowave\TaxSwitch\Plugin;

use Anowave\TaxSwitch\Helper\Data;
use Magento\Catalog\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

class Calculation
{
    /**
     * @var Data
     */
    protected $helper;
    
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var State
     */
    protected $state;

    public function __construct
    (
        Data $helper,
        Session $session,
        State $state
    )
    {
        $this->helper = $helper;
        $this->session = $session;
        $this->state = $state;
    }

    /**
     * Change tax rate dynamically 
     * 
     * @param \Magento\Tax\Model\Calculation $subject
     * @param callable $proceed
     * @param DataObject $request
     * 
     * @return [type]
     */
    public function aroundGetRate(\Magento\Tax\Model\Calculation $subject, callable $proceed, DataObject $request)
    {
        if (Area::AREA_FRONTEND === $this->state->getAreaCode() && !$this->helper->isLogged())
        {
            $country = $this->helper->getRequest()->getParam(Data::CONTEXT_TAX_DISPLAY_COUNTRY_TEST);

            if ($country)
            {
                $request->setCountryId($country);
            }
            else 
            {
                $insights = $this->session->getInsights();

                if ($insights)
				{
					if (property_exists($insights, 'registered_country'))
					{
						$iso = $insights->registered_country->iso_code;

                        $request->setCountryId($iso);
                    }
                }		
            }
        }

        return $proceed($request);
    }
}
  