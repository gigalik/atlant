<?php
class Docs_categoriesController
{
	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$status = $_COOKIE['display_status'] == 1 ? [0, 1] : [1];
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		return [
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('docs_categories')->select('id')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->in('status', $status)->getAll())
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

			$_POST['data']['parent_id'] = $_POST['data']['parent_id'] ?: 0;

			$docs_category = Docs_categories::add($_POST['data']);
			if ($docs_category['code'] != 201) {
				Notify::createError($docs_category['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($docs_category['message']);
			Router::redirect($redirect);
		}

		return [
			'docs_categories' => $categories = Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll(),
			'rating' => (int) (count($categories) + 1) * 10
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

			$_POST['data']['parent_id'] = $_POST['data']['parent_id'] ?: 0;

			$docs_category = Docs_categories::edit($_POST['data']);
			if ($docs_category['code'] != 200) {
				Notify::createError($docs_category['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($docs_category['message']);
			Router::redirect($redirect);
		}

		$docs_category = Db::connect('docs_categories')->where(['id' => (int) $_GET['id']])->get();
		if ($docs_category == null) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'docs_categories' => Db::connect('docs_categories')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->notWhere(['id' => $docs_category->id])->getAll(),
			'docs_category' => $docs_category
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		if (!empty($children = Db::connect('docs')->where(['category_id' => (int) $_REQUEST['id']])->getAll()))
			echo Formatting::json(['type' => 'Error', 'message' => 'This category has attachments:' . implode(', ', $children)], 409);

		$response = Docs_categories::delete($_REQUEST);
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

			$callback['message'] = Docs_categories::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}
