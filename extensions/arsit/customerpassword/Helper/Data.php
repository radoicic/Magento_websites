<?php
/**
 * Arsit_CustomerPassword
 *
 * @category    Arsit
 * @package     Arsit_CustomerPassword
 * @copyright   Copyright (c) 2016 arsit.ru
 * @author      developer@arsit.ru
 */

namespace Arsit\CustomerPassword\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const ADMIN_FORM_ELEMENT = 'setpassword';

    /**
     * Return Element Key
     *
     * @return string
     */
    public function getElementKey()
    {
        return self::ADMIN_FORM_ELEMENT;
    }
}
