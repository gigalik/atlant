<?php
class User
{
	const ACTIVE = 1;
	const INACTIVE = 0;

	private $full_img_size = 250;
	private $thumb_img_size = 90;
	private $img_type = 'png';
	private $data = [];

	private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	public static function check($data, $create = false)
	{
		$where = [];
		$data['phone'] ? $where['phone'] = Formatting::phone($data['phone']) : '';
		$data['email'] ? $where['email'] =  strip_tags(trim($data['email'])) : '';

		if(!$user = Db::connect('users')->select('id')->where($where)->get())
		{
			if ($create == true)
				return self::add($data, 1);
			else 
				Notify::createError(Langs::get('messages', 'Wrong data or user does not exist'));
				return Router::redirect('/auth/signup');
		}

		return $user;
	}

	public static function add($data, $send_message = 0)
	{
		if (!empty($data['id']))
			return Router::set_code(400, true);

		if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) {
			$filename = explode('.', $_FILES['image']['name']);

			$data['img'] = json_encode([
				'fullsize' => $fullsize = '/img/' . strtolower(__CLASS__) . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
				'thumb' => $thumb = '/img/' . strtolower(__CLASS__) . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
			]);

			Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 150);
			Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 50);
		}

		$new_user = [
			'name'              => strip_tags(trim($data['name'])) ?: strip_tags(trim(explode('@', $data['email'])[0])),
			'surname'           => $data['surname'] ? strip_tags(trim($data['surname'])) : null,
			'patronymic'        => $data['patronymic'] ? strip_tags(trim($data['patronymic'])) : null,
			'birthday'          => strip_tags($data['birthday']),
			'sex'               => (int) $data['sex'] ?: 0,
			'phone'             => $data['phone'] ? Formatting::phone($data['phone']) : '',
			'email'             => $email = strip_tags(trim($data['email'])),
			'username'          => base64_encode(strtolower($username = $data['username'] ?: 'user_' . date('ymdhis'))),
			'pass_hash'         => password_hash(strip_tags($password = $data['password'] ?: User::generate_password(12)), PASSWORD_DEFAULT),
			'usergroup'         => (int) $data['usergroup'] ?: 4,
			'status'            => (int) $data['status'] ?: 0,
			'img'               => $data['img'] ?: null,
		];

		if($send_message == 1)
		{
			if (Storage::json('check', CONF, 'unisender') == true)
			{
				$data = [
					'email' => $email,
					'subject' => Langs::get('email_subjects', 'Registration on the site'),
					'settings' => [
						'{{username}}' => $username,
						'{{user_email}}' => $email,
						'{{user_password}}' => $password,
						'{{verify_code}}' => $verify_code = uniqid('verify')
					]
				];

				Utilites::transmitter($data, UTILITIES . '/email/new_user.html');
			}
		}

		$id = Db::connect('users')->insert($new_user);

		Db::connect('users_verify')->insert(['user_id' => $id, 'code' => $verify_code]);

		Log::set(User::get_data()['id'] ?: $id, 'Added user', strtolower(__CLASS__), strtolower(__FUNCTION__), $id);

		return ['id' => $id];
	}

	public static function edit($data)
	{
		if (empty($data['id']))
			self::add($data, 1);

		if (!$current_user = Db::connect('users')->where(['id' => (int) $data['id']])->get())
			return false;

		if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) 
		{
			if (!empty($current_user->img)) foreach (json_decode($current_user->img) as $img) Images::remove($img);

			$filename = explode('.', $_FILES['image']['name']);

			$data['img'] = json_encode([
				'fullsize' => $fullsize = '/img/' . strtolower(__CLASS__) . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
				'thumb' => $thumb = '/img/' . strtolower(__CLASS__) . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
			]);

			Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 150);
			Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 50);
		}

		if (isset($_POST['deleteImage']) && $_POST['deleteImage'] == "on")
			if (!empty($current_user->img)) foreach (json_decode($current_user->img) as $img) Images::remove($img);

		if(!empty($data['phone'])) 
			$data['phone'] = Formatting::phone($data['phone']);

		if(!empty($data['username'])) 
			$data['username'] = base64_encode($data['username']);

		if(!empty($data['password'])) 
			$data['pass_hash'] = password_hash(strip_tags($data['password']), PASSWORD_DEFAULT);

		unset($data['password']);

		Db::connect('users')->where(['id' => (int) $current_user->id])->update($data);
		Log::set(User::get_data()['id'], 'Edit user', strtolower(__CLASS__), strtolower(__FUNCTION__), $current_user->id);

		return ['id' => $current_user->id];
	}

	public static function delete($data)
	{
		if ($data['id'] == null)
			return Router::get_code(400);

		if (!$item = Db::connect('users')->select('id, img')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);

		Db::connect('users')->where(['id' => (int) $item->id])->delete();
		Log::set(User::get_data()['id'], 'Removed user', strtolower(__CLASS__), strtolower(__FUNCTION__), $item->id);
		return Router::get_code(200);
	}

	public static function clone($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('users')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		if (!empty($old_item->img)) {
			$old_fullsize = json_decode($old_item->img, true)['fullsize'];
			$old_thumb = json_decode($old_item->img, true)['thumb'];
			$filename = str_replace(' ', '_', uniqid(rand()));

			$item->img = json_encode([
				'fullsize' => $fullsize = '/img/' . strtolower(__CLASS__) . '/fullsize/' . $filename . '.png',
				'thumb' => $thumb = '/img/' . strtolower(__CLASS__) . '/thumb/' . $filename . '.png'
			]);

			Images::upload(ROOT . $old_fullsize, ROOT . $fullsize, 'png', null, 250);
			Images::upload(ROOT . $old_thumb, ROOT . $thumb, 'png', null, 90);
		}

		$item->username = 'user_' . time();

		$id = Db::connect('users')->insert((array) $item);
		Log::set(User::get_data()['id'], 'Clone user', strtolower(__CLASS__), strtolower(__FUNCTION__), $id);

		return Router::get_code(201);
	}

	// PASSWORD
	public static function generate_password($length = 12)
	{
		$chars = '1234567890AaBbCcDdEeFfGgHhIiJjKklMmNnOoPpQRrSsTtUuVvWwXxYyZz~!@$%_-+=&?';
		$numChars = strlen($chars);
		$string = '';

		for ($i = 0; $i < $length; $i++) $string .= substr($chars, rand(1, $numChars) - 1, 1);

		return $string;
	}

	public static function verify ($code)
	{
		if(!$verify_data = Db::connect('users_verify')->where(['code' => Utilites::check_str($code)])->get())
			return false;

		Db::connect('users')->where(['id' => $verify_data->user_id])->update(['status' => 1]);
		Db::connect('users_verify')->where(['id' => $verify_data->id])->delete();

		return true;
	}

	public static function get_data ($user_id = null, $fields = 'id, name, surname, patronymic, username, phone, email, sex, birthday, usergroup, img, status')
	{
		if(!$user_id) {
			$auth = unserialize(Text::decrypt($_COOKIE['auth'], SALT));
			$user_id = $auth['user_id'];
		}

		if(!$user = Db::connect('users')->select($fields)->where('id', (int) $user_id)->get())
			return Router::get_code(404);

		return (array) $user;
	}
}
