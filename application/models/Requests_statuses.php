<?php
class Requests_statuses
{
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	public static function add(array $data)
	{
		if (isset($data['id']))
			return Router::get_code(400);

		$insert_id = Db::connect('requests_statuses')->insert($data);
		Log::set(User::get_data()['id'], 'Added request_status', 'requests_statuses', 'add', $insert_id);

		$response = Router::get_code(201);
		$response['insert_id'] = $insert_id;
		return $response;
	}

	public static function edit(array $data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		$item = Db::connect('requests_statuses')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(400);

		Db::connect('requests_statuses')->where(['id' => (int) $item->id])->update($data);
		Log::set(User::get_data()['id'], 'Edit request_status', 'requests_statuses', 'edit', $item->id);

		$response = Router::get_code(200);
		$response['insert_id'] = $item->id;
		return $response;
	}

	public static function delete($data)
	{
		if ($data['id'] == null)
			return Router::get_code(400);

		$item = Db::connect('requests_statuses')->select('id')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		Db::connect('requests_statuses')->where(['id' => (int) $item->id])->delete();
		Log::set(User::get_data()['id'], 'Removed request_status', 'requests_statuses', 'delete', $item->id);
		return Router::get_code(200);
	}

	public static function clone($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('requests_statuses')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		$id = Db::connect('requests_statuses')->insert((array) $item);
		Log::set(User::get_data()['id'], 'Clone request status', strtolower(__CLASS__), 'clone', $id);

		return Router::get_code(201);
	}
}
