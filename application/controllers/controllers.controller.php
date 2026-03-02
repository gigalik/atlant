<?php
class ControllersController
{
	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;
		$where = [];
		$where['lang_code'] = $_COOKIE['lang'] ?: DEFAULT_LANG;
		$_GET['type'] ? $where['type'] = strip_tags($_GET['type']) : '';

		return [
			'controllers' => Db::connect('controllers')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'controller_types' => Db::connect('controller_types')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll(),
			'total' => count(Db::connect('controllers')->select('id')->where($where)->getAll())
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

			$controller = Controllers::add($_POST['data']);
			if ($controller['code'] != 201) {
				Notify::createError($controller['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($controller['message']);
			Router::redirect($redirect);
		}

		return [
			'controller_types' => Db::connect('controller_types')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll(),
			'usergroups' => Db::connect('users_groups')->getAll(),
			'rating' => (int) (count(Db::connect('controllers')->select('id')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll()) + 1) * 10
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

			$controller = Controllers::edit($_POST['data']);
			if ($controller['code'] != 200) {
				Notify::createError($controller['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($controller['message']);
			Router::redirect($redirect);
		}

		$controller = Db::connect('controllers')->where(['id' => (int) $_GET['id']])->get();
		if ($controller == null) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'controller' => $controller,
			'controller_types' => Db::connect('controller_types')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->orderBy('rating asc')->getAll(),
			'usergroups' => Db::connect('users_groups')->getAll(),
			'access' => explode(',', str_replace(['{', '}', '[', ']', '"'], '', $controller->access))
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Controllers::delete($_REQUEST);
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

			$callback['message'] = Controllers::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}
