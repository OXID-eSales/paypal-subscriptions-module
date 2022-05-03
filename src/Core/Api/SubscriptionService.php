<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidSolutionCatalysts\PayPal\Core\Api;

use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidSolutionCatalysts\PayPalApi\Exception\ApiException;
use OxidSolutionCatalysts\PayPalApi\Model\Catalog\Patch;
use OxidSolutionCatalysts\PayPalApi\Model\Subscriptions\BillingCycle;
use OxidSolutionCatalysts\PayPalApi\Model\Subscriptions\Frequency;
use OxidSolutionCatalysts\PayPalApi\Model\Subscriptions\Money;
use OxidSolutionCatalysts\PayPalApi\Model\Subscriptions\PaymentPreferences;
use OxidSolutionCatalysts\PayPalApi\Model\Subscriptions\Plan;
use OxidSolutionCatalysts\PayPalApi\Model\Subscriptions\PlanRequestPOST;
use OxidSolutionCatalysts\PayPalApi\Model\Subscriptions\PricingScheme;
use OxidSolutionCatalysts\PayPalApi\Model\Subscriptions\Taxes;
use OxidSolutionCatalysts\PayPalApi\Service\Subscriptions;
use OxidSolutionCatalysts\PayPal\Core\ServiceFactory;
use OxidSolutionCatalysts\PayPal\Repository\SubscriptionRepository;

class SubscriptionService
{
    /**
     * @var Subscriptions
     */
    public $subscriptionService;

    /**
     * @var Request
     */
    private $request;

    public function __construct()
    {
        $this->subscriptionService = Registry::get(ServiceFactory::class)->getSubscriptionService();
        $this->request = Registry::getRequest();
    }

    /**
     * @param $subscriptionPlan
     * @throws ApiException
     */
    public function update($subscriptionPlan)
    {
        $this->updatePlanDescription(
            $subscriptionPlan,
            $this->request->getRequestEscapedParameter('billing_plan_description')
        );

        $this->updatePlanPaymentFailureThreshold(
            $subscriptionPlan,
            $this->request->getRequestEscapedParameter('payment_failure_threshold')
        );

        $this->updatePlanAutoBillOutstanding(
            $subscriptionPlan,
            $this->request->getRequestEscapedParameter('auto_bill_outstanding')
        );

        $this->updatePlanSetupFee(
            $subscriptionPlan,
            $this->request->getRequestEscapedParameter('setup_fee')
        );

        $this->updatePlanSetupFeeFailureAction(
            $subscriptionPlan,
            $this->request->getRequestEscapedParameter('setup_fee_failure_action')
        );

        $this->updatePlanTaxesPercentage(
            $subscriptionPlan,
            $this->request->getRequestEscapedParameter('tax_percentage')
        );

        $this->updatePricingSchemes(
            $subscriptionPlan,
            $this->request->getRequestEscapedParameter('tax_percentage')
        );
    }

    /**
     * @param Plan $subscriptionPlan
     * @param $billingPlanDescription
     * @throws ApiException
     */
    private function updatePlanDescription(Plan $subscriptionPlan, $billingPlanDescription)
    {
        if ($subscriptionPlan->description !== $billingPlanDescription) {
            $patchRequest = new Patch();
            $patchRequest->op = Patch::OP_REPLACE;
            $patchRequest->value = $billingPlanDescription;
            $patchRequest->path = '/description';
            $this->subscriptionService->updatePlan($subscriptionPlan->id, [$patchRequest]);
        }
    }

    /**
     * @param Plan $subscriptionPlan
     * @param $paymentFailureThreshold
     * @throws ApiException
     */
    private function updatePlanPaymentFailureThreshold(Plan $subscriptionPlan, $paymentFailureThreshold)
    {
        if ($subscriptionPlan->payment_preferences->payment_failure_threshold !== $paymentFailureThreshold) {
            $patchRequest = new Patch();
            $patchRequest->op = Patch::OP_REPLACE;
            $patchRequest->value = $paymentFailureThreshold;
            $patchRequest->path = '/payment_preferences/payment_failure_threshold';
            $this->subscriptionService->updatePlan($subscriptionPlan->id, [$patchRequest]);
        }
    }

    /**
     * @param Plan $subscriptionPlan
     * @param $autoBillOutstanding
     * @throws ApiException
     */
    private function updatePlanAutoBillOutstanding(Plan $subscriptionPlan, $autoBillOutstanding)
    {
        if ($subscriptionPlan->payment_preferences->auto_bill_outstanding !== $autoBillOutstanding) {
            $patchRequest = new Patch();
            $patchRequest->op = Patch::OP_REPLACE;
            $patchRequest->value = $autoBillOutstanding;
            $patchRequest->path = '/payment_preferences/auto_bill_outstanding';
            $this->subscriptionService->updatePlan($subscriptionPlan->id, [$patchRequest]);
        }
    }

    /**
     * @param Plan $subscriptionPlan
     * @param $setupFee
     * @throws ApiException
     */
    private function updatePlanSetupFee(Plan $subscriptionPlan, $setupFee)
    {
        if ($subscriptionPlan->payment_preferences->setup_fee !== $setupFee) {
            $patchRequest = new Patch();
            $patchRequest->op = Patch::OP_REPLACE;
            $patchRequest->value = $setupFee;
            $patchRequest->path = '/payment_preferences/setup_fee';
            $this->subscriptionService->updatePlan($subscriptionPlan->id, [$patchRequest]);
        }
    }

