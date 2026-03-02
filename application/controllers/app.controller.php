<?php
class AppController
{
	public function index()
	{
		return Router::set_code(403);
	}

	public function cabinet_file()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		if (!isset($_FILES['upload'])) {
			die(json_encode([
				'status' => false,
				'error' => [
					'message' => 'File not submited'
				]
			]));
		}

		$file_name = 'upload' . time();

		$file = Images::upload_image($file_name, 'upload', 'fullsize', $_FILES["upload"]['tmp_name'], ['extention' => 'jpg', 'new_width' => 1024]);
		die(json_encode([
			'status' => true,
			'url' => '/img/upload/fullsize/' .	$file_name . '.jpg'
		]));
	}



	// UTILITIES
	// Cookie
	public function cookie()
	{
		if (isset($_REQUEST['title']))
			Storage::cookie($_REQUEST['action'] ?: 'set', ['title' => $_REQUEST['title'], 'val' => $_REQUEST['val']]);

		die(Formatting::json(['info' => 'Cookie ' . $_REQUEST['title'] . ' set value: ' . $_REQUEST['val']]));
	}

	// Session
	public function session()
	{
		if (isset($_REQUEST['title']))
			Storage::session($_REQUEST['action'] ?: 'set', ['title' => $_REQUEST['title'], 'val' => $_REQUEST['val']]);

		die(Formatting::json(['info' => 'Cookie ' . $_REQUEST['title'] . ' set value: ' . $_REQUEST['val']]));
	}

	// Translit
	public function translit()
	{
		if (!empty($_REQUEST['value'])) {
			$code = 200;
			$data = [
				'string' => Text::translit($_REQUEST['value'], $_REQUEST['type'] ?: 'sef'),
			];
		} else {
			$code = 400;
			$data = [];
		}

		die(Formatting::json($data, $code));
	}

	// Sortable
	public function sortable () 
	{	
		if(!isset($_GET['sort']))
			die(json_encode(['message' => 'Something went wrong']));

		if(!isset($_GET['table']))
			die(json_encode(['message' => 'Table not set']));

		$table = Utilites::check_str($_GET['table']);

		foreach ($_POST['sort'] as $i => $row) {
			Db::connect($table)->where(['id' => intval($row)])->update(['rating' => ($i + 1) * 10]);
		}

		die(json_encode(['message' => 'Done']));
	}


	function currency()
	{
		$valute_rates = json_decode(file_get_contents('https://www.cbr-xml-daily.ru/daily_json.js'));

		if ($_GET['cur'] == 'us') {
			$value = $_GET['val'] * $valute_rates->Valute->USD->Value;
		}

		echo $value;
		die();
	}

	public function change_language()
	{
		Storage::cookie('set', ['title' => 'lang', 'val' => $_REQUEST['code'] ?: DEFAULT_LANG]);
		Router::redirect($_SERVER['HTTP_REFERER']);
	}

	public function generate_password()
	{
		if (!empty($_POST['generate'])) {
			$code = 200;
			$data = [
				'password' => User::generate_password((int) $_POST['length'] ?: random_int(8, 15)),
			];
		} else {
			$code = 400;
			$data = [];
		}

		die(Formatting::json($data, $code));
	}

	public function relogin()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		if (!empty($_GET['id']) && !empty($new_user = Db::connect('users')->select('*')->where(['id' => $_GET['id']])->get())) {
			if ($new_user->usergroup >= 2) {
				$_SESSION['username'] = base64_decode($new_user->username);
				User::get_data()['id'] = $new_user->id;
				$_SESSION['usergroup'] = $new_user->usergroup;
				$_SESSION['userstatus'] = $new_user->status;
			}
		}

		Router::redirect('/admin');
	}

	public function multiple()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if($_POST['controller'] == 'catalog') $_POST['controller'] = 'goods';

		$items = explode(',', $_POST['values']);
		$controller = $_POST['controller'];

		App::check_permission($_POST['controller']);

		if ($_POST['clone'] == true)
			foreach ($items as $value)
				$controller::clone(['id' => $value]);

		if ($_POST['delete'] == true)
			foreach ($items as $value)
				$controller::delete(['id' => $value]);

		Router::redirect($redirect);
	}

	public function discount()
	{
		$price = Utilites::check_str($_POST['data']['price'], 'double');
		$discount = Utilites::check_str($_POST['data']['discount'], "int");

		die(json_encode(Formatting::money(Utilites::get_discount($price, $discount))));
	}

	public function tg_init()
	{
		if (Storage::json('check', CONF, 'telegram') != false)
		{
			$telegram_access = Storage::json('get', CONF, 'telegram');
			echo '<pre>';
			print_r(json_decode(Telegram::getUpdates($telegram_access['token'])));
			echo '</pre>';
		}

		die();
	}
}
