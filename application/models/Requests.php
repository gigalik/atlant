<?php
class Requests
{
	protected $data = [];

	public function __construct(array $data = [])
	{
		foreach ($data as $key => $value)
			$this->data[$key] = $value;
	}
	protected function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	protected function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	public static function add($data)
	{
		if (!empty($data['id']))
			return Router::get_code(400);

		$id = Db::connect('requests')->insert($data);

		Log::set(User::get_data()['id'] ?: $data['user_id'], 'Added request', strtolower(__CLASS__), 'add', $id);

		return Router::get_code(201);
	}

	public static function edit($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		$item = Db::connect('requests')->select('id')->where(['id' => (int) $data['id']])->get();

		if ($item == null)
			return Router::get_code(400);

		Db::connect('requests')->where(['id' => (int) $item->id])->update($data);
		Log::set(User::get_data()['id'], 'Edit request', strtolower(__CLASS__), 'edit', $item->id);

		return Router::get_code(200);
	}

	public static function delete($data)
	{
		if ($data['id'] == null)
			return Router::get_code(400);

		$item = Db::connect('requests')->select('id')->where(['id' => (int) $data['id']])->get();
		if ($item == null)
			return Router::get_code(404);

		Db::connect('requests')->where(['id' => (int) $item->id])->delete();
		Log::set(User::get_data()['id'], 'Removed request', strtolower(__CLASS__), 'delete', $item->id);
		return Router::get_code(200);
	}

	public static function get_for_calendar($start = null, $end = null)
	{
		$query = Db::connect('requests');

		if ($start != null)
			$query = $query->where('create_date', '>=', $start);

		if ($end != null)
			$query = $query->where('create_date', '<=', $end);

		$events = [];
		foreach ($query->getAll() as $request) {
			$events[] = [
				'id' => $request->id,
				'title' => $request->name . '<br>' . $request->phone,
				'type' => 'requests',
				'date' => date('Y-m-d', strtotime($request->create_date)),
			];
		}

		return $events;
	}

	public static function clone($data)
	{
		if (empty($data['id']))
			return Router::get_code(400);

		if (!$old_item = Db::connect('requests')->where(['id' => (int) $data['id']])->get())
			return Router::get_code(404);

		unset($old_item->id);
		$item = $old_item;

		$id = Db::connect('requests')->insert((array) $item);
		Log::set(User::get_data()['id'], 'Clone request', strtolower(__CLASS__), 'clone', $id);

		return Router::get_code(201);
	}

	public static function getTotalPrice(object $request, $currency = 'RUR', $formater = 'ru_RU')
	{
		$total_price = 0;
		foreach (json_decode($request->services) as $index => $position)
			$total_price += (float) $position->total_price * ((100 - ((float) $position->discount ?: 0)) / 100);

		return Formatting::money($total_price, $formater, $currency);
	}
}
