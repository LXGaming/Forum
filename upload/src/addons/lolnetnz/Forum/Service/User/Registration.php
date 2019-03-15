<?php

namespace lolnetnz\Forum\Service\User;

use lolnetnz\Forum\Integration\RedisIntegration;

class Registration extends XFCP_Registration {

    protected function sendRegistrationContact() {
        parent::sendRegistrationContact();

        $user = $this->user;
        $message["username"] = $user->username;
        if (!empty($user->Profile->CustomFields["minecraft_username"])) {
            $message["minecraft_username"] = $user->Profile->CustomFields["minecraft_username"];
        }

        RedisIntegration::publish(json_encode($message));
    }
}
