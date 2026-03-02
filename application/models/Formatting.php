<?php
class Formatting extends App
{
	private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"
	public static function phone(string $phone = '+79999999999', $type = 'clear')
	{
		switch ($type) {
			case 'analog':
				if (preg_match('/^\+\d(\d{4})(\d{2})(\d{2})(\d{2})$/', $phone, $matches)) {
					$code = substr($phone, 0, 2);
					return $code . ' (' . $matches[1] . ') ' . $matches[2] . '-' . $matches[3] . '-' . $matches[4];
				}
				break;

			case 'omni':
				if (preg_match('/^\+\d(\d{3})(\d{3})(\d{4})$/', $phone, $matches))
					return '8 (' . $matches[1] . ') ' . $matches[2] . ' ' . $matches[3];
				break;

			case 'mobile':
				if (preg_match('/^\+\d(\d{3})(\d{3})(\d{4})$/', $phone, $matches)) {
					$code = substr($phone, 0, 2);
					return $code . ' (' . $matches[1] . ') ' . $matches[2] . ' ' . $matches[3];
				}
				break;

			case 'clear':
				return str_replace([' ', '(', ')', '-'], '', $phone);
		}
	}

	public static function date(string $date, string $format = 'd.m.Y H:i:s')
	{
		return date_format(date_create($date), $format);
	}

	public static function money(float $amount = 0, int $digits = 0, string $formatter = 'ru_RU', string $currency = 'RUR')
	{
		$fmt = new NumberFormatter($formatter, NumberFormatter::CURRENCY);
		$fmt->setTextAttribute(NumberFormatter::CURRENCY_CODE, $currency);
		$fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, $digits);
		return str_replace('р.', '₽', $fmt->formatCurrency($amount, $currency));
	}

	public static function remove_attributes($text, $allowed = [])
	{
    $attributes = implode('|', $allowed);
    $reg = '/(<[\w]+)([^>]*)(>)/i';

    $text = preg_replace_callback(
			$reg,
			function ($matches) use ($attributes) {
				// Если нет разрешенных атрибутов, возвращаем пустой тег
				if (!$attributes) {
					return $matches[1] . $matches[3];
				}

				$attr = $matches[2];
				$reg = '/(' . $attributes . ')="[^"]*"/i';
				preg_match_all($reg, $attr, $result);
				$attr = implode(' ', $result[0]);
				$attr = ($attr ? ' ' : '') . $attr;

				return $matches[1] . $attr . $matches[3];
			},
			$text
    );

    return $text;
	}

	public static function getTime($minutes)
	{
		$hours = floor($minutes / 60); // Получаем количество полных часов
		$min = $minutes - ($hours * 60); // Получаем оставшиеся минуты

		return ['h' => $hours, 'm' => $min];
	}

	public static function json(array $data, int $code = 200, $flags = null)
	{
		$status = Router::get_code($code, false);

		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");

		return json_encode(array_merge($status, ['data' => $data]), $flags);
	}

	public static function array_to_table($data = [], $show_headers = 0, $classes = "table")
	{
		$rows = [];
		if($show_headers == 2) {
			$headers = [];
			foreach (array_keys((array) $data[0]) as $cell) 
				$headers[] = "<th>{$cell}</th>";
			$rows[] = "<thead><tr class='sticky-top'>" . implode('', $headers) . "</tr></thead>";
		} elseif($show_headers == 1) {
			$headers = [];
			foreach ($data[0] as $cell) 
				$headers[] = "<th>{$cell}</th>";
			$rows[] = "<thead><tr class='sticky-top'>" . implode('', $headers) . "</tr></thead>";
			unset($data[0]);
		}
		foreach ($data as $row) 
		{
			$cells = [];
			foreach ($row as $key => $cell) 
				$cells[] = "<td class=\"{$key}\">{$cell}</td>";
			$rows[] = "<tr>" . implode('', $cells) . "</tr>";
		}
		return '<table class="'. $classes .'">' . implode('', $rows) . '</table>';
	}
}
