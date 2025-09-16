<?php
/**
 * Public alias for the application entry point
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Bootstrap;

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    exit(1);
}
/*
$bootstrap = Bootstrap::create(BP, $_SERVER);

$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);
*/

//echo $_SERVER['HTTP_HOST']; die();
 switch ($_SERVER['HTTP_HOST']) {
 	case 'dev.nzboxer.com':
         $params = $_SERVER; $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'base';
         $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
         $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
         $app = $bootstrap->createApplication('Magento\Framework\App\Http');
         $bootstrap->run($app);
         break;
 	case 'dev.parlay.co.nz':
         $params = $_SERVER; $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'claro';
         $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
         $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
         $app = $bootstrap->createApplication('Magento\Framework\App\Http');
         $bootstrap->run($app);
         break;

     case 'dev.fundraising.parlay.co.nz':
         $params = $_SERVER; $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'fundraising';
         $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
         $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
         $app = $bootstrap->createApplication('Magento\Framework\App\Http');
         $bootstrap->run($app);
         break;
 }
