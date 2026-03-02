<?php
class Services 
{
	private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

  public static function add ($data)
  {
		if (!empty($data['id'])) 
			return Router::get_code(400);

    $id = Db::connect('services')->insert($data);
		Log::set(User::get_data()['id'], 'Added service', strtolower(__CLASS__), strtolower(__FUNCTION__), $id);

		return Router::get_code(201);
  }

	public static function edit ($data)
	{
		if (empty($data['id'])) 
			return Router::get_code(400);

		if (!$item = Db::connect('services')->select('id')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(400);

		Db::connect('services')->where(['id' => (int) $item->id])->update($data);
		Log::set(User::get_data()['id'], 'Edit service', strtolower(__CLASS__), strtolower(__FUNCTION__), $item->id);

		return Router::get_code(200);
	}

	public static function delete ($data)
	{
		if ($data['id'] == null)
      return Router::get_code(400);

		if (!$item = Db::connect('services')->select('id')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		Db::connect('services')->where(['id' => (int) $item->id])->delete();
		Log::set(User::get_data()['id'], 'Removed service', strtolower(__CLASS__), strtolower(__FUNCTION__), $item->id);
		return Router::get_code(200);
	}

	public static function clone ($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('services')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		$id = Db::connect('services')->insert((array) $item);
		Log::set(User::get_data()['id'], 'Clone service', strtolower(__CLASS__), strtolower(__FUNCTION__), $id);

		return Router::get_code(201);
	}
}