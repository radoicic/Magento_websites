<?php
namespace TiDesign\Domainmanager\Block\Adminhtml\Domainlist\Edit;

use Magento\Backend\Block\Widget\Context;
use TiDesign\Domainmanager\Api\DomainlistRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    protected $context;
   
    protected $domainlistRepository;
    
    public function __construct(
        Context $context,
        DomainlistRepositoryInterface $domainlistRepository
    ) {
        $this->context = $context;
        $this->domainlistRepository = $domainlistRepository;
    }

    public function getId()
    {
        try {
            return $this->domainlistRepository->getById(
                $this->context->getRequest()->getParam('id')
            )->getTdId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
?>