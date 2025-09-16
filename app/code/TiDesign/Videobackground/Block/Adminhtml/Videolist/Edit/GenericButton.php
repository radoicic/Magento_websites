<?php
namespace TiDesign\Videobackground\Block\Adminhtml\Videolist\Edit;

use Magento\Backend\Block\Widget\Context;
use TiDesign\Videobackground\Api\VideolistRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    protected $context;
   
    protected $videolistRepository;
    
    public function __construct(
        Context $context,
        VideolistRepositoryInterface $videolistRepository
    ) {
        $this->context = $context;
        $this->videolistRepository = $videolistRepository;
    }

    public function getId()
    {
        try {
            return $this->videolistRepository->getById(
                $this->context->getRequest()->getParam('id')
            )->getId();
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