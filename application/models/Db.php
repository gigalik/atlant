<?php
require_once ROOT . '/vendor/autoload.php';

class Db extends App
{
	private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"

	public static function connect(?string $table_name = null): ?Buki\Pdox
	{
		if (Storage::json('check', CONF, 'db') == false)
			return null;

		$db = new Buki\Pdox(Storage::json('get', CONF, 'db'));

		if ($table_name != null)
			$db = $db->table($table_name);

		return $db;
	}

	// public static function query($query, $all = true, $type = null, $argument = null): ?Buki\Pdox
	public static function query($query): ?Buki\Pdox
	{
		if (Storage::json('check', CONF, 'db') == false)
			return null;

		$db = new Buki\Pdox(Storage::json('get', CONF, 'db'));

		if ($query != null)
			$db = $db->query($query);

		return $db;
	}

	public static function service($table, $action)
	{
		if (Storage::json('check', CONF, 'db') == false)
			return null;

		$db = new Buki\Pdox(Storage::json('get', CONF, 'db'));

		if ($table != null)
		{
			switch ($action) {
				case 'analyze':
						$db = $db->table($table)->analyze();
					break;
				case 'check':
						$db = $db->table($table)->check();
					break;
					case 'checksum':
						$db = $db->table($table)->checksum();
					break;
				case 'optimize':
						$db = $db->table($table)->optimize();
					break;
				case 'repair':
						$db = $db->table($table)->repair();
					break;
			}
		}

		return $db;
	}
}
