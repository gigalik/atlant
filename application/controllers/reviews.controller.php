<?php
class ReviewsController
{
	public function add() {
		$redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : $_SERVER['HTTP_REFERER'];

		if($_POST['token'] != $_SESSION['token'])
		{
			Notify::create(null, 'Отзыв', 'error', 'Произошла ошибка');
			Router::redirect($redirect);
		}

		$user = User::check($_POST['user']);

		$_POST['data']['title'] = strip_tags(trim($_POST['user']['name']));
		$_POST['data']['user_id'] = $user->id ?: $user['id'];
		$_POST['data']['message'] = json_encode($_POST['data']['message']);

		Reviews::add($_POST['data']);

		/** TELEGRAM **/
		if (Storage::json('check', CONF, 'telegram') != false)
		{
			$telegram_access = Storage::json('get', CONF, 'telegram');

			$message = "-------------------------------------------- \n";
			$message .= "Новая #отзыв на " . $_SERVER['SERVER_NAME'] . " \n";
			$message .= "-------------------------------------------- \n";

			$_POST['user']['name'] ? $message .= "<b>Клиент: </b>" .  $_POST['user']['name'] . " \n" : '';
			$_POST['user']['phone'] ? $message .= "<b>Телефон: </b>" . Formatting::phone($_POST['user']['phone']) . " \n" : '';
			$_POST['user']['email'] ? $message .= "<b>Email: </b>" . $_POST['user']['email'] . " \n" : '';
			$_POST['data']['source'] ? $message .= "<b>Страница отправки: </b>" . $_POST['data']['source'] . " \n" : '';
			$_POST['data']['message'] ? $message .= "<b>Cjj: </b>" . $_POST['data']['title'] . " \n" : '';
			$message .= "<b>Дата оформления: </b>" . date('d.m.Y H:i:s') . " \n";
			$message .= "<b>Комментарий:</b> " . $_POST['data']['message'] . "\n";

			$telegram_post = new Telegram;
			$telegram_post->storeFormValues($telegram_access);
			$telegram_post->post(['text' => $message]);
		}

		Notify::create(null, 'Отзыв', 'success', 'Ваш отзыв отправлен на проверку');

		Router::redirect($redirect);
	}

	public function detail()
	{
		if (!$review = Db::connect('reviews')->where(['sef' => strip_tags($GLOBALS['router']->getSef())])->get())
			Router::redirect('/' . $GLOBALS['router']->getController());

		if ($review->status != 1)
			Router::get_code(404, true);

		Db::connect('reviews')->where(['id' => (int) $review->id])->update(['views' => (int) $review->views + 1]);

		return [
			'review' => $review,
			'seo' => [
				'title' => $review->seo_title ?: $review->title,
				'description' => strip_tags($review->seo_title ?: $review->description),
				'robots' => [
					'i' => $review->robots_index ?: 'index',
					'f' => $review->robots_follow ?: 'follow',
				]
			]
		];
	}

	public function admin_index()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];

		return [
			'reviews' => $reviews = Db::connect('reviews')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'users' => $reviews ? Db::connect('users')->select('id, name, surname, phone, username, img')->in('id', array_column($reviews, 'user_id'))->getAll() : [],
			'services' => $reviews ? Db::connect('services')->select('id, title, sef, img')->in('id', array_column($reviews, 'service_id'))->getAll() : [],
			'total' => count(Db::connect('reviews')->select('id')->where($where)->getAll())
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

			$review = Reviews::add($_POST['data']);
			if ($review['code'] != 201) {
				Notify::createError($review['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($review['message']);
			Router::redirect($redirect);
		}

		return [
			'rating' => (int) (count(Db::connect('reviews')->select('id')->getAll()) + 1) * 10,
			'services' => Db::connect('services')->select('id, title')->whereNull('parent_id')->getAll(),
			'users' => Db::connect('users')->select('id, name, surname')->getAll(),
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

			$review = Reviews::edit($_POST['data']);
			if ($review['code'] != 200) {
				Notify::createError($review['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($review['message']);
			Router::redirect($redirect);
		}

		if (!$review = Db::connect('reviews')->where(['id' => (int) $_GET['id']])->get())
			die(Langs::get('messages', 'Not found'));

		return [
			'review' => $review,
			'services' => Db::connect('services')->select('id, title')->whereNull('parent_id')->getAll(),
			'users' => Db::connect('users')->select('id, name, surname')->getAll(),
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Reviews::delete($_REQUEST);
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

			$callback['message'] = Reviews::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}
