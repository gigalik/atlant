<?php
class AdvantagesController
{
	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$status = $_COOKIE['display_status'] == 1 ? [0, 1] : [0, 1];
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];

		return [
			'advantages' => Db::connect('advantages')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('advantages')->select('id')->where($where)->getAll())
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

			$advantage = Advantages::add($_POST['data']);
			if ($advantage['code'] != 201) {
				Notify::createError($advantage['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($advantage['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('advantages')->select('id')->getAll()) + 1) * 10
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

			$advantage = Advantages::edit($_POST['data']);
			if ($advantage['code'] != 200) {
				Notify::createError($advantage['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($advantage['message']);
			Router::redirect($redirect);
		}

		$advantage = Db::connect('advantages')->where(['id' => (int) $_GET['id']])->get();
		if ($advantage == null) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'advantage' => $advantage
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Advantages::delete($_REQUEST);
		echo Formatting::json($response['data'], $response['code']);
	}

	public function api_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) {
			if ($_POST['data'] == null)
				echo Formatting::json(['message' => Langs::get('messages', 'Data not sending')], 400);

			$controller = Advantages::edit($_POST['data']);
			echo Formatting::json(['message' => Langs::get('messages', $controller['message'])], $controller['code']);
		}

		die();
	}
}
