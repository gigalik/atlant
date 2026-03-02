<?php
class App
{
	private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"

	// PERMISSIONS
	public static function check_auth(?string $referer = null)
	{
		$referer = str_replace('/homepage', '', $referer);

		if (!$_COOKIE['auth'] && $GLOBALS['router']->getController() != 'auth')
			return Router::redirect("/auth?redirect=" . base64_encode($referer));

		$user_data = User::get_data();

		if ($user_data['status'] != User::ACTIVE) {
			session_destroy();
			Storage::cookie('unset', ['title' => 'auth']);

			Router::set_code(403);
			exit();
		}
	}

	public static function check_permission(?string $controller = null, string $response_type = 'bool')
	{
		if (User::get_data()['usergroup'] == 1)
			switch ($response_type) {
				case 'code':
					return Router::set_code(200);
				case 'bool':
					return true;
			}

		if ($controller == null)
			switch ($response_type) {
				case 'code':
					return Router::set_code(500, true);
				case 'bool':
					return false;
			}

		if (!empty($controller_params = Db::connect('controllers')->select('name, access, lang_code')->where(['name' => strip_tags($controller), 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get()))
			if (User::get_data()['usergroup'] != 1 && (User::get_data()['usergroup'] == null || !in_array(User::get_data()['usergroup'], json_decode($controller_params->access, true) ?: [])))
				switch ($response_type) {
					case 'code':
						return Router::set_code(403, true);
					case 'bool':
						return false;
				}

		switch ($response_type) {
			case 'code':
				return Router::set_code(200);
			case 'bool':
				return true;
		}
	}

	public static function check_permission_manual(array $role = [4])
	{
		if (!in_array(User::get_data()['usergroup'], $role))
			Router::set_code(403, true);

		return true;
	}

	public static function get_nav($method_prefix = 'public_')
	{
		$nav = [];

		switch (substr($method_prefix, 0, -1)) {
			case 'admin':
				$controller_types = Db::connect('controller_types')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll();
				$controllers = Db::connect('controllers')->where(['status' => 1, 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll();

				foreach ($controller_types as $t_key => $type) {
					array_push($nav, [
						'id' => (int) $type->id,
						'title' => $type->title,
						'description' => $type->description,
						'rating' => (int) $type->rating,
						'items' => []
					]);

					foreach ($controllers as $key => $controller) {
						if (($controller->type == $type->id) && (in_array(User::get_data()['usergroup'], json_decode($controller->access) ?: [1]))) {
							array_push($nav[$t_key]['items'], [
								'id' => (int) $controller->id,
								'title' => $controller->title,
								'name' => $controller->name,
								'description' => $controller->description,
								'rating' => (int) $controller->rating,
								'access' => $controller->access,
								'display' => $controller->display,
							]);
						}
					}
				}
				break;

			default:
				foreach (Db::connect('navbar')->where(['method_prefix' => $method_prefix, 'display' => 1, 'parent_id' => 0, 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll() as $p_key => $parent) {
					array_push($nav, [
						'id' => (int) $parent->id,
						'title' => $parent->title,
						'link' => $parent->link,
						'rating' => (int) $parent->rating,
						'items' => []
					]);

					foreach (Db::connect('navbar')->where(['method_prefix' => $method_prefix, 'display' => 1, 'parent_id' => $parent->id, 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll() as $key => $child) {
						if ($child->parent_id == $parent->id) {
							array_push($nav[$p_key]['items'], [
								'id' => (int) $child->id,
								'title' => $child->title,
								'link' => $child->link,
								'rating' => (int) $child->rating,
								'access' => $child->access,
								'display' => $child->display,
							]);
						}
					}
				}
				break;
		}

		return $nav;
	}

	public static function get_scripts($position)
	{
		return Db::connect('scripts')->where(['status' => 1, 'position' => $position])->getAll();
	}

	public static function is_mobile()
	{
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}

	public static function get_discount (float $price = 0, int $discount = 0)
	{
		if(($discount != null) || ($discount != 0))
			$discount = $price * ($discount / 100);

		return $price - $discount;
	}
}