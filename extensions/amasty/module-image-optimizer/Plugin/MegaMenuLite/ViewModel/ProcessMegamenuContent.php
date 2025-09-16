<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2023 Amasty (https://www.amasty.com)
 * @package Image Optimizer for Magento 2 (System)
 */

namespace Amasty\ImageOptimizer\Plugin\MegaMenuLite\ViewModel;

use Amasty\ImageOptimizer\Model\LazyConfigProvider;
use Amasty\ImageOptimizer\Model\Output\ImageReplaceProcessor;
use Amasty\MegaMenuLite\ViewModel\Tree;

class ProcessMegamenuContent
{
    /**
     * @var ImageReplaceProcessor
     */
    private $imageReplaceProcessor;

    /**
     * @var LazyConfigProvider
     */
    private $lazyConfigProvider;

    /**
     * @var array|null
     */
    private $cachedResult = null;

    public function __construct(
        ImageReplaceProcessor $imageReplaceProcessor,
        LazyConfigProvider $lazyConfigProvider
    ) {
        $this->imageReplaceProcessor = $imageReplaceProcessor;
        $this->lazyConfigProvider = $lazyConfigProvider;
    }

    public function afterGetNodesData(Tree $subject, array $result): array
    {
        if ($this->lazyConfigProvider->get()->getData('is_lazy')) {
            return $result;
        }

        if ($this->cachedResult === null) {
            if (!empty($result['elems'])) {
                foreach ($result['elems'] as &$node) {
                    $this->processNode($node);
                }
            }
            $this->cachedResult = $result;
        }

        return $this->cachedResult;
    }

    private function processNode(array &$node): void
    {
        if (!empty($node['elems'])) {
            foreach ($node['elems'] as &$child) {
                $this->processNode($child);
            }
        }

        if (!empty($node['content'])) {
            $this->imageReplaceProcessor->process($node['content']);
        }
    }
}
