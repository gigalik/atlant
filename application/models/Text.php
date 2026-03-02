<?php
class Text extends App
{
	private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	//Translit
	public static function translit(string $value, string $type = null)
	{
		$converter = Storage::json('get', dirname(dirname(__FILE__)) . '/utilities/Text', 'translit');

		switch ($type) {
			case 'sef':
				$value = mb_strtolower($value);
				$value = strtr($value, $converter);
				$value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
				$value = mb_ereg_replace('[-]+', '-', $value);
				$value = trim($value, '-');
				break;

			case 'filename':
				$value = mb_strtolower($value);
				$value = strtr($value, $converter);
				$value = mb_ereg_replace('[^-0-9a-z\.]', '_', $value);
				$value = mb_ereg_replace('[-]+', '_', $value);
				$value = trim($value, '_');
				break;

			default:
				$value = strtr($value, $converter);
				break;
		}

		return $value;
	}

	// Crypting
	static function encrypt($data, $key, $cipher = "aes-256-cbc") {
    $encryption_key = base64_decode($key);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $encrypted = openssl_encrypt($data, $cipher, $encryption_key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
  }
  
  static function decrypt($data, $key, $cipher = "aes-256-cbc") {
    $encryption_key = base64_decode($key);
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, $cipher, $encryption_key, 0, $iv);
  }

	public static function word_break($string, $max_length = 55)
	{
		$regular = '~\S{' . $max_length . '}\S~si';
		$replace = "$1<wbr />";

		return preg_replace($regular, $replace, $string);
	}

	/**
	 * Склонение существительных после числительных.
	 * @use Text::num_word_lib(32, ['lang' => 'en', 'lib' => 'square', 'word' => 'hectare'], true);
	 * 
	 * @param array $params [lang, lib, word]
	 */
	public static function num_word_lib(float $value, array $params, bool $show = true)
	{
		$words = Storage::json('get', '/application/utilities/Text', 'num_word_' . ($params['lang'] ?: DEFAULT_LANG));

		return self::num_word($value, $words, $show);
	}

	public static function num_word(float $value, array $words, bool $show = true)
	{
		$num = $value % 100;

		if ($num > 19)
			$num = $num % 10;

		$out = ($show) ?  $value . ' ' : '';

		switch ($num) {
			case 1:
				$out .= $words[0];
				break;
			case 2:
			case 3:
			case 4:
				$out .= $words[1];
				break;
			default:
				$out .= $words[2];
				break;
		}

		return $out;
	}

	// Чистка данных
	public static function remove_editor_trash(string $text)
	{
		$text = preg_replace("/(style|class)=\".*?\"/", '', $text);
		return $text;
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
}
