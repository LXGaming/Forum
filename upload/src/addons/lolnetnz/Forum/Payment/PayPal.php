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
            $userUpgradeId = $purchaseRequest->extra_data["user_upgrade_id"];
            if (isset($userUpgradeId)) {
                $userUpgrade = \XF::em()->find("XF:UserUpgrade", $userUpgradeId);
                if (isset($userUpgrade)) {
                    $data["item"] = $userUpgrade->title;
                } else {
                    $data["item"] = $purchaseRequest->Purchasable->title;
                }
            } else {
                $data["item"] = $purchaseRequest->Purchasable->title;
            }

            $data["cost"] = $purchaseRequest->cost_amount;
            $data["currency"] = $purchaseRequest->cost_currency;
        }

        $purchaser = $state->getPurchaser();
        $data["username"] = $purchaser->username;
        $uniqueId = $purchaser->Profile->custom_fields["minecraft_unique_id"];
        if (!empty($uniqueId)) {
            $data["minecraft_unique_id"] = preg_replace("/(\w{8})(\w{4})(\w{4})(\w{4})(\w{12})/i", "$1-$2-$3-$4-$5", $uniqueId);
        }

        $data["result"] = $state->paymentResult;
        $data["provider"] = $this->getTitle();

        $message["id"] = "forum:payment";
        $message["data"] = $data;
        RedisIntegration::publish(json_encode($message));
    }
}
