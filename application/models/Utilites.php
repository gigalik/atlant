<?php
require_once ROOT . '/vendor/autoload.php';
use Unisender\ApiWrapper\UnisenderApi;

class Utilites extends App
{
	private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

  // EMAIL TEMPLATOR
  public static function transmitter (array $data, $template, $type = 'email')
  {
    $storage = Storage::get_default();
		$unisender = Storage::json('get', CONF, 'unisender');
		
    $data['settings']['{{background}}'] = $data['settings']['{{background}}'] ?: "#ffffff";
    $data['settings']['{{padding}}'] = $data['settings']['{{padding}}'] ?: "1rem";
    $data['settings']['{{text_color}}'] = $data['settings']['{{text_color}}'] ?: "#000000";
    $data['settings']['{{link_color}}'] = $data['settings']['{{link_color}}'] ?: "#0070B2";
    $data['settings']['{{site_name}}'] = $storage['information']->title;
    $data['settings']['{{site_url}}'] = SITE_URL;

    $body = '<body style="background-color: {{background}}; color: {{text_color}}; padding: {{padding}}">';
    $body .= file_get_contents($template);
    $body .= '</body>';

    $UnisenderApi = new UnisenderApi($unisender['api_key'], 'UTF-8', 4, null, false, $storage['information']->title);

		$message = [
			'email' => $data['email'],
			'sender_name' => $storage['information']->title, 
			'sender_email' => 'info@devstarter.ru', // $storage['information']->email, 
			'subject' => $data['subject'],
			'body' => str_ireplace((array) array_keys($data['settings']), (array) $data['settings'], $body), 
			'list_id' => '1024',
			'format' => 'json'
		];

		return $UnisenderApi->sendEmail($message);
  }

  // GEO SERVICES
  /**
	 * Преобразует текстовую строку адреса в геокоординаты
   * 
	 * @param string $address указанный адрес для расшифровки
	 * 
   * @return bool|array результат
	 */
	public static function geocoder($address)
	{
		if (empty($address))
			return false;

		$ch = curl_init('https://geocode-maps.yandex.ru/1.x/?apikey=19e8dd1d-99b5-43be-ae3b-df3505462427&format=json&geocode=' . urlencode(strip_tags(trim($address))));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($res, true);

    $coordinates = $res['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
    $coordinates = explode(' ', $coordinates);

		return [
			'lon' => $coordinates[0],
			'lat' => $coordinates[1]
		];
	}

  /**
	 * Получение погоды по геокоординатам
   * 
	 * @param string $api_key API ключ от сервиса Openweathermap (dev: c1ae2a767873a6797537bbc632b5871f)
   * @param string $lon долгота
   * @param string $lat широта
   * @param string $lang язык
   * @param string $units система измерения
	 * 
   * @return string
	 */
  public static function weather($api_key, $lon, $lat, $lang = 'ru', $units = 'metric')
  {
    return file_get_contents("https://api.openweathermap.org/data/2.5/weather?" . http_build_query([
      "lon" => $lon,
      "lat" => $lat,
      "units" => $units,
      "lang" => $lang,
      "appid" => $api_key,
    ]));
  }

	public static  function get_ip($ip)
	{
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}

		if (preg_match('/([0-9]|[0-9][0-9]|[01][0-9][0-9]|2[0-4][0-9]|25[0-5])(\.' . '([0-9]|[0-9][0-9]|[01][0-9][0-9]|2[0-4][0-9]|25[0-5])){3}/', $ip, $match)) {
			return $match[0];
		}

		return $ip;
	}

  // FILES
  public static function upload_file(array $file)
	{
		if (!file_exists(ROOT . DS . 'files' . DS . 'upload'))
			mkdir(ROOT . DS . 'files' . DS . 'upload', 0777, true);

		$file_path =  DS . 'files' . DS . 'upload' . DS . date('YmdHis') . '_' . $file['name'];
		move_uploaded_file($file['tmp_name'], ROOT . $file_path);

		return str_replace(DS, '/', $file_path);
	}

  /**
	 * Проверяет код ошибки загружаемого файла
	 * Дает больше информации, чем обычная проверка на UPLOAD_ERR_OK, так как генерирует ошибки
   * 
	 * @param ?array $file загружаемый файл
	 * 
   * @return bool результат проверки
	 */
  public static function check_upload_err(?array $file = null)
	{
		if ($file == null)
			return false;

		$errors = [
			0 => 'There is no error, the file uploaded with success',
			1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
			2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
			3 => 'The uploaded file was only partially uploaded',
			4 => 'No file was uploaded',
			6 => 'Missing a temporary folder',
			7 => 'Failed to write file to disk.',
			8 => 'A PHP extension stopped the file upload.',
		];

		if ($file['error'] === UPLOAD_ERR_OK)
			return true;

		if ($file['error'] === UPLOAD_ERR_NO_FILE)
			return false;

		Notify::createError($errors[$file['error']] != null ? $errors[$file['error']] : 'Unknown upload error');
		return false;
	}

  //DEV TOOLS
  /**
	 * Проверяет IP на то, принадлежит ли он сотрудникам компании
	 * Используется для отображения ошибок или вывода страницы 404 на главной
   * 
	 * @param string $ip IP-адрес
   * 
	 * @return bool результат проверки
	 */
  public static function is_dev(string $ip = null)
	{
		if ($ip == null)
			$ip = $_SERVER["REMOTE_ADDR"];

		return in_array($ip, ['127.0.0.1', '46.149.229.88', '151.237.175.207']);
	}

