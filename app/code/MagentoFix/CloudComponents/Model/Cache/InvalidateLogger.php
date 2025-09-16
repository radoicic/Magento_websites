<?php
declare(strict_types=1);

namespace MagentoFix\CloudComponents\Model\Cache;

use Magento\Framework\App\Request\Http as HttpRequest;
use Psr\Log\LoggerInterface as Logger;
use Magento\CloudComponents\Model\DebugTrace;

class InvalidateLogger extends \Magento\Framework\Cache\InvalidateLogger
{
    private $tagsToLog = [
        'cat_p',
        'cat_c',
        'PRODUCT_PRICE',
        'cms_b',
        'cms_p',
        'config_scopes',
        'eav',
        'eav_attribute',
        'fpc',
        'review_block',
        'SEARCH_QUERY',
        'search_query',
        'store_group',
        'store',
        'store_relations',
        'website',
        'CORE_DESIGN',
        'core_design',
        'WEBSERVICE',
        'webservice',
        'banner',
        'catalog_event',
        'config',
        'block_html',
        'COLLECTION_DATA',
        'collection_data',
        'collections',
        'layout_general_cache_tag',
        'layout',
        'compiled_config',
        'acl_cache',
        'reflection',
        'db_ddl',
        'LOCKED_RECORD_INFO_SYSTEM_CONFIG',
        'all'
    ];

    private $debugTrace;

    public function __construct(
        HttpRequest $request,
        Logger $logger,
        DebugTrace $debugTrace
    ) {
        parent::__construct($request, $logger);
        $this->debugTrace = $debugTrace;
    }

    public function execute($invalidateInfo)
    {
        $needTrace = false;
        if (is_array($invalidateInfo) && isset($invalidateInfo['tags'])) {
            foreach ($invalidateInfo['tags'] as $tag) {
                if (in_array(strtolower((string)$tag), $this->tagsToLog)) {
                    $needTrace = true;
                }
            }
            if ($needTrace) {
                $invalidateInfo['trace'] = $this->debugTrace->getTrace();
            }
        }
        parent::execute($invalidateInfo);
    }
}
