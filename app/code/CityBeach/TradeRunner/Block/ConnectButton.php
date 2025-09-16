<?php

namespace CityBeach\TradeRunner\Block;

use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Model\ConfigBasedIntegrationManager;
use \Magento\Integration\Model\OauthService;

class ConnectButton extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'CityBeach_TradeRunner::connectbutton.phtml';

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $historicToken;

    /**
     * @var string
     */
    protected $storeName;


    /**
     * @param IntegrationServiceInterface $integrationService
     * @param ConfigBasedIntegrationManager $integrationManager
     * @param OauthService $oauthService
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        IntegrationServiceInterface $integrationService,
        ConfigBasedIntegrationManager $integrationManager,
        OauthService $oauthService,
        array $data = []
    ) {
        $name = 'CityBeach Integration';
        $integration = $integrationService->findByName($name);
        $token = $this->findAccessToken( $name, $integrationService, $integrationManager, $oauthService );
        $old_token = $this->findAccessToken( 'CityBeach', $integrationService, $integrationManager, $oauthService );
        $this->emailAddress = "";
        $this->accessToken = $token->getToken();
        $this->historicToken = $this->historicToken = $old_token ? $old_token->getToken() : "not available";
        $this->baseUrl = $context->getUrlBuilder()->getBaseUrl();
        $this->storeName = $context->getStoreManager()->getStore()->getName();
        parent::__construct($context, $data);
    }

    private function findAccessToken( $name, $integrationService, $integrationManager, $oauthService ) {
        $integration = $integrationService->findByName($name);
        $consumer_id = $integration->getConsumerId();
        $token = $oauthService->getAccessToken($consumer_id);
        if (! $token ) {
            $integrationManager->processIntegrationConfig([$name]);
            $integration = $integrationService->findByName($name);
            $consumer_id = $integration->getConsumerId();
            $token = $oauthService->getAccessToken($consumer_id);
            if (! $token ) {
                $token = FALSE;
            }
        }
        return $token;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getHistoricToken()
    {
        return $this->historicToken;
    }

    /**
     * @return string
     */
    public function getStoreName()
    {
        return $this->storeName;
    }
}
