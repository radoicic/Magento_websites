<?php
/**
 * Anowave Magento 2 MaxMind API
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
namespace Anowave\TaxSwitch\Model;
 
class Api
{
	/**
	 * MaxMind Username
	 * 
	 * @var string
	 */
	private $username = null;
	
	/**
	 * MaxMind Password
	 * 
	 * @var string
	 */
	private $password = null;

	/**
	 * @var \Anowave\TaxSwitch\Helper\Data
	 */
	protected $helper = null;
	
	/**
	 * Constructor 
	 * 
	 * @param \Anowave\TaxSwitch\Helper\Data $helper
	 */
	public function __construct
	(
		\Anowave\TaxSwitch\Helper\Data $helper
	)
	{
		$this->helper = $helper;
		
		/**
		 * Set username
		 */
		$this->username = $this->helper->getConfig('taxswitch/maxmind/username');
		
		/**
		 * Set password
		 */
		$this->password = $this->helper->getConfig('taxswitch/maxmind/password');
	}

	/**
	 * Get insights
	 */
	public function insights()
	{
		if (!$this->password || !$this->username)
		{
			return null;
		}

		$ch = curl_init("https://geoip.maxmind.com/geoip/v2.1/country/{$this->helper->getIp()}");
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($ch, CURLOPT_TIMEOUT,5);
		curl_setopt($ch, CURLOPT_USERPWD ,"$this->username:$this->password");
		
		return function() use ($ch)
		{
			$insights = array();
			
			try
			{
				$response = curl_exec($ch);

				if (!curl_error($ch))
				{
					$insights = json_decode($response);
					
					if (!isset($insights->country))
					{
						$insights = array();
					}
				}
			}
			catch (\Exception $e)
			{
				return null;
			}
				
			curl_close($ch);
			
			return $insights;
		};
	}
	
	/**
	 * Get country 
	 * 
	 * @return mixed
	 */
	public function getCountry()
	{
		return call_user_func($this->country());
	}
	
	/**
	 * Get MaxMind insights object 
	 * 
	 * @return mixed
	 */
	public function getInsights()
	{
		$f = $this->insights();
		
		if ($f)
		{
			return call_user_func($f);
		}
	}
}