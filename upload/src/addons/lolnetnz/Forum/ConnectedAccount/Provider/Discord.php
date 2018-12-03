<?php

namespace lolnetnz\Forum\ConnectedAccount\Provider;

use XF\ConnectedAccount\Provider\AbstractProvider;
use XF\Entity\ConnectedAccountProvider;

class Discord extends AbstractProvider {

    public function getOAuthServiceName() {
        return "lolnetnz\Forum:Service\Discord";
    }

    public function getDefaultOptions() {
        return [
            "client_id" => "",
            "client_secret" => ""
        ];
    }

    public function getOAuthConfig(ConnectedAccountProvider $provider, $redirectUri = null) {
        return [
            "key" => $provider->options["client_id"],
            "secret" => $provider->options["client_secret"],
            "grant_type" => "code",
            "redirect" => $redirectUri ?: $this->getRedirectUri($provider),
            "scopes" => [
                \lolnetnz\Forum\ConnectedAccount\Service\Discord::SCOPE_IDENTIFY
            ]
        ];
    }

    public function getProviderDataClass() {
        return "lolnetnz\Forum:ProviderData\Discord";
    }

    public function isValidForRegistration() {
        return false;
    }
}
