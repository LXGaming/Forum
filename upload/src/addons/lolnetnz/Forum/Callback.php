<?php

namespace lolnetnz\Forum;

use lolnetnz\Forum\Util\Mojang;

class Callback {

	public static function verifyDiscordId(\XF\CustomField\Definition $definition, &$value, &$error) {
		$length = strlen($value);
		if ($length < 17 || $length > 19) {
			$error = \XF::phrase($definition->field_id . "_invalid_length");
			return false;
		}

		if (preg_match("/^[0-9]+$/", $value) !== 1) {
			$error = \XF::phrase($definition->field_id . "_invalid_format");
			return false;
		}

		return Callback::isAvailable($definition, $value, $error);
	}

	public static function verifyMinecraftUniqueId(\XF\CustomField\Definition $definition, &$value, &$error) {
		$length = strlen($value);
		if ($length < 32 || $length > 36) {
			$error = \XF::phrase($definition->field_id . "_invalid_length");
			return false;
		}

		if (preg_match("/^[a-zA-Z0-9-]+$/", $value) !== 1) {
			$error = \XF::phrase($definition->field_id . "_invalid_format");
			return false;
		}

		return Callback::isAvailable($definition, $value, $error);
	}

	public static function verifyMinecraftUsername(\XF\CustomField\Definition $definition, &$value, &$error) {
		$length = strlen($value);
		if ($length < 3 || $length > 16) {
			$error = \XF::phrase($definition->field_id . "_invalid_length");
			return false;
		}

		if (preg_match("/^[a-zA-Z0-9_]+$/", $value) !== 1) {
			$error = \XF::phrase($definition->field_id . "_invalid_format");
			return false;
		}

		if (!Callback::isAvailable($definition, $value, $error)) {
			return false;
		}

		$mojang = new Mojang();
		$uniqueId = $mojang->getUUIDByName($value);

		if (!$uniqueId) {
			$error = \XF::phrase($definition->field_id . "_invalid_name");
			return false;
		}

		return true;
	}

	public static function isAvailable(\XF\CustomField\Definition $definition, &$value, &$error) {
		$fieldValue = \XF::em()->findOne("XF:UserFieldValue", ["field_id" => $definition->field_id, "field_value" => $value]);
		if (!$fieldValue) {
			return true;
		}

		$user = \XF::em()->findOne("XF:User", ["user_id" => $fieldValue->user_id]);
		if ($user) {
			$visitor = \XF::visitor();
			if ($user->user_id === $visitor->user_id) {
				return true;
			}

			if ($user->canEdit()) {
				return true;
			}

			$error = \XF::phrase($definition->field_id . "_duplicate", ["username" => $user->username]);
		} else {
			$error = \XF::phrase($definition->field_id . "_duplicate", ["username" => "Unknown"]);
		}

		return false;
	}
}
