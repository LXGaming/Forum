<?php

namespace lolnetnz\Forum\Payment;

use lolnetnz\Forum\Integration\RedisIntegration;
use XF\Entity\PurchaseRequest;
use XF\Purchasable\Purchase;

class PayPal extends XFCP_PayPal {

    protected function getPaymentParams(PurchaseRequest $purchaseRequest, Purchase $purchase) {
        $params = parent::getPaymentParams($purchaseRequest, $purchase);
        $params["cmd"] = "_donations";
        return $params;
    }

    public function completeTransaction(CallbackState $state) {
        parent::completeTransaction($state);

        if ($state->legacy) {
            $purchaseRequest = null;
            $message["item"] = $state->userUpgrade->title;
            $message["cost"] = $state->userUpgrade->cost_amount;
            $message["currency"] = $state->userUpgrade->cost_currency;
        } else {
            $purchaseRequest = $state->getPurchaseRequest();
            $message["item"] = $purchaseRequest->Purchasable->title;
            $message["cost"] = $purchaseRequest->cost_amount;
            $message["currency"] = $state->userUpgrade->cost_currency;
        }

        $message["username"] = $state->getPurchaser()->username;
        $message["result"] = $state->paymentResult;

        RedisIntegration::publish(json_encode($message));
    }
}
