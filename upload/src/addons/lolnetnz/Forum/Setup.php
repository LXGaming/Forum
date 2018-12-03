<?php

namespace lolnetnz\Forum;

use XF\AddOn\AbstractSetup;

class Setup extends AbstractSetup {

	public function install(array $stepParams = []) {
		$this->db()->insert("xf_connected_account_provider", [
			"provider_id" => "Discord",
			"provider_class" => "lolnetnz\Forum:Provider\Discord",
			"display_order" => 10,
			"options" => "",
		]);
	}

	public function upgrade(array $stepParams = []) {
		// TODO: Implement upgrade() method.
	}

	public function uninstall(array $stepParams = []) {
		$this->db()->delete("xf_connected_account_provider", [
			"provider_id" => "Discord",
		]);
	}
}