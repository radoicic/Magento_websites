<?php

declare(strict_types=1);

namespace Amasty\LabelGraphQl\Model\Resolver;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Model\Label\Actions\GetLabelCssClass;
use Amasty\Label\Model\Label\Parts\FrontendSettings\GetLabelImageUrl;
use Amasty\Label\Model\LabelViewer;
use Amasty\Label\Model\ResourceModel\Label\Collection;
use Amasty\Label\Model\ResourceModel\Label\GetRelatedEntitiesIds as GetLabelCustomerGroupIds;
use Amasty\Label\ViewModel\Label\TextProcessor;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Psr\Log\LoggerInterface;

class LabelProvider implements ResolverInterface
{
    const MODEL_KEY = 'model';

    /**
     * @var LabelViewer
     */
    private $labelViewer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TextProcessor
     */
    private $textProcessor;

    /**
     * @var GetLabelImageUrl
     */
    private $getLabelImageUrl;

    /**
     * @var GetLabelCssClass
     */
    private $getLabelCssClass;

    /**
     * @var GetLabelCustomerGroupIds
     */
    private $getLabelCustomerGroupIds;

    public function __construct(
        LabelViewer $labelViewer,
        CollectionFactory $productCollectionFactory,
        LoggerInterface $logger,
        TextProcessor $textProcessor,
        GetLabelImageUrl $getLabelImageUrl,
        GetLabelCssClass $getLabelCssClass,
        GetLabelCustomerGroupIds $getLabelCustomerGroupIds,
        Session $session
    ) {
        $this->labelViewer = $labelViewer;
        $this->logger = $logger;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->session = $session;
        $this->textProcessor = $textProcessor;
        $this->getLabelImageUrl = $getLabelImageUrl;
        $this->getLabelCssClass = $getLabelCssClass;
        $this->getLabelCustomerGroupIds = $getLabelCustomerGroupIds;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws \Exception
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['productIds']) || !is_array($args['productIds']) || !isset($args['mode'])) {
            throw new GraphQlNoSuchEntityException(__('Wrong parameter provided.'));
        }

        try {
            $result = [];
            $storeId = (int) $context->getExtensionAttributes()->getStore()->getId();
            $this->session->setCustomerId($context->getUserId());
            $mode = strtolower($args['mode']) === LabelViewer::MODE_PRODUCT_PAGE
                ? Collection::MODE_PDP
                : Collection::MODE_LIST;

            /** @var Product $product **/
            foreach ($this->getProducts($args['productIds'], $storeId) as $product) {
                $labels = [];

                /** @var LabelInterface $label **/
                foreach ($this->labelViewer->getAppliedLabels($product, true, $mode) as $label) {
                    $extensionAttributes = $label->getExtensionAttributes();
                    $frontendSettings = $extensionAttributes->getFrontendSettings();
                    /** @var \Amasty\Label\Model\Label  $label */
                    $data['label_id'] = $label->getLabelId();
                    $data['product_id'] = $label->getExtensionAttributes()->getRenderSettings()->getProduct()->getId();
                    $data['size'] = $frontendSettings->getImageSize();
                    $data['txt'] = strip_tags(
                        (string) $this->textProcessor->renderLabelText($frontendSettings->getLabelText(), $label),
                        '<br>,<a>,<b>'
                    );
                    $data['image'] = $this->getRelativePath(
                        $this->getLabelImageUrl->getByLabel($label),
                        $context
                    );
                    $data['position'] = $this->getLabelCssClass->execute($frontendSettings->getPosition());
                    $data['style'] = $frontendSettings->getStyle();
                    $data['name'] = $label->getName();
                    $data[self::MODEL_KEY] = $label;
                    $data['is_visible'] = $extensionAttributes->getRenderSettings()->isLabelVisible();
                    $data['customer_group_ids'] = $this->getCustomerGroupIdsAsString($data['label_id']);

                    $labels[] = $data;
                }

                $result[$product->getId()]['items'] = $labels;
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new GraphQlNoSuchEntityException(__('Something went wrong.'));
        }
    }

    private function getCustomerGroupIdsAsString(int $labelId): string
    {
        return implode(',', $this->getLabelCustomerGroupIds->execute($labelId));
    }

    /**
     * @param string $src
     * @param $context
     *
     * @return string|string[]
     */
    protected function getRelativePath(?string $src, $context): ?string
    {
        $result = null;

        if (null !== $src) {
            $baseUrl = $context->getExtensionAttributes()->getStore()->getBaseUrl();
            $result = str_replace($baseUrl, '', $src);
        }

        return $result;
    }

    /**
     * @param array $productIds
     * @param int $storeId
     *
     * @return \Magento\Framework\DataObject[]
     */
    protected function getProducts(array $productIds, int $storeId)
    {
        $collection = $this->productCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addPriceData()
            ->addIdFilter($productIds);

        return $collection->getItems();
    }
}
