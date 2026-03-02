<?php
class Controller_typesController
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

		return [
			'controller_types' => Db::connect('controller_types')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('controller_types')->select('id')->where($where)->getAll())
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

			$controller_type = Controller_types::add($_POST['data']);
			if ($controller_type['code'] != 201) {
				Notify::createError($controller_type['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($controller_type['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('controller_types')->select('id')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll()) + 1) * 10
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

			$controller_type = Controller_types::edit($_POST['data']);
			if ($controller_type['code'] != 200) {
				Notify::createError($controller_type['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($controller_type['message']);
			Router::redirect($redirect);
		}

		$controller_type = Db::connect('controller_types')->where(['id' => (int) $_GET['id']])->get();
		if ($controller_type == null) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'controller_type' => $controller_type
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		if (!empty($children = Db::connect('controllers')->select('title')->where(['category_id' => (int) $_REQUEST['id']])->getAll()))
			echo Formatting::json(['type' => 'Error', 'message' => 'This category has attachments:' . implode(', ', $children)], 409);

		$response = Controller_types::delete($_REQUEST);
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

			$callback['message'] = Controller_types::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}
