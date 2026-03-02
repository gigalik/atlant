<?php
class SettingsController 
{
  public function index () {
    Router::set_code(403);
  }

  public function cabinet_index()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		return [];
	}

  public function cabinet_config()
	{
		App::check_auth(SITE_URL);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['update'])) {
			if (!$info = Db::connect('information')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get()) {
				Notify::createError(Langs::get('messages', 'Not found'));
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) {
				if (!empty($info->img)) foreach (json_decode($info->img) as $img) Images::remove($img);

				$filename = explode('.', $_FILES['image']['name']);

				if($_FILES['image']['type'] == 'image/svg+xml') {
					$path = [
						'fullsize' => DS . IMG . DS . $GLOBALS['router']->getController() . '/svg/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
					];
				} else {
					$path = [
						'fullsize' => DS . IMG . DS . $GLOBALS['router']->getController() . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
						'thumb' => DS . IMG . DS . $GLOBALS['router']->getController() . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
					];
				}

				$_POST['app']['img'] = json_encode($path);

				foreach($path as $key => $item)
					Images::upload($_FILES['image']['tmp_name'], ROOT . $item, $filename[1]);
			}

			if (isset($_POST['deleteImage']) && $_POST['deleteImage'] == "on") if (!empty($info->img)) foreach (json_decode($info->img) as $img) Images::remove($img);

			if ($_POST['requisites'] != null)
				$_POST['app']['requisites'] = json_encode($_POST['requisites']);

			$_POST['app']['phone'] = json_encode($_POST['app']['phone']);

			Db::connect('information')->where(['id' => $info->id])->update($_POST['app']);

			Router::redirect(CI);
		}

		return [
			'app' => Db::connect('information')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->get(),
			'seo' => [
				'title' => Langs::get('titles', 'Setup application'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	public function cabinet_db()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['save'])) {
			Storage::json('set', CONF, 'db', $_POST['db']);
			Router::redirect(CI);
		}

		return [
			'db' => Storage::json('get', CONF, 'db'),
			'seo' => [
				'title' => Langs::get('titles', 'Setup database'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	public function cabinet_extensions()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if ($ext = $GLOBALS['router']->getSef()) {
			if (Storage::json('check', CONF, $ext) == true) {
				return [
					'extension_path' => $extension_path = str_replace(ROOT, '', EXTENSIONS) . DS . $ext,
					'extension_info' => Storage::json('get', $extension_path . DS, 'info'),
					'extension_params' => Storage::json('get', CONF, $ext)
				];
			} else {
				return [
					'extension_path' => $extension_path = str_replace(ROOT, '', EXTENSIONS) . DS . $ext,
					'extension_info' => Storage::json('get', $extension_path . DS, 'info'),
				];
			}
		}

		if (isset($_POST['get'])) {
			if ($_POST['name'] == 'yandex') {
				$url = $_POST['ext']['basic_url'] . http_build_query(['response_type' => $_POST['ext']['response_type'], 'client_id' => $_POST['ext']['client_id']]);
			}

			Storage::json('set', CONF, $_POST['name'], $_POST['ext']);
			Router::redirect($url);
		}

		if (isset($_POST['save'])) {
			Storage::json('set', CONF, $_POST['name'], $_POST['ext']);
			Router::redirect($redirect);
		}

		return [
			'seo' => [
				'title' => Langs::get('titles', 'Extensions'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	public function cabinet_langpack()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		if(isset($_POST['update']))
			file_put_contents(CONF . '/lang' . DS . ($_COOKIE['lang'] ?: DEFAULT_LANG) . '.json', json_encode($_POST['pack'], JSON_UNESCAPED_UNICODE));

		$path = CONF . '/lang';
		$filename = $_COOKIE['lang'] ?: DEFAULT_LANG;

		return [
			'pack' => Storage::json('get', $path, $filename)
		];
	}

  // DATABASE SERVICE
	public function service($table, $action)
	{
		App::check_auth(CI);
		App::check_permission_manual([1, 2]);

		$table = Utilites::check_str($_REQUEST['table']);
		$action = Utilites::check_str($_REQUEST['action']);

		die(json_encode(Db::service($table, $action)));
	}
}