<?php

namespace lolnetnz\Forum\ConnectedAccount\ProviderData;

use XF\ConnectedAccount\ProviderData\AbstractProviderData;

class Discord extends AbstractProviderData {

    public function getDefaultEndpoint() {
        return "users/@me";
    }

    public function getProviderKey() {
        return $this->requestFromEndpoint("id");
    }

    public function getUsername() {
        return $this->requestFromEndpoint("username");
    }

    public function getLocale() {
        return $this->requestFromEndpoint("locale");
    }

    public function isMFAEnabled() {
        return $this->requestFromEndpoint("mfa_enabled");
    }

    public function getFlags() {
        return $this->requestFromEndpoint("flags");
    }

    public function getAvatar() {
        return $this->requestFromEndpoint("avatar");
    }

    public function getDiscriminator() {
        return $this->requestFromEndpoint("discriminator");
    }
}