    /**
     * @param Plan $subscriptionPlan
     * @param $setupFeeFailureAction
     * @throws ApiException
     */
    private function updatePlanSetupFeeFailureAction(Plan $subscriptionPlan, $setupFeeFailureAction)
    {
        if ($subscriptionPlan->payment_preferences->setup_fee_failure_action !== $setupFeeFailureAction) {
            $patchRequest = new Patch();
            $patchRequest->op = Patch::OP_REPLACE;
            $patchRequest->value = $setupFeeFailureAction;
            $patchRequest->path = '/payment_preferences/setup_fee_failure_action';
            $this->subscriptionService->updatePlan($subscriptionPlan->id, [$patchRequest]);
        }
    }

    /**
     * @param Plan $subscriptionPlan
     * @param $taxPercentage
     * @throws ApiException
     */
    private function updatePlanTaxesPercentage(Plan $subscriptionPlan, $taxPercentage)
    {
        if ($subscriptionPlan->taxes->percentage !== $taxPercentage) {
            $patchRequest = new Patch();
            $patchRequest->op = Patch::OP_REPLACE;
            $patchRequest->value = $taxPercentage;
            $patchRequest->path = '/taxes/percentage';
            $this->subscriptionService->updatePlan($subscriptionPlan->id, [$patchRequest]);
        }
    }

    /**
     * @param Plan $subscriptionPlan
     * @throws ApiException
     */
    public function deactivatePlan(Plan $subscriptionPlan)
    {
        $this->subscriptionService->deactivatePlan($subscriptionPlan->id);
    }

    /**
     * @param string $productId
     * @param string $articleId
     * @return Plan
     * @throws ApiException
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function saveNewSubscriptionPlan(string $productId, string $articleId)
    {
        $fixed_price = $this->request->getRequestEscapedParameter('fixed_price', "");
        $interval = $this->request->getRequestEscapedParameter('interval', "");
        $tenure = $this->request->getRequestEscapedParameter('tenure', "");
        $total_cycles = $this->request->getRequestEscapedParameter('total_cycles', "");

        $sequenceCount = 1;
        if (count($fixed_price)) {
            $count = count($total_cycles);

            // search for trial tenure
            $bFoundTrail = false;
            for ($i = 0; $i < $count; $i++) {
                if ($tenure[$i] == 'TRIAL') {
                    $bFoundTrail = true;
                    break;
                }
            }
            if ($bFoundTrail) {
                 $sequenceCount++;
            }

            $cycles = [];

            for ($i = 0; $i < $count; $i++) {
                $cycle = new BillingCycle();
                $cycle->total_cycles = $total_cycles[$i];
                $cycle->sequence = ($tenure[$i] == 'TRIAL' ? 1 : $sequenceCount);
                $cycle->tenure_type = $tenure[$i];
                $cycle->frequency = new Frequency();
                $cycle->frequency->interval_count = $total_cycles[$i];
                $cycle->frequency->interval_unit = $interval[$i];
                $cycle->pricing_scheme = new PricingScheme();
                $cycle->pricing_scheme->fixed_price = new Money();
                $cycle->pricing_scheme->fixed_price->value = $fixed_price[$i];
                $cycle->pricing_scheme->fixed_price->currency_code = 'EUR';
                $cycle->pricing_scheme->tiers = null;
                $cycles[] = $cycle;
                if ($tenure[$i] !== 'TRIAL') {
                     $sequenceCount++;
                }
            }

            $payment_preferences = new PaymentPreferences();
            $payment_preferences->auto_bill_outstanding = true;
            $payment_preferences->setup_fee = new Money();
            $payment_preferences->setup_fee->currency_code = $this->request->getRequestEscapedParameter(
                'setup_fee_currency',
                'EUR'
            );
            $payment_preferences->setup_fee->value = $this->request->getRequestEscapedParameter('setup_fee', 0);
            $payment_preferences->service_type = 'PREPAID';
            $payment_preferences->payment_failure_threshold = $this->request->getRequestEscapedParameter(
                'payment_failure_threshold',
                1
            );
            $payment_preferences->setup_fee_failure_action = $this->request->getRequestEscapedParameter(
                'setup_fee_failure_action',
                ''
            );
            $payment_preferences->auto_bill_outstanding = $this->request->getRequestEscapedParameter(
                'auto_bill_outstanding',
                true
            );

            $tax = new Taxes();
            $tax->percentage = $this->request->getRequestEscapedParameter('tax_percentage', 0);
            $tax->inclusive = $this->request->getRequestEscapedParameter('tax_inclusive', false);

            $subscriptionPlanRequest = new PlanRequestPOST();
            $subscriptionPlanRequest->name = $this->request->getRequestEscapedParameter('billing_plan_name', '');
            $subscriptionPlanRequest->product_id = $productId;
            $subscriptionPlanRequest->billing_cycles = $cycles;
            $subscriptionPlanRequest->payment_preferences = $payment_preferences;
            $subscriptionPlanRequest->taxes = $tax;
            $subscriptionPlanRequest->description = $this->request
                ->getRequestEscapedParameter('billing_plan_description');

            $response = $this->subscriptionService->createPlan($subscriptionPlanRequest);

            if ($response->id) {
                $subscriptionPlanId = $response->id;
                $repository = new SubscriptionRepository();
                $repository->saveSubscriptionPlan($subscriptionPlanId, $productId, $articleId);
            }

            $response->billing_cycles = $cycles;

            return $response;
        }
    }

    public function listPlans($paypalProductId, $planIds)
    {
        return $this->subscriptionService->listPlans(
            'string',
            $paypalProductId,
            implode(
                ',',
                $planIds
            ),
            true,
            1,
            10,
            'return=representation'
        );
    }
}
