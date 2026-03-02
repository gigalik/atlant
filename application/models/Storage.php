<?php
class Storage extends App
{
	private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	public static function get_default($results = [])
	{
		return array_merge( $results, [
			'information' => $information = Db::connect('information')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get(),
			'session_user' => $_COOKIE['auth'] ? (object) User::get_data() : null,
			'controller_info' => $controller = Db::connect('controllers')->where(['name' => $GLOBALS['router']->getController(), 'lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get(),
			'social_networks' => Db::connect('social_networks')->where('status', 1)->getAll(),
			'seo' => self::get_seo($controller, $results['seo'], $information),
		]);
	}

	public static function get_seo($controller, $data = null, $information)
	{
		$controller_data = [
			'seo_title' => $controller->seo_title ?: $controller->title,
			'seo_description' => $controller->seo_description,
			'seo_keywords' => $controller->seo_keywords,
			'robots' => [
				'i' => $controller->robots_index,
				'f' => $controller->robots_follow
			]
		];

		return [
			'title' => str_ireplace(['%SITE_NAME%'], [$information->title], $data['title'] ?: $controller_data['seo_title']),
			'description' => str_ireplace(['%SITE_NAME%'], [$information->title], $data['description'] ?: $controller_data['seo_description']),
			'keywords' => preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $data['keywords'] ?: $controller_data['seo_keywords']),
			'img' => $data['img'] ? $data['img'] : '/img/splash.jpg',
			'robots' => [
				'i' => $data['robots']['i'] ?: $controller_data['robots']['i'],
				'f' => $data['robots']['f'] ?: $controller_data['robots']['f']
			]
		];
	}

	public static function json($action, $path, $name, $params = null)
	{
		switch ($action) {
			case 'check':
				return file_exists($path . DS . $name . '.json') ? true : false;

			case 'set':
				if (!file_exists($path)) {
					mkdir($path, 0755, true);
					$message = 'Directory created';
				}

				file_put_contents($path . DS . $name . '.json', json_encode($params));
				Formatting::json(['info' => $message, 'params' => $params]);
				break;

			case 'get':
				$array = json_decode(file_get_contents($path . DS . $name . '.json'), true);
				return $params ? $array[$params] : $array;

			case 'put':
				if (!file_exists($path)) {
					mkdir($path, 0755, true);
					$message = 'Directory created';
				}

				$items_list = json_decode(file_get_contents($path . DS . $name . '.json'), true);

				file_put_contents($path . DS . $name . '.json', json_encode(array_merge($items_list, $params), JSON_UNESCAPED_UNICODE));
				unset($items_list);
				break;
		}
	}

	// Cookie
	public static function cookie($action, $params)
	{
		if ($action == 'set')
			setcookie($params['title'], $params['val'], time() + 60 * 60 * 24 * ((int) $params['days'] ?: 30), "/", $_SERVER['SERVER_NAME']);

		if ($action == 'unset')
			setcookie($params['title'], '', 0, "/", $_SERVER['SERVER_NAME']);
	}

	// Session
	public static function session($action, $params)
	{
		if ($action == 'set')
			$_SESSION[$params['title']] = $params['val'];

		if ($action == 'unset')
			unset($_SESSION[$params['title']]);
	}
}
