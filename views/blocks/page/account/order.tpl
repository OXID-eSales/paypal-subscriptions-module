[{if $order->isPayPalSubscription($order->getId())}]
    [{include file='modules/osc/paypal/order_and_subscription_overview.tpl'}]
[{elseif $order->isPayPalPartSubscription($order->getId())}]
    [{include file='modules/osc/paypal/order_and_partsubscription_overview.tpl'}]
[{else}]
    [{$smarty.block.parent}]
[{/if}]