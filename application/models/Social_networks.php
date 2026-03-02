<?php
class Social_networks
{
	private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	public static function add(array $data)
	{
		if (isset($data['id']))
			return Router::get_code(400);

		$insert_id = Db::connect('social_networks')->insert($data);
		Log::set(User::get_data()['id'], 'Added social network', strtolower(__CLASS__), strtolower(__FUNCTION__), $insert_id);

		$response = Router::get_code(201);
		$response['insert_id'] = $insert_id;
		return $response;
	}

	public static function edit(array $data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		$item = Db::connect('social_networks')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(400);

		Db::connect('social_networks')->where(['id' => (int) $item->id])->update($data);
		Log::set(User::get_data()['id'], 'Edit social network', strtolower(__CLASS__), strtolower(__FUNCTION__), $item->id);

		$response = Router::get_code(200);
		$response['insert_id'] = $item->id;
		return $response;
	}

	public static function delete($data)
	{
		if ($data['id'] == null)
			return Router::get_code(400);

		$item = Db::connect('social_networks')->select('id')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		Db::connect('social_networks')->where(['id' => (int) $item->id])->delete();
		Log::set(User::get_data()['id'], 'Removed social network', strtolower(__CLASS__), strtolower(__FUNCTION__), $item->id);
		return Router::get_code(200);
	}

	public static function clone($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('social_networks')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		$id = Db::connect('social_networks')->insert((array) $item);
		Log::set(User::get_data()['id'], 'Clone social network', strtolower(__CLASS__), strtolower(__FUNCTION__), $id);

		return Router::get_code(201);
	}
}
