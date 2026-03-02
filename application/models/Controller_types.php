<?php
class Controller_types
{
	private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	public static function add($data)
	{
		if (!empty($data['id']))
			return Router::get_code(400);

		$id = Db::connect('controller_types')->insert($data);
		Log::set(User::get_data()['id'], 'Added controller type', strtolower(__CLASS__), strtolower(__FUNCTION__), $id);

		return Router::get_code(201);
	}

	public static function edit($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		$item = Db::connect('controller_types')->select('id')->where(['id' => (int) $data['id']])->get();

		if ($item == null)
			return Router::get_code(400);

		Db::connect('controller_types')->where(['id' => (int) $item->id])->update($data);
		Log::set(User::get_data()['id'], 'Edit controller type', strtolower(__CLASS__), strtolower(__FUNCTION__), $item->id);
		return Router::get_code(200);
	}

	public static function delete($data)
	{
		if ($data['id'] == null)
			return Router::get_code(400);

		$item = Db::connect('controller_types')->select('id')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		Db::connect('controller_types')->where(['id' => (int) $item->id])->delete();
		Log::set(User::get_data()['id'], 'Removed controller type', strtolower(__CLASS__), strtolower(__FUNCTION__), $item->id);
		return Router::get_code(200);
	}

	public static function clone($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('controller_types')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		$id = Db::connect('controller_types')->insert((array) $item);
		Log::set(User::get_data()['id'], 'Clone controller type', strtolower(__CLASS__), strtolower(__FUNCTION__), $id);

		return Router::get_code(201);
	}
}
