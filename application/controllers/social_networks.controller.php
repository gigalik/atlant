<?php
class Social_networksController
{
  public function admin_index()
  {
    App::check_auth(CI);
    App::check_permission($GLOBALS['router']->getController());

    $limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
    $sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
    $offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

    return [
      'social_networks' => Db::connect('social_networks')->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
      'total' => count(Db::connect('social_networks')->select('id')->getAll())
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

			$network = Social_networks::add($_POST['data']);
			if ($network['code'] != 201) {
				Notify::createError($network['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($network['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('social_networks')->select('id')->getAll()) + 1) * 10,
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

			$social_network = Social_networks::edit($_POST['data']);
			if ($social_network['code'] != 200) {
				Notify::createError($social_network['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($social_network['message']);
			Router::redirect($redirect);
		}

		$social_network = Db::connect('social_networks')->where(['id' => (int) $_GET['id']])->get();
		if ($social_network == null) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'social_network' => $social_network,
		];
	}

  public function delete()
  {
    App::check_auth(CI);
    App::check_permission($GLOBALS['router']->getController());

    if (isset($_REQUEST['delete']) == false)
      echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

    $response = Social_networks::delete($_REQUEST);
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

			$callback['message'] = Social_networks::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}