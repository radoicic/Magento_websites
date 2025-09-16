<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CityBeach\Integration\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Integration\Model\AuthorizationService;
use Magento\Integration\Model\ConfigBasedIntegrationManager;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\OauthService;

class InstallPatch
    implements DataPatchInterface,
    PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ConfigBasedIntegrationManager
     */
    private $integrationManager;

    /**
     * @var IntegrationServiceInterface
     */
    private $integrationService;

    /**
     * @var AuthorizationService
     */
    private $authorizationService;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var OauthService
     */
    private $oauthService;

    /**
     * @var Token
     */
    private $token;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ConfigBasedIntegrationManager $integrationManager
     * @param IntegrationServiceInterface $integrationService
     * @param AuthorizationService $scopeConfig
     * @param ScopeConfigInterface $oauthService
     * @param OauthService $oauthService
     * @param Token $token
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ConfigBasedIntegrationManager $integrationManager,
        IntegrationServiceInterface $integrationService,
        AuthorizationService $authorizationService,
        ScopeConfigInterface $scopeConfig,
        OauthService $oauthService,
        Token $token
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->integrationManager = $integrationManager;
        $this->integrationService = $integrationService;
        $this->authorizationService = $authorizationService;
        $this->scopeConfig = $scopeConfig;
        $this->oauthService = $oauthService;
        $this->token = $token;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $name = 'CityBeach Integration';
        $this->integrationManager->processIntegrationConfig([$name]);

        $email_address = $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $integration = $this->integrationService->findByName($name);
        $integration->setStatus(\Magento\Integration\Model\Integration::STATUS_ACTIVE);
        $integrationData = [
            \Magento\Integration\Model\Integration::ID => $integration->getId(),
            \Magento\Integration\Model\Integration::NAME => $integration->getName(),
            \Magento\Integration\Model\Integration::EMAIL => $email_address,
            \Magento\Integration\Model\Integration::ENDPOINT => $integration->getEndpoint(),
            \Magento\Integration\Model\Integration::IDENTITY_LINK_URL => $integration->getIdentityLinkUrl(),
            \Magento\Integration\Model\Integration::SETUP_TYPE => $integration->getSetupType(),
            \Magento\Integration\Model\Integration::CONSUMER_ID => $integration->getConsumerId(),
            \Magento\Integration\Model\Integration::STATUS => \Magento\Integration\Model\Integration::STATUS_ACTIVE,
        ];
        $this->integrationService->update($integrationData);

        // Code to create consumer
        $consumer = $this->oauthService->createConsumer(['name' => $name]);
        $consumerId = $consumer->getId();
        $integration->setConsumerId($consumer->getId());
        $integration->save();

        // Code to grant permission
        $this->authorizationService->grantAllPermissions($integration->getId());

        // Code to Activate and Authorize
        $uri = $this->token->createVerifierToken($consumerId);
        $this->token->setType('access');
        $this->token->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $name = 'CityBeach Integration';
        $integration = $this->integrationService->findByName($name);
        if ( $integration ) {
            $consumerId = $integration->getConsumerId();
            if ( $consumerId ) {
                $this->oauthService->deleteConsumer($consumerId);
            }
            $this->integrationService->delete($integration->getId());
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
