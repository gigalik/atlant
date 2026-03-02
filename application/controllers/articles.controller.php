<?php
class ArticlesController
{
	public function index()
	{
		$limit = (int) $_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE;
		$sorting = 'publication_date DESC, rating ASC';
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		return [
			'news' => Db::connect('articles')->where(['status' => 1])->orderBy($sorting)->limit($limit)->offset($offset)->getAll(),
		];
	}

	public function category()
	{
		if (!$category = Db::connect('articles_categories')->where(['sef' => strip_tags($GLOBALS['router']->getSef())])->get())
			Router::redirect('/' . $GLOBALS['router']->getController());

		if ($category->status != 1)
			Router::get_code(404, true);

		Db::connect('articles_categories')->where(['id' => (int) $category->id])->update(['views' => (int) $category->views + 1]);

		$limit = (int) $_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE;
		$sorting = 'publication_date DESC, rating ASC';
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		return [
			'category' => $category,
			'news' => Db::connect('articles')->where(['status' => 1, 'category_id' => (int) $category->id])->orderBy($sorting)->limit($limit)->offset($offset)->getAll(),
			'seo' => [
				'title' => $category->seo_title ?: $category->title . ' | %SITE_NAME%',
				'description' => strip_tags($category->seo_title ?: $category->description),
				'robots' => [
					'i' => $category->robots_index ?: 'index',
					'f' => $category->robots_follow ?: 'follow',
				]
			]
		];
	}

	public function post()
	{
		if (!$article = Db::connect('articles')->where(['sef' => strip_tags($GLOBALS['router']->getSef())])->get())
			Router::redirect('/' . $GLOBALS['router']->getController());

		if ($article->status != 1)
			Router::get_code(404, true);

		Db::connect('articles')->where(['id' => (int) $article->id])->update(['views' => (int) $article->views + 1]);

		return [
			'article' => $article,
			'category' => Db::connect('articles_categories')->where(['id' => $article->category_id])->get(),
			'seo' => [
				'title' => $article->seo_title ?: $article->title . ' | %SITE_NAME%',
				'description' => strip_tags($article->seo_title ?: $article->description),
				'robots' => [
					'i' => $article->robots_index ?: 'index',
					'f' => $article->robots_follow ?: 'follow',
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
			'articles' => Db::connect('articles')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'total' => count(Db::connect('articles')->select('id')->where($where)->getAll())
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

			$article = Articles::add($_POST['data']);
			if ($article['code'] != 201) {
				Notify::createError($article['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($article['message']);
			Router::redirect($redirect);
		}

		return [
			'articles_categories' => Db::connect('articles_categories')->select('id, title')->getAll(),
			'rating' => (int) (count(Db::connect('articles')->select('id')->getAll()) + 1) * 10,
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

			$article = Articles::edit($_POST['data']);
			if ($article['code'] != 200) {
				Notify::createError($article['message']);
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			Notify::createSuccess($article['message']);
			Router::redirect($redirect);
		}

		$article = Db::connect('articles')->where(['id' => (int) $_GET['id']])->get();
		if ($article == null) {
			Notify::createError(Langs::get('messages', 'Not found'));
			Router::redirect($_SERVER['HTTP_REFERER']);
		}

		return [
			'article' => $article,
			'articles_categories' => Db::connect('articles_categories')->select('id, title')->getAll(),
		];
	}

	public function delete()
	{
		App::check_auth(CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_REQUEST['delete']) == false)
			echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

		$response = Articles::delete($_REQUEST);
		echo Formatting::json($response['data'], $response['code']);
	}

	public function api_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit'])) {
			if ($_POST['data'] == null)
				echo Formatting::json(['message' => Langs::get('messages', 'Data not sending')], 400);

			$controller = Articles::edit($_POST['data']);
			echo Formatting::json(['message' => Langs::get('messages', $controller['message'])], $controller['code']);
		}

		die();
	}
}
