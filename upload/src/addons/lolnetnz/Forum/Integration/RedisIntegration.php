<?php

namespace lolnetnz\Forum\Integration;

class RedisIntegration {

    private static $redis = null;
    private static $timeout = 10;

    public static function publish($message) {
        if (!self::connect()) {
            return;
        }

        self::instance()->publish("xenforo", $message);
        self::instance()->close();
    }

    public static function connect() {
        if (isset(self::$redis)) {
            return true;
        }

        $options = \XF::app()->options();
        if (!$options->lolnetnzForumRedisEnabled) {
            return false;
        }

        if (!extension_loaded("redis")) {
            \XF::logError("Missing redis extension");
            return false;
        }

        try {
            $redis = new Redis();
            if (!$redis->connect($options->lolnetnzForumRedisHost, $options->lolnetnzForumRedisPort, self::$timeout)) {
                return false;
            }

            if (!empty($options->lolnetnzForumRedisPassword) && !$redis->auth($options->lolnetnzForumRedisPassword)) {
                return false;
            }

            self::$redis = $redis;
            return true;
        } catch (RedisException $ex) {
            \XF::logException($ex);
            return false;
        }
    }

    public static function instance() {
        return self::$redis;
    }
}
