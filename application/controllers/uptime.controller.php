<?php
class UptimeController
{
	public function cabinet_add_task()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		// App::check_permission($GLOBALS['router']->getController());

		$module_name = 'uptime';

		if (!Storage::json('check', CONF, $module_name) == true)
			echo Langs::get('messages', 'Config not found');
		else
			$config = Storage::json('get', CONF, $module_name);

		if (isset($_POST['data']['create']))
		{
			$results = [
				'user_data' => Db::connect('users')->where(['id' => (int) ($_POST['creator_id'] ? : User::get_data()['id'])])->get(),
			];

			$_POST['data']['title'] = mb_strimwidth(strip_tags($_POST['description']), 0, 100, "...");

			$uptime = new Uptime;
			$uptime->storeFormValues(['token' => $config['api_key']]);
			$result = json_decode($uptime->query('add_task', $_POST['data'], $results['user_data']));

			// Log::set(User::get_data()['id'], 'Add task', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction(), $task['insert_id'], ['comment' => $_POST['data']['description'], 'id' => $task['insert_id']]);

			Notify::createSuccess($result->message ? : '');
			Router::redirect($redirect);
		}

		$params = json_decode(file_get_contents($config['basic_url'] . '/get_params?token=' . $config['api_key'] . '&lang=' . ($_COOKIE['lang'] ?: DEFAULT_LANG)));

		return [
			'params' => $params,
			'tasks_types' => $params->task_types,
			'tasks_priorities' => $params->task_priority,
		];
	}

	public function cabinet_get_task_info()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
    App::check_permission($GLOBALS['router']->getController());

		$uptime = new Uptime;
		$uptime->storeFormValues(['token' => $_GET['token']]);
		$res = json_decode($uptime->query('get_task_info', $_GET, Db::connect('users')->where(['id' => (int) User::get_data()['id']])->get()), true);

		return [
			'task' => $res['task'],
			'reviews' => $res['reviews'],
			'logs' => $res['logs']
		];
	}

	public function change_task()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$results = [
			'user_data' => Db::connect('users')->where(['id' => (int) User::get_data()['id']])->get(),
		];

		$uptime = new Uptime;
		$uptime->storeFormValues(['token' => $_REQUEST['token']]);
		$result = json_decode($uptime->query('change_task', $_REQUEST, $results['user_data']));

		Notify::create(null, 'Uptime', $result->type, $result->message);
		Router::redirect($_SERVER['HTTP_REFERER']);
		die();
	}

	public function add_task_comment()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$redirect = $_REQUEST['redirect'] ? base64_decode($_REQUEST['redirect']) : $_SERVER['HTTP_REFERER'];

		$module_name = 'uptime';

		if (!Storage::json('check', CONF, $module_name) == true)
			echo Langs::get('messages', 'Config not found');
		else
			$config = Storage::json('get', CONF, $module_name);

		$uptime = new Uptime;
		$uptime->storeFormValues(['token' => $config['api_key']]);
		$result = json_decode($uptime->query('add_comment', $_REQUEST['data'], Db::connect('users')->where(['id' => (int) User::get_data()['id']])->get()));

		Notify::create(null, 'Uptime', $result->type, $result->message);
		Router::redirect($redirect);
	}
}
