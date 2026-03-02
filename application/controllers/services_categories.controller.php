<?php
class Services_categoriesController
{
	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		//Получить товары в проектах
		$services_categories = Db::connect('services_categories');
		$count_partners = clone $services_categories;

		return [
			'services_categories' => $services_categories->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count($count_partners->select('id')->getAll()),
			'services' => Db::connect('services')->orderBy('rating ASC')->getAll(),
		];
	}

	public function admin_add()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['add']) || isset($_POST['edit'])) {
			if ($_POST['data'] == null) {
				Notify::createError(Langs::get('messages', 'Data not sending'));
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			$_POST['data']['seo_title'] = $_POST['data']['seo_title'] ?: $_POST['data']['title'];

			if (isset($_POST['edit']))
				$service_category = Services_categories::edit($_POST['data']);
			else if (isset($_POST['add']))
				$service_category = Services_categories::add($_POST['data']);

			if ($service_category['code'] != 200 && $service_category['code'] != 201) {
				Notify::createError($service_category['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($service_category['message']);
			Router::redirect($redirect);
		}

		return [
			'service_category' => (object) [
				'robots_index' => 'index',
				'robots_follow' => 'follow',
				'status' => 1,
				'rating' => (int) (count(Db::connect('services_categories')->select('id')->getAll()) + 1) * 10
			],
		];
	}

	public function admin_edit()
	{
		$results = $this->admin_add();

		if (!$service_category = Db::connect('services_categories')->where(['id' => (int) $_GET['id']])->get()) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		$results['service_category'] = $service_category;

		return $results;
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		if (!empty($children = Db::connect('services')->select('title')->where(['category_id' => (int) $_REQUEST['id']])->getAll()))
			echo Formatting::json(['type' => 'Error', 'message' => 'This category has attachments:' . implode($children, ', ')], 409);

		$response = Services_categories::delete($_REQUEST);
		echo Formatting::json($response['data'], $response['code']);
	}

	public function api_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) {
			if ($_POST['data'] == null)
				echo Formatting::json(['message' => Langs::get('messages', 'Data not sending')], 400);

			$controller = Services_categories::edit($_POST['data']);
			echo Formatting::json(['message' => Langs::get('messages', $controller['message'])], $controller['code']);
		}

		die();
	}
}
