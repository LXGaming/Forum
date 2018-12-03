<?php

namespace lolnetnz\Forum\ConnectedAccount\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Discord extends AbstractService {

    const SCOPE_EMAIL = "email";
    const SCOPE_IDENTIFY = "identify";

    public function __construct(CredentialsInterface $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = array(), UriInterface $baseApiUri = null) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri("https://discordapp.com/api/");
        }
    }

    public function getAuthorizationEndpoint() {
        return new Uri($this->baseApiUri . "/oauth2/authorize");
    }

    public function getAccessTokenEndpoint() {
        return new Uri($this->baseApiUri . "/oauth2/token");
    }

    protected function getAuthorizationMethod() {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }

    protected function parseAccessTokenResponse($responseBody) {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException("Unable to parse response.");
        } elseif (isset($data["error"])) {
            throw new TokenResponseException("Error in retrieving token: \"" . $data["error"] . "\"");
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data["access_token"]);
        unset($data["access_token"]);

        if (isset($data["expires_in"])) {
            $token->setLifeTime($data["expires_in"]);
            unset($data["expires_in"]);
        }

        if (isset($data["refresh_token"])) {
            $token->setRefreshToken($data["refresh_token"]);
            unset($data["refresh_token"]);
        }

        $token->setExtraParams($data);

        return $token;
    }

    protected function getExtraOAuthHeaders() {
        return array("Accept" => "application/json");
    }
}
