<?php
class SliderController
{
  public function admin_index()
  {
    App::check_auth(CI);
    App::check_permission($GLOBALS['router']->getController());

    $limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
    $sorting = $_COOKIE['items_sorting'] ?: 'rating asc';
    $offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

    return [
      'slider' => Db::connect('slider')->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
      'total' => count(Db::connect('slider')->select('id')->getAll())
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

			$slider = Slider::add($_POST['data']);
			if ($slider['code'] != 201) {
				Notify::createError($slider['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($slider['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('slider')->select('id')->where(['lang_code' => $_COOKIE['lang'] ?: DEFAULT_LANG])->getAll()) + 1) * 10
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

			$slider = Slider::edit($_POST['data']);
			if ($slider['code'] != 200) {
				Notify::createError($slider['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($slider['message']);
			Router::redirect($redirect);
		}

		if (!$slider = Db::connect('slider')->where(['id' => (int) $_GET['id']])->get()) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'slider' => $slider
		];
	}

  public function delete()
  {
    App::check_auth(CI);
    App::check_permission($GLOBALS['router']->getController());

    if (isset($_REQUEST['delete']) == false)
      echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

    $response = Slider::delete($_REQUEST);
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

			$callback['message'] = Slider::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}