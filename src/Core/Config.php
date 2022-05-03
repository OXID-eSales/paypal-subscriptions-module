<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidSolutionCatalysts\PayPal\Core;

use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\PayPal\Traits\ServiceContainer;
use OxidSolutionCatalysts\PayPal\Service\ModuleSettings;
use OxidSolutionCatalysts\PayPalApi\Client;

/**
 * Class Config
 */
class Config
{
    use ServiceContainer;

    /**
     * Checks if module configurations are valid
     *
     * @throws StandardException
     */
    public function checkHealth(): void
    {
        if (!$this->getServiceFromContainer(ModuleSettings::class)->checkHealth()) {
            throw oxNew(
                StandardException::class
            );
        }
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        try {
            $this->checkHealth();
        } catch (StandardException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->isSandbox();
    }

    /**
     * Get client id based on set active mode
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getClientId();
    }

    /**
     * Get client secret based on active mode
     *
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getClientSecret();
    }

    /**
     * @return string
     */
    public function getWebhookId()
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getWebhookId();
    }

    public function isAcdcEligibility(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->isAcdcEligibility();
    }

    public function isPuiEligibility(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->isPuiEligibility();
    }

    public function getLiveClientId(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getLiveClientId();
    }

    public function getLiveClientSecret(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getLiveClientSecret();
    }

    public function getLiveWebhookId(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getLiveWebhookId();
    }

    public function getSandboxClientId(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getSandboxClientId();
    }

    public function getSandboxClientSecret(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getSandboxClientSecret();
    }

    public function getSandboxWebhookId(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getSandboxWebhookId();
    }

    public function showPayPalBasketButton(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showPayPalBasketButton();
    }

    public function showPayPalPayLaterButton(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showPayPalPayLaterButton();
    }

    public function loginWithPayPalEMail(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->loginWithPayPalEMail();
    }

    public function showPayPalProductDetailsButton(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showPayPalProductDetailsButton();
    }

    public function getAutoBillOutstanding(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getAutoBillOutstanding();
    }

    public function getSetupFeeFailureAction(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getSetupFeeFailureAction();
    }

    public function getPaymentFailureThreshold(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getPaymentFailureThreshold();
    }

    public function showAllPayPalBanners(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showAllPayPalBanners();
    }

    public function showBannersOnStartPage(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showBannersOnStartPage();
    }

    public function getStartPageBannerSelector(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getStartPageBannerSelector();
    }

    public function showBannersOnCategoryPage(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showBannersOnCategoryPage();
    }

    public function getCategoryPageBannerSelector(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getCategoryPageBannerSelector();
    }

    public function showBannersOnSearchPage(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showBannersOnSearchPage();
    }

    public function getSearchPageBannerSelector(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getSearchPageBannerSelector();
    }

    public function showBannersOnProductDetailsPage(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showBannersOnProductDetailsPage();
    }

    public function getProductDetailsPageBannerSelector(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getProductDetailsPageBannerSelector();
    }

    public function showBannersOnCheckoutPage(): bool
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->showBannersOnCheckoutPage();
    }

    public function getPayPalBannerCartPageSelector(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getPayPalBannerCartPageSelector();
    }

    public function getPayPalBannerPaymentPageSelector(): string
    {
        return $this->getServiceFromContainer(ModuleSettings::class)->getPayPalBannerPaymentPageSelector();
    }

    /**
     * TODO: use Service\ModuleSettings
     * Config value getter
     * @Todo PSPAYPAL-491 Work in progress, add tests
     * @Todo Ensure we fetch this setting from the active subshop.
     * @param mixed oxconfig.OXVARNAME
     * @return string|boolean value
     */
    public function getPayPalModuleConfigurationValue($varname)
    {
        if ($varname == '') {
            return (bool) false;
        }

        //TODO: try catch invalid settings
        #return $this->getServiceFromContainer(ModuleSettings::class)->getRawValue($varname);

        return (string) Registry::getConfig()->getConfigParam($varname);
    }

    /**
     * Get webhook controller url
     *
     * @return string
     */
    public function getWebhookControllerUrl(): string
    {
        return html_entity_decode(
            Registry::getConfig()->getCurrentShopUrl(false) . 'index.php?cl=oscpaypalwebhook'
        );
    }

    public function getClientUrl(): string
    {
        return $this->isSandbox() ? $this->getClientSandboxUrl() : $this->getClientLiveUrl();
    }

    public function getClientLiveUrl(): string
    {
        return Client::PRODUCTION_URL;
    }

    public function getClientSandboxUrl(): string
    {
        return Client::SANDBOX_URL;
    }
}
