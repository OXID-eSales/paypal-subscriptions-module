[{assign var="config" value=$oViewConf->getPayPalConfig()}]
[{if $config->isActive() && !$oViewConf->isPayPalExpressSessionActive() && $config->showPayPalBasketButton()}]
    [{include file="modules/osc/paypal/smartpaymentbuttons.tpl" buttonId="PayPalPayButtonNextCart2" buttonClass="float-right pull-right paypal-button-wrapper small"}]
    <div class="float-right pull-right paypal-button-or">
        [{"OR"|oxmultilangassign|oxupper}]
    </div>
    [{if $loadingScreen == 'true'}]
        <div id="overlay"><div class="loader"></div></div>
        <script>
            document.getElementById("overlay").style.display = "block";
        </script>
    [{/if}]
[{/if}]
