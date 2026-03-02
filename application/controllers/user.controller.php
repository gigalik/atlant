<?php
class UserController
{
	public function index()
	{
		Router::set_code(403);
	}

	public function cabinet_index()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$status = (int) $_COOKIE['display_status'] ? [0, 1] : [0, 1];
		$sorting = $_COOKIE['items_sorting'] ?: 'id ASC, surname ASC';
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$where = [];
		$_GET['category'] ? $where['usergroup'] = Db::connect('users_groups')->select('id, code')->where(['code' => strip_tags($_GET['category'])])->get()->id : '';

		return [
			'users' => Db::connect('users')->in('status', $status)->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
			'usergroups' => Db::connect('users_groups')->getAll(),
			'search_placeholder' => 'Enter first name, last name or phone number',

			'total' => count(Db::connect('users')->select('id')->where($where)->in('status', $status)->getAll())
		];
	}

	public function cabinet_add()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['add'])) 
		{
			if (Db::connect('users')->where('username', base64_encode(strtolower(strip_tags($_POST['user']['username']))))->get() != null) {
				Notify::createError('Логин занят');
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			User::add($_POST['user']);
			Notify::createSuccess('User add successfully');
			Router::redirect(base64_decode($_POST['redirect']) ?: $redirect);
		}

		return [
			'users_groups' => Db::connect('users_groups')->where('id', '>=', User::get_data()['usergroup'])->getAll(),
		];
	}

	public function cabinet_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['edit']))
		{
			User::edit($_POST['user']);
			Notify::createSuccess(Langs::get('messages', 'User edit successfully'));
			Router::redirect($redirect);
		}

		if (!$user = Db::connect('users')->where(['id' => (int) $_GET['id']])->get())
		{
			Notify::createError(Langs::get('messages', 'User not found'));
			Router::redirect($redirect);
		}

		return [
			'user' => $user,
			'users_groups' => Db::connect('users_groups')->where('id', '>=', User::get_data()['usergroup'])->getAll(),
		];
	}

	public function cabinet_search () 
  {
    App::check_auth(CI);
    App::check_permission($GLOBALS['router']->getController());

    $sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;

		$where = [];
		$_GET['category'] ? $where['usergroup'] = Db::connect('users_groups')->select('id, code')->where(['code' => strip_tags($_GET['category'])])->get()->id : '';

    $like = '%' . strip_tags($_GET['val']) . '%';

    return [
			'users' => Db::connect('users')->where($where)->like('name', $like)->orLike('surname', $like)->orLike('patronymic', $like)->orLike('phone', '%' . Formatting::phone(strip_tags($_GET['val'])))->orderBy($sorting)->getAll(),
			'usergroups' => Db::connect('users_groups')->getAll(),
    ];
  }

	public function cabinet_profile()
	{
		App::check_auth(SITE_URL);
		App::check_permission($GLOBALS['router']->getController());

		$limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
		$offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

		$year = (int) $_GET['year'] ? : date('Y');
    $month = (int) $_GET['month'] ? : '';

    $period_from = Formatting::date($year . '-' . ($month ?: 1), 'Y-m-01');
    $period_to = Formatting::date($year . '-' . ($month ?: 12), 'Y-m-t');

		$user = Db::connect('users')->select('id, surname, name, patronymic, sex, birthday, phone, email, username, usergroup, status,img')->where(['username' => (string) base64_encode($GLOBALS['router']->getSef())])->get();

		if(!in_array(User::get_data()['usergroup'], [1, 2, 3])) {
			if($user->id != User::get_data()['user_id']) Router::redirect('/cabinet/user/profile/' . $_SESSION['username']);

			$companies = Db::connect('companies')->where(['owner_id' => User::get_data()['id']])->getAll();
			$deals = !empty($companies) ? Db::connect('deals')->in('company_id', array_column($companies, 'id'))->orderBy('create_date DESC')->getAll() : [];
			$tasks = [];
		}

		if(in_array(User::get_data()['usergroup'], [1, 2, 3])) { 
			if(!in_array($user->usergroup, [1, 2, 3])) {
				$companies = Db::connect('companies')->select('id, title')->where(['owner_id' => $user->id])->getAll();
			} else {
				foreach(Db::connect('companies')->select('id, curators')->whereNotNull('curators')->getAll() as $company)
					if(in_array($user->id, (array) json_decode($company->curators))) $arCompanies[] = $company;

				$own_companies = Db::connect('companies')->select('id')->where(['owner_id' => $user->id])->getAll();
				$all_companies = array_merge($arCompanies ?: [], $own_companies ?: []);
				$companies = !empty($all_companies) ? Db::connect('companies')->select('id, title, tin, status, date_create, source_id')->in('id', array_column($all_companies, 'id'))->getAll() : [];
			}

			$deals = !empty($companies) ? Db::connect('deals')->where(['performer_id' => $user->id])->orWhere(['creator_id' => $user->id])->orderBy('create_date DESC')->getAll() : [];
			$tasks = Db::connect('tasks')->where(['creator_id' => $user->id])->orWhere(['performer_id' => $user->id])->getAll();
		}

		if($user->usergroup == 4) {
			$companies ? $company_finance = Db::connect('finances')->between('date', $period_from.'%', $period_to.'%')->in('company_id', array_column($companies, 'id'))->orderBy('date ASC')->getAll() : [];
		}

		$personal_finance = Db::connect('finances')->between('date', $period_from.'%', $period_to.'%')->where(['user_id' => $user->id])->orderBy('date ASC')->getAll();

		return [
			'user' => $user,
			'companies' => $companies,
			// 'sources' => Db::connect('companies_sources')->select('id,title')->getAll(),

			'finances' => array_merge($personal_finance ?: [], $company_finance ?: []) ?: [],

			'requests' => Db::connect('requests')->where(['user_id' => $user->id])->orderBy('create_date DESC')->getAll() ?: [],
			'requests_statuses' => Db::connect('requests_statuses')->where('status', 1)->orderBy('rating ASC')->getAll() ?: [],

			'deals' => !empty($deals) ? $deals : [],
			'deals_statuses' => Db::connect('deals_statuses')->where('status', 1)->orderBy('rating ASC')->getAll() ?: [],

			'schedule_types' => Db::connect('schedule_types')->orderBy('rating ASC')->getAll() ?: [],
			// 'schedule_stages' => Db::connect('schedule_stages')->orderBy('rating ASC')->getAll(),

			'tasks' => $tasks ?: [],
		];
	}

	public function cabinet_settings()
	{
		App::check_auth($redirect = CI);

		return [
			'user' => Db::connect('users')->select('*')->where(['id' => (int) User::get_data()['id']])->get(),
		];
	}

	public function edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : '/cabinet');

		if (!$user = Db::connect('users')->where(['id' => (int) User::get_data()['id']])->get()) {
			Notify::createError(Langs::get('messages', 'User not found'));
			Router::redirect($redirect);
		}

		$_POST['user']['id'] = User::get_data()['id'];
		User::edit($_POST['user']);

		Router::redirect($redirect);
	}

	public function delete()
	{
		App::check_auth($redirect = CI);
		App::check_permission($GLOBALS['router']->getController());

		if (isset($_POST['delete'])) 
		{
			if (!$user = Db::connect('users')->select('id, img')->where('id', (int) $_POST['id'])->get()) 
			{
				$callback = [
					'type' => 'Danger',
					'message' => Langs::get('messages', 'Not found')
				];

				return;
			}

			if (!empty($user->img)) foreach (json_decode($user->img) as $img) Images::remove($img);

			foreach(Db::connect('companies')->select('id')->where('owner_id', $user->id)->getAll() as $company)
			{
				Db::connect('deals')->where('company_id', $company->id)->delete();
				Db::connect('finances')->where('company_id', $company->id)->delete();
				Db::connect('schedule')->where('company_id', $company->id)->delete();
				// Db::connect('tasks')->where('company_id', $company->id)->delete();
			}
			Db::connect('companies')->where('owner_id', $user->id)->delete();
			Db::connect('requests')->where('user_id', $user->id)->delete();
			Db::connect('reviews')->where('user_id', $user->id)->delete();
			Db::connect('users_verify')->where('user_id', $user->id)->delete();

			Db::connect('users')->where('id', $user->id)->delete();

			$callback = [
				'type' => 'Success',
				'message' => Langs::get('messages', 'Successful removal'),
			];
		}

		die(json_encode($callback));
	}

	public function activate()
	{
		if (isset($_GET['code']))
			if(User::verify($_GET['code']) == false)
				Notify::createError(Langs::get('messages', 'Not found'));
			else
				Notify::createSuccess(Langs::get('messages', 'User activate successfully'));

		Router::redirect('/auth?redirect=' . base64_encode('/cabinet'));
	}

	public function api_get()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if ($_POST['get'] == 'true') 
		{
			if ($_POST['id'] == null)
				echo Formatting::json(['message' => Langs::get('messages', 'Data not sending')], 400);

			echo Formatting::json(['type' => 'success', 'user' => Db::connect('users')->select('name, surname, patronymic, username, birthday, phone, email')->where(['id' => (int) $_POST['id']])->get()], 200);
		} else {
			echo Formatting::json(['message' => Langs::get('messages', 'Wrong')], 403);
		}

		die();
	}

	public function api_edit()
	{
		App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
		App::check_permission($GLOBALS['router']->getController());

		if ($_POST['edit'] == 'ajax') 
		{
			if ($_POST['data'] == null)
				$callback['message'] = Langs::get('messages', 'Data not sending');

			$callback['message'] = User::edit($_POST['data']);
		}

		die(json_encode($callback));
	}
}