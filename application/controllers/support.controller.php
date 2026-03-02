<?php
class SupportController
{
  public function index () {
    Router::set_code(403);
  }

  public function cabinet_index ()
	{
		App::check_auth($redirect = CI);
		// App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: 'id desc';
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$_GET['type'] ? $where['type'] = strip_tags($_GET['type']) : '';

		$module_name = 'uptime';

		if (!Storage::json('check', CONF, $module_name) == true)
			return Router::set_code(204);
		else
			$config = Storage::json('get', CONF, $module_name);

		$params = json_decode(file_get_contents($config['basic_url'] . '/get_params?' . http_build_query(['token' => $config['api_key'], 'lang' => $_COOKIE['lang'] ?: DEFAULT_LANG])));

		$where = [
			'limit' => $limit,
			'offset' => $offset,
			'order' => $sorting,
			'token' => $config['api_key'],
			'lang' => $_COOKIE['lang'] ?: DEFAULT_LANG,
		];

		$_GET['status'] ? $where['status'] = strip_tags($_GET['status']) : '';

		$tasks_list = json_decode(file_get_contents($config['basic_url'] . '/get_list_task?' . http_build_query($where))); 

		return [
			'config' => $config,
			'tasks_types' => $params->task_types,
			'tasks_priority' => $params->task_priority,
			'tasks_statuses' => $params->task_statuses,
			'tasks'	=> $tasks_list->tasks,
			'total' => $tasks_list->tasks_total ? : 0,
			'seo' => [
				'title' => 'Помощь'
			]
		];
	}

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
    // App::check_permission($GLOBALS['router']->getController());

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

		if (isset($_REQUEST['add']))
		{
			$module_name = 'uptime';

			if (!Storage::json('check', CONF, $module_name) == true)
				echo Langs::get('messages', 'Config not found');
			else
				$config = Storage::json('get', CONF, $module_name);

			$results = [
				'user_data' => Db::connect('users')->where(['id' => (int) User::get_data()['id']])->get(),
			];

			$uptime = new Uptime;
			$uptime->storeFormValues(['token' => $config['api_key']]);
			$result = json_decode($uptime->query('add_comment', $_REQUEST['data'], $results['user_data']), true);

			Notify::create(null, 'Uptime', $result['type'], $result['message']);
		}

		Router::redirect($_SERVER['HTTP_REFERER']);
		die();
	}
}