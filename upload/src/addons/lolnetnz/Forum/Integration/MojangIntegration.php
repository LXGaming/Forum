<?php

namespace lolnetnz\Forum\Integration;

class MojangIntegration {

    private static $profiles = 100;

    private static function _post($data) {
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

    public static function getUUIDsByNames($names) {
        if (!names) {
            return false;
        }

        $count = count($names);
        $pages = ceil($count / self::$profiles);
        $array = array();

        for ($page = 0; $page < $pages; $page++) {
            $index = ($page * self::$profiles);
            $fetchNames = array_slice($names, $index, self::$profiles);

            $data = self::_post($fetchNames);
            $array = array_merge($array, $data);
        }

        return $array;
    }

    public static function getUUIDByName($name) {
        if (!$name) {
            return false;
        }

        $results = self::_post(array($name));
        if (!isset($results[0]["id"])) {
            return false;
        }

        return $results[0]["id"];
    }

    public static function getNameByUUID($uuid) {
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
