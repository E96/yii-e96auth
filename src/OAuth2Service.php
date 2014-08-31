<?php
// vim: sw=4:ts=4:noet:sta:
namespace E96Auth;
use \EOAuth2Service;

class OAuth2Service extends EOAuth2Service {
	protected $accountInfoUrl;

	protected $uid = null;

	protected function getAccessToken($code) {
		$params = array(
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $this->getState('redirect_uri'),
		);
		return $this->makeRequest($this->getTokenUrl($code), [ 'data' => $params ]);
	}

	protected function saveAccessToken($token) {
		$this->setState('auth_token', $token->access_token);
		$this->setState('expires', time() + (isset($token->expires_in) ? $token->expires_in : 365 * 86400) - 60);
		$this->access_token = $token->access_token;
	}

	protected function fetchAttributes() {
		$info = $this->makeSignedRequest($this->accountInfoUrl);
		$this->attributes['id'] = $info->id;
		$this->attributes['name'] = $info->login;
		$this->attributes['displayName'] = $info->name;
	}
}
