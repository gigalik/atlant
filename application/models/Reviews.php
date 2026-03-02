<?php
class Reviews
{
	private $full_img_size = 1400;
	private $thumb_img_size = 450;
	private $img_type = 'png';
	private $data = [];

	public function __construct(array $data = [])
	{
		foreach ($data as $key => $value)
			$this->data[$key] = $value;
	}
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"

	public static function add($data)
	{
		if (!empty($data['id']))
			return Router::get_code(400);

		if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) {
			$filename = explode('.', $_FILES['image']['name']);

			$data['img'] = json_encode([
				'fullsize' => $fullsize = '/img/' . strtolower(__CLASS__) . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
				'thumb' => $thumb = '/img/' . strtolower(__CLASS__) . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
			]);

			Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 1400);
			Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 390);
		}

		$id = Db::connect('reviews')->insert($data);
		Log::set(User::get_data()['id'], 'Added review', strtolower(__CLASS__), 'add', $id);

		return Router::get_code(201);
	}

	public static function edit($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		$item = Db::connect('reviews')->select('id, img')->where(['id' => (int) $data['id']])->get();

		if ($item == null)
			return Router::get_code(400);

		if ((UPLOAD_ERR_OK === $_FILES['image']['error'])) {
			if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);

			$filename = explode('.', $_FILES['image']['name']);

			$data['img'] = json_encode([
				'fullsize' => $fullsize = '/img/' . strtolower(__CLASS__) . '/fullsize/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1],
				'thumb' => $thumb = '/img/' . strtolower(__CLASS__) . '/thumb/' . str_replace(' ', '_', date('ymdHis')) . '.' . $filename[1]
			]);

			Images::upload($_FILES['image']['tmp_name'], ROOT . $fullsize, $filename[1], null, 1400);
			Images::upload($_FILES['image']['tmp_name'], ROOT . $thumb, $filename[1], null, 450);
		}

		if (isset($_POST['deleteImage']) && $_POST['deleteImage'] == "on") {
			if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);
		}

		Db::connect('reviews')->where(['id' => (int) $item->id])->update($data);
		Log::set(User::get_data()['id'], 'Edit review', strtolower(__CLASS__), 'edit', $item->id);

		return Router::get_code(200);
	}

	public static function delete($data)
	{
		if ($data['id'] == null)
			return Router::get_code(400);

		$item = Db::connect('reviews')->select('id, img')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		if (!empty($item->img)) foreach (json_decode($item->img) as $img) Images::remove($img);

		Db::connect('reviews')->where(['id' => (int) $item->id])->delete();
		Log::set(User::get_data()['id'], 'Removed review', strtolower(__CLASS__), 'delete', $item->id);
		return Router::get_code(200);
	}

	public static function clone($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('reviews')->where(['id' => (int) $data['id']])->get())
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

			Images::upload(ROOT . $old_fullsize, ROOT . $fullsize, 'png', null, 1400);
			Images::upload(ROOT . $old_thumb, ROOT . $thumb, 'png', null, 450);
		}

		$id = Db::connect('reviews')->insert((array) $item);
		Log::set(User::get_data()['id'], 'Clone review', strtolower(__CLASS__), 'clone', $id);

		return Router::get_code(201);
	}
}
