<?php
class RequestsController
{
	public function add ()
	{
		$redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : SITE_URL;

		if($_POST['token'] != $_SESSION['token'])
		{
			Notify::create(null, 'Заявка', 'error', 'Произошла ошибка');
			Router::redirect($redirect);
		}

		$user = User::check($_POST['user']);
		$_POST['data']['user_id'] = $user->id;

		Requests::add($_POST['data']);

		/** TELEGRAM **/
		if (Storage::json('check', CONF, 'telegram') != false)
		{
			$telegram_access = Storage::json('get', CONF, 'telegram');

			$message = "-------------------------------------------- \n";
			$message .= "Новая #заявка на " . $_SERVER['SERVER_NAME'] . " \n";
			$message .= "-------------------------------------------- \n";

			$_POST['data']['title'] ? $message .= "<b>Тема: </b>" . $_POST['data']['title'] . " \n" : '';
			$_POST['user']['name'] ? $message .= "<b>Клиент: </b>" .  $_POST['user']['name'] . " \n" : '';
			$_POST['user']['phone'] ? $message .= "<b>Телефон: </b>" . Formatting::phone($_POST['user']['phone']) . " \n" : '';
			$_POST['user']['email'] ? $message .= "<b>Email: </b>" . $_POST['user']['email'] . " \n" : '';
			$_POST['data']['source'] ? $message .= "<b>Страница отправки: </b>" . SITE_URL . $_POST['data']['source'] . " \n" : '';
			$message .= "<b>Дата оформления: </b>" . date('d.m.Y H:i:s') . " \n";
			$message .= "<b>Комментарий:</b> " . $_POST['data']['message'] . "\n";

			$telegram_post = new Telegram;
			$telegram_post->storeFormValues($telegram_access);
			$telegram_post->post(['text' => $message]);

			Notify::create(null, 'Заявка', 'success', 'Запрос отправлен');
		}

		Router::redirect($redirect);
	}

	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: 'status_id asc, create_date asc';
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		return [
			'requests' => $requests = Db::connect('requests')->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'requests_statuses' => Db::connect('requests_statuses')->where('status', 1)->orderBy('rating ASC')->getAll(),
			'users' => $requests ? Db::connect('users')->select('id, name, surname, phone, username, img')->in('id', array_column($requests, 'user_id'))->getAll() : [],
			'total' => count(Db::connect('requests')->select('id')->getAll())
		];
	}

	public function admin_add()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['add'])) {
			if ($_POST['data'] == null) {
				Notify::createError(Langs::get('messages', 'Data not sending'));
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			$_POST['data']['type_id'] = 1;
			$user = Db::connect('users')->where('id', $_POST['data']['user_id'])->get();
			$_POST['data']['name'] = $user->name;
			$_POST['data']['phone'] = $user->phone;
			$_POST['data']['status_id'] = 1;

			$request = Requests::add($_POST['data']);
			if ($request['code'] != 201) {
				Notify::createError($request['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($request['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('requests')->select('id')->getAll()) + 1) * 10,
			'requests_statuses' => Db::connect('requests_statuses')->where('status', 1)->orderBy('rating ASC')->getAll(),
			'users' => Db::connect('users')->getAll(),
		];
	}

	public function admin_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) {
			if ($_POST['data'] == null) {
				Notify::createError(Langs::get('messages', 'Data not sending'));
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			$request = Requests::edit($_POST['data']);
			if ($request['code'] != 200) {
				Notify::createError($request['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($request['message']);
			Router::redirect($redirect);
		}

		if (!$request = Db::connect('requests')->where(['id' => (int) $_GET['id']])->get()) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'request' => $request,
			'requests_statuses' => Db::connect('requests_statuses')->where('status', 1)->orderBy('rating ASC')->getAll(),
			'users' => Db::connect('users')->getAll(),
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Requests::delete($_REQUEST);
		echo Formatting::json($response['data'], $response['code']);
	}

	public function api_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if ($_POST['edit'] == 'ajax') 
		{
			if ($_POST['data'] == null)
				$callback['message'] = Langs::get('messages', 'Data not sending');

			$callback['message'] = Requests::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}
