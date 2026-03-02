<?php
Class AuthController
{
  public function index ()
	{
		$redirect = base64_decode($_GET['redirect']);

		Storage::cookie('unset', ['title' => 'auth']);

		if (isset($_POST['login'])) 
		{
			if (empty($_POST['user']['password'])) 
			{
				Notify::createError(Langs::get('messages', Langs::get('messages', 'Password not sending')));
				Router::redirect($redirect);
			}

			$user = User::check($_POST['user']);
			$user_params = Db::connect('users')->select('id, username, usergroup, status, pass_hash')->where(['id' => $user->id ?: $user['id']])->get();

			if (!password_verify(str_replace(' ', '', Utilites::check_str($_POST['user']['password'])), $user_params->pass_hash))
			{
				Notify::createError(Langs::get('messages', 'Password is\'n correct. Try again'));
				Router::redirect($_SERVER['HTTP_REFERER']);
			}

			$data = [
				'user_id' => $user_params->id,
			];

			Storage::cookie('set', ['title' => 'auth', 'val' => Text::encrypt(serialize($data), SALT)]);

			Log::set($user_params->id, 'User logged in by email number', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());

			$path = 'cabinet';

			Router::redirect($redirect ?: SITE_URL . DS . $path);
		}

		return [
			'form' => [
				'action' => '/auth',
				'btn' => 'login'
			],
			'seo' => [
				'title' => Langs::get('headers', 'Authorization on the site %SITE_NAME%'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	public function signup()
	{
		Storage::cookie('unset', ['title' => 'auth']);

		if (isset($_POST['registration'])) 
		{
			if (!Utilites::captcha($_POST['smart-token']))
				Notify::createError(Langs::get('messages', 'You shall not pass'));

			$original = base64_decode($_POST['original']);
			$user = User::check($_POST['user'], true);

			if ($user['username'] ?: $user->username)
			{
				Notify::createError(Langs::get('messages', 'Data already in use'));
				Router::redirect('/auth/restore');
			}

			Notify::createSuccess(Langs::get('messages', 'You have been sent your login information'));

			$redirect = !empty($original) ? '/links/generate?data=' . base64_encode(http_build_query(['original' => $original, 'user' => $user->id ?: $user['id']])) : '/auth/check';

			Router::redirect($redirect);
		}

		return [
			'form' => [
				'action' => '/auth/signup',
				'btn' => 'registration'
			],
			'seo' => [
				'title' => Langs::get('headers', 'Registration on the site %SITE_NAME%'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	public function restore()
	{
		Storage::cookie('unset', ['title' => 'auth']);

		$redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : '/cabinet';

		if (isset($_POST['restore'])) 
		{
			if(!$user = User::check($_POST['user']))
				Notify::createError(Langs::get('messages', 'Not found'));

			$current_user = Db::connect('users')->select('id, email, phone, username')->where(['id' => $user->id])->get();
			$new_password = User::generate_password(random_int(6, 12));

			Db::connect('users')->where('id', (int) $user->id)->update(['pass_hash' => password_hash(strip_tags($new_password), PASSWORD_DEFAULT)]);

			$data = [
				'email' => $current_user->email,
				'subject' => Langs::get('email_subjects', 'Restore password on the site'),
				'settings' => [
					'{{username}}' => base64_decode($current_user->username),
					'{{user_phone}}' => $current_user->phone,
					'{{user_email}}' => $current_user->email,
					'{{user_password}}' => $new_password,
				]
			];

			Utilites::transmitter($data, UTILITIES . '/email/restore_password.html');

			Log::set($user->id, 'User restore password', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());
			Notify::createSuccess(Langs::get('messages', 'You have been sent your login information'));

			Router::redirect($redirect);
		}

		return [
			'form' => [
				'action' => '/auth/restore_password',
				'btn' => 'restore'
			],
			'seo' => [
				'title' => Langs::get('headers', 'Restore password on the site %SITE_NAME%'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

	public function check()
	{
		return [
			'seo' => [
				'title' => Langs::get('headers', 'Check your email from %SITE_NAME%'),
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}

  public function signout()
	{
		if(empty($user_data = User::get_data()))
			Router::redirect(SITE_URL);

		Log::set($user_data['id'] ?: 0, 'User logout', $GLOBALS['router']->getController(), $GLOBALS['router']->getAction());

		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);

			foreach ($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);

				if (!in_array($name, ['policy', 'lang'])) Storage::cookie('unset', ['title' => $name]);
			}
		}

		session_destroy();
		Router::redirect('/auth/goodbye');
	}

	public function goodbye()
	{
		return [
			'seo' => [
				'title' => Langs::get('headers', 'Goodbye! %SITE_NAME% with love') . ' ❤️',
				'robots' => [
					'i' => 'noindex',
					'f' => 'nofollow'
				]
			]
		];
	}
}