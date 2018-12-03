<?php

namespace lolnetnz\Forum\Util;

class Mojang {

	protected static $profiles = 100;

	protected function _post($data) {
		$data = json_encode($data);

		$options = array(
			"http" => array(
				"header" => "Content-Type: application/json",
				"method" => "POST",
				"content" => $data
			)
		);

		$context = stream_context_create($options);
		return json_decode(file_get_contents("https://api.mojang.com/profiles/minecraft", false, $context), true);
	}

	public function getUUIDsByNames($names) {
		if (!names) {
			return false;
		}

		$count = count($names);
		$pages = ceil($count / $this::$profiles);
		$array = array();

		for ($page = 0; $page < $pages; $page++) {
			$index = ($page * $this::$profiles);
			$fetchNames = array_slice($names, $index, $this::$profiles);

			$data = $this->_post($fetchNames);
			$array = array_merge($array, $data);
		}

		return $array;
	}

	public function getUUIDByName($name) {
		if (!$name) {
			return false;
		}

		$results = $this::_post(array($name));
		if (!isset($results[0]["id"])) {
			return false;
		}

		return $results[0]["id"];
	}

	public function getNameByUUID($uuid) {
		if (!$uuid) {
			return false;
		}

		$result = json_decode(file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/" . $uuid), true);
		if (!isset($result["name"])) {
			return false;
		}

		return $result["name"];
	}
}