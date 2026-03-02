<?php
class Requests_prioritiesController
{
	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		return [
			'requests_priorities' => Db::connect('requests_priorities')->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('requests_priorities')->select('id')->getAll())
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

			$request_priority = Requests_priorities::add($_POST['data']);
			if ($request_priority['code'] != 201) {
				Notify::createError($request_priority['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($request_priority['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('requests_priorities')->select('id')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll()) + 1) * 10
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

			$request_priority = Requests_priorities::edit($_POST['data']);
			if ($request_priority['code'] != 200) {
				Notify::createError($request_priority['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($request_priority['message']);
			Router::redirect($redirect);
		}

		if (!$request_priority = Db::connect('requests_priorities')->where(['id' => (int) $_GET['id']])->get()) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'request_priority' => $request_priority
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Requests_priorities::delete($_REQUEST);
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

			$callback['message'] = Requests_priorities::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}
