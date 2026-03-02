<?php
class Slider
{
	private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	public static function add(array $data)
	{
		if (isset($data['id']))
			return Router::get_code(400);

		if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) {
			$filename = explode('.', $_FILES['image']['name']);

			$data['img'] = json_encode([
				'fullsize' => $fullsize = '/img/' . strtolower(__CLASS__) . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
				'thumb' => $thumb = '/img/' . strtolower(__CLASS__) . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
			]);

			Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 1398);
			Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 256);
		}

		$insert_id = Db::connect('slider')->insert($data);
		Log::set(User::get_data()['id'], 'Added slider', 'slider', strtolower(__FUNCTION__), $insert_id);

		$response = Router::get_code(201);
		$response['insert_id'] = $insert_id;
		return $response;
	}

	public static function edit(array $data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$item = Db::connect('slider')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(400);

		if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) {
			if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);

			$filename = explode('.', $_FILES['image']['name']);

			$data['img'] = json_encode([
				'fullsize' => $fullsize = '/img/' . strtolower(__CLASS__) . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
				'thumb' => $thumb = '/img/' . strtolower(__CLASS__) . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
			]);

			Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 1398);
			Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 256);
		}

		if (isset($_POST['deleteImage']) && $_POST['deleteImage'] == "on") {
			if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);
		}


		Db::connect('slider')->where(['id' => (int) $item->id])->update($data);
		Log::set(User::get_data()['id'], 'Edit slider', 'slider', strtolower(__FUNCTION__), $item->id);

		$response = Router::get_code(200);
		$response['insert_id'] = $item->id;
		return $response;
	}

	public static function delete($data)
	{
		if ($data['id'] == null)
			return Router::get_code(400);

		$item = Db::connect('slider')->select('id, img')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);

		Db::connect('slider')->where(['id' => (int) $item->id])->delete();
		Log::set(User::get_data()['id'], 'Removed slide', strtolower(__CLASS__), strtolower(__FUNCTION__), $item->id);
		return Router::get_code(200);
	}

	public static function clone($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('slider')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		if (!empty($old_item->img)) {
			$old_fullsize = json_decode($old_item->img, true)['fullsize'];
			$old_thumb = json_decode($old_item->img, true)['thumb'];
			$filename = str_replace(' ', '_', uniqid(rand()));

			$item->img = json_encode([
				'fullsize' => $fullsize = '/img/' . strtolower(__CLASS__) . '/fullsize/' . $filename . '.jpg',
				'thumb' => $thumb = '/img/' . strtolower(__CLASS__) . '/thumb/' . $filename . '.jpg'
			]);

			Images::upload(ROOT . $old_fullsize, ROOT . $fullsize, 'jpg', null, 1024);
			Images::upload(ROOT . $old_thumb, ROOT . $thumb, 'jpg', null, 512);
		}

		$id = Db::connect('slider')->insert((array) $item);
		Log::set(User::get_data()['id'], 'Clone slide', strtolower(__CLASS__), strtolower(__FUNCTION__), $id);

		return Router::get_code(201);
	}
}
