<?php
class Images
{
	private function __construct() { /* ... @return ClassName */ }  // We protect from creation through "new ClassName"
	private function __clone() { /* ... @return ClassName */ }  // We protect from creation through "cloning"
	private function __wakeup() { /* ... @return ClassName */ }  // We protect from creation through "unserialize"
	public static function upload($input, $output, $type = 'jpeg', $new_height = null, $new_width = null)
	{
		$get_directory = explode('/', str_replace(ROOT . '/', '', $output));
		array_pop($get_directory);
		$directory = ROOT . DS . implode(DS, $get_directory);

		if (!file_exists($directory)) mkdir($directory, 0755, true);

		if (in_array($type, ['svg', 'svg+xml'])) {
			if (move_uploaded_file($input, $output)) {
				Notify::createSuccess('The file is correct and was uploaded successfully');
				return true;
			} else {
				Notify::createError('Possible file upload attack!');
				return false;
			}
		} else {
			$im = new Imagick($input);
			// $im->setImageResolution(1250, 1250);

			$imageprops = $im->getImageGeometry();
			$original_width = $imageprops['width'];
			$original_height = $imageprops['height'];

			if (in_array($type, ['jpeg', 'jpg'])) {
				$im->setCompressionQuality(70);
				$im->setImageColorspace(Imagick::COLORSPACE_SRGB);
			}

			if (in_array($type, ['webp', 'png', 'ico'])) {
				// $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
				// $im->setBackgroundColor(new ImagickPixel('transparent'));
			}

			if ($new_height) {
				if ($original_height > $new_height) {
					$newWidth = ($new_height / $original_height) * $original_width;
					$im->resizeImage($newWidth, $new_height, Imagick::FILTER_LANCZOS, 0.9, true);
				}
			}

			if ($new_width) {
				if ($original_width > $new_width) {
					$newHeight = $new_width * ($original_height / $original_width);
					$im->resizeImage($new_width, $newHeight, Imagick::FILTER_LANCZOS, 0.9, true);
				}
			}

			$im->setImageFormat($type);
			$im->writeImage($output);
			$im->clear();
			$im->destroy();
		}
	}

	public static function upload_image(string $title, string $controller, string $type, string $file_name, array $options)
	{
		if (!file_exists(ROOT . DS . 'img' . DS . $controller . DS . $type))
			mkdir(ROOT . DS . 'img' . DS . $controller . DS . $type, 0777, true);

		$file_path =  DS . 'img' . DS . $controller . DS . $type . DS . $title . '.' . $options['extention'];
		Images::upload($file_name, ROOT . $file_path, $options['extention'], $options['new_height'], $options['new_width']);

		return str_replace(DS, '/', $file_path);
	}

	public static function get(object $item, string $type = 'fullsize', bool $set_default = true)
	{
		$file_path = $item->img != null ? json_decode($item->img, true)[$type] : null;

		if ($file_path != null && file_exists(ROOT . $file_path))
			return $file_path;

		if ($set_default)
			return '/img/no-img.png';

		return null;
	}

	public static function remove($image_path)
	{
		foreach (glob(ROOT . explode('.', $image_path)[0] . ".*") as $filename) {
			if (!unlink($filename)) trigger_error("Удаление изображения: Невозможно удалить изображение", E_USER_ERROR);
		}
	}

	public static function save_image_from_url($input, $output, $type, $h = null)
	{
		$get_directory = explode('/', str_replace(ROOT . '/', '', $output));
		array_pop($get_directory);
		$directory = ROOT . DS . implode(DS, $get_directory);

		if (!file_exists($directory)) mkdir($directory, 0755, true);

		$im = new Imagick($input);
		// $im->setImageResolution(1250,1250);
		$im->setImageColorspace(255);
		$im->setCompression(Imagick::COMPRESSION_JPEG);
		$im->setCompressionQuality(50);
		$im->setImageFormat('jpeg');

		if ($h) {
			$imageprops = $im->getImageGeometry();
			$width = $imageprops['width'];
			$height = $imageprops['height'];

			if ($height > $h) {
				// if($width > $height) {
				// 	$newHeight = $h;
				// 	$newWidth = ($h / $height) * $width;
				// } else {
				// 	$newWidth = $h;
				// 	$newHeight = ($h / $width) * $height;
				// }

				$newHeight = $h;
				$newWidth = ($h / $height) * $width;

				$im->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 0.9, true);
			}
		}

		$im->writeImage($output);
		$im->clear();
		$im->destroy();
	}

	public static function get_image_style(object $item)
	{
		if (!$item->img_prop)
			return false;

		$parts = explode(' ', $item->img_prop);
		$style = 'background-position: ' . $parts[0] . ' ' . $parts[1] . ';background-size: ' . $parts[2];

		$parts = array_map(function ($item) {
			return str_replace('%', '', $item);
		}, $parts);

		return [
			'range_slider' => [
				'margL' => $parts[0],
				'margT' => $parts[1],
				'sizeImg' => $parts[2],
			],
			'style' => $style
		];
	}

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
}