	public static function date_diff($date1, $date2)
	{
		$time = new DateTime($date1);

		$since_time = $time->diff(new DateTime($date2));

		$diff = [
			'd' => $since_time->days,
			'h' => $since_time->days * 24 + $since_time->h,
			'm' => ($since_time->days * 24 * 60) + ($since_time->h * 60) + $since_time->i,
		];
		
		return $diff;
	}

  /**
	 * Позволяет вставить элемент в середину массива
	 * 
	 * @param array $array массив
	 * @param int $index индекс, после которого нужно вставить значение
	 * @param mixed $value значение для вставки
	 * 
	 * @return array новый массив
	 */
	public static function array_insert(array $array, int $index, $value)
	{
		return array_merge(array_slice($array, 0, $index), [$value], array_slice($array, $index));
	}

	/**
	 * Преобразует массив с ключами в новую пару ключ значение
	 * 
	 * @param array $array массив
	 * @param string $column колонка выступающая в качестве ключа
	 * 
	 * @return array новый массив
	 */
	public static function combine_by_column(array $array, string $column)
	{
		return array_combine(array_column($array, $column), $array);
	}

	public static function check_url($url) 
	{
		$url = trim(strip_tags($url));

		if(!isset($url))
			return false;

		try {
			if(get_headers($url, 1))
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public static function check_str($str, $type = 'string')
	{
		$clean = strip_tags($str);
		$clean = trim($clean);

		switch ($type) {
			case 'int':
				return (int) $clean;
			case 'double':
				return (double) $clean;
			case 'float':
				return (float) $clean;
			case 'bool':
				return (bool) $clean;
			default:
				return (string) $clean;
		}
	}

	// Barcode
	public static function code39($text) {
    if (!preg_match('/^[A-Z0-9-. $+\/%]+$/i', $text)) {
      throw new Exception('Ошибка ввода');
    }

    $text = '*'.strtoupper($text).'*';
    $length = strlen($text);
    $chars = str_split($text);
    $colors = '';

		$code39 = array(
			'0' => 'bwbwwwbbbwbbbwbw', '1' => 'bbbwbwwwbwbwbbbw',
			'2' => 'bwbbbwwwbwbwbbbw', '3' => 'bbbwbbbwwwbwbwbw',
			'4' => 'bwbwwwbbbwbwbbbw', '5' => 'bbbwbwwwbbbwbwbw',
			'6' => 'bwbbbwwwbbbwbwbw', '7' => 'bwbwwwbwbbbwbbbw',
			'8' => 'bbbwbwwwbwbbbwbw', '9' => 'bwbbbwwwbwbbbwbw',
			'A' => 'bbbwbwbwwwbwbbbw', 'B' => 'bwbbbwbwwwbwbbbw',
			'C' => 'bbbwbbbwbwwwbwbw', 'D' => 'bwbwbbbwwwbwbbbw',
			'E' => 'bbbwbwbbbwwwbwbw', 'F' => 'bwbbbwbbbwwwbwbw',
			'G' => 'bwbwbwwwbbbwbbbw', 'H' => 'bbbwbwbwwwbbbwbw',
			'I' => 'bwbbbwbwwwbbbwbw', 'J' => 'bwbwbbbwwwbbbwbw',
			'K' => 'bbbwbwbwbwwwbbbw', 'L' => 'bwbbbwbwbwwwbbbw',
			'M' => 'bbbwbbbwbwbwwwbw', 'N' => 'bwbwbbbwbwwwbbbw',
			'O' => 'bbbwbwbbbwbwwwbw', 'P' => 'bwbbbwbbbwbwwwbw',
			'Q' => 'bwbwbwbbbwwwbbbw', 'R' => 'bbbwbwbwbbbwwwbw',
			'S' => 'bwbbbwbwbbbwwwbw', 'T' => 'bwbwbbbwbbbwwwbw',
			'U' => 'bbbwwwbwbwbwbbbw', 'V' => 'bwwwbbbwbwbwbbbw',
			'W' => 'bbbwwwbbbwbwbwbw', 'X' => 'bwwwbwbbbwbwbbbw',
			'Y' => 'bbbwwwbwbbbwbwbw', 'Z' => 'bwwwbbbwbbbwbwbw',
			'-' => 'bwwwbwbwbbbwbbbw', '.' => 'bbbwwwbwbwbbbwbw',
			' ' => 'bwwwbbbwbwbbbwbw', '*' => 'bwwwbwbbbwbbbwbw',
			'$' => 'bwwwbwwwbwwwbwbw', '/' => 'bwwwbwwwbwbwwwbw',
			'+' => 'bwwwbwbwwwbwwwbw', '%' => 'bwbwwwbwwwbwwwbw'
		);

    foreach ($chars as $char) {
      $colors .= $code39[$char];
    }

    $html = '
          <div style="float:left">
            <div>';

    foreach (str_split($colors) as $i => $color) {
      if ($color=='b') {
        $html.='<span style="border-left: 1.92px solid; display: inline-block; height: 30px"></span>';
      } else {
        $html.='<span style="border-left: white 1.92px solid; display: inline-block; height: 30px"></span>';
      }
    }

    $html.='</div>
            <div style="float:left; width:100%;" align=center >'.$text.'</div></div>';
      //  echo htmlspecialchars($html);
    echo $html;
  }

	public static function captcha($token) 
	{
		$smartcaptcha = Storage::json('get', CONF, 'smartcaptcha');

    $ch = curl_init();
    $args = http_build_query([
        "secret" => $smartcaptcha['server'],
        "token" => $token,
        "ip" => $_SERVER['REMOTE_ADDR'],
    ]);
    curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
			echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
			return true;
    }
    $resp = json_decode($server_output);
    return $resp->status === "ok";
	}
}
