<?php

namespace lolnetnz\Forum\Payment;

use lolnetnz\Forum\Integration\RedisIntegration;
use XF\Entity\PurchaseRequest;
use XF\Payment\CallbackState;
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
            $data["item"] = $state->userUpgrade->title;
            $data["cost"] = $state->userUpgrade->cost_amount;
            $data["currency"] = $state->userUpgrade->cost_currency;
        } else {
            $purchaseRequest = $state->getPurchaseRequest();
            $data["item"] = $purchaseRequest->Purchasable->title;
            $data["cost"] = $purchaseRequest->cost_amount;
            $data["currency"] = $state->userUpgrade->cost_currency;
        }

        $purchaser = $state->getPurchaser();
        $data["username"] = $purchaser->username;
        if (!empty($purchaser->Profile->CustomFields["minecraft_unique_id"])) {
            $data["minecraft_unique_id"] = $purchaser->Profile->CustomFields["minecraft_unique_id"];
        }

        $data["result"] = $state->paymentResult;
        $data["provided"] = getTitle();

        $message["id"] = "xenforo:payment";
        $message["data"] = $data;
        RedisIntegration::publish(json_encode($message));
    }
}
