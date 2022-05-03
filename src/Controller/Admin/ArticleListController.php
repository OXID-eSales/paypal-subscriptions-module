<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\PayPal\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidSolutionCatalysts\PayPalApi\Exception\ApiException;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Registry;
use OxidSolutionCatalysts\PayPal\Core\ServiceFactory;
use OxidSolutionCatalysts\PayPal\Repository\SubscriptionRepository;

class ArticleListController extends ArticleListController_Parent
{
    /**
     * @param $oxid
     * @return bool
     */
    public function isSubscriptionProduct($oxid)
    {
        return $this->hasLinkedObject($oxid);
    }

    public function hasLinkedObject($oxid)
    {
        $linkedObject = null;

        $article = oxNew(Article::class);
        $article->load($oxid);

        $repository = new SubscriptionRepository();

        $linkedProduct = $repository->getLinkedProductByOxid($oxid);
        if ($linkedProduct) {
            $linkedProduct = $linkedProduct[0]['PAYPALPRODUCTID'];

            try {
                $linkedObject = Registry::get(ServiceFactory::class)
                    ->getCatalogService()
                    ->showProductDetails($linkedProduct);
            } catch (ApiException $exception) {
                // We have a linkedProduct, but its does not exists in PayPal-Catalogs, so we delete them
                Registry::getLogger()->error($exception);
                //$repository->deleteLinkedProduct($linkedProduct);
            }
        }

        if ($linkedObject) {
            return true;
        }

        return false;
    }
}
