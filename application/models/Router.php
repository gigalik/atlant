<?php
class Router
{
	protected $url;
	protected $method_prefix;
	protected $controller;
	protected $action;
	protected $sef;
	protected $params;
	protected $route;

	public function getUrl()
	{
		return $this->url;
	}

	public function getMethodPrefix()
	{
		return $this->method_prefix;
	}

	public function getController()
	{
		return $this->controller;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function getSef()
	{
		return $this->sef;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getRoute()
	{
		return $this->route;
	}

	public function __construct($url)
	{
		$routes = [
			'default' => '',
      'cabinet' => 'cabinet_',
			'admin' => 'admin_',
			'api' => 'api_',
		];

		$this->url = parse_url(urldecode(trim($url, '/')));
		$this->route = TEMPLATE;
		$this->method_prefix = isset($routes[$this->route]) ? $routes[$this->route] : '';
		$this->controller = DEFAULT_CONTROLLER;
		$this->action = DEFAULT_ACTION;
		$this->sef = '';

		$path_parts = explode('/', $this->url['path']);

		if (count($path_parts)) {
			if (in_array(strtolower(current($path_parts)), array_keys($routes))) {
				$this->route = strtolower(current($path_parts));
				$this->method_prefix = isset($routes[$this->route]) ? $routes[$this->route] : '';
				array_shift($path_parts);
			}

			if (current($path_parts)) {
				$this->controller = strtolower(current($path_parts));
				array_shift($path_parts);
			}

			if (current($path_parts)) {
				$this->action = strtolower(current($path_parts));
				array_shift($path_parts);
			}

			if (current($path_parts)) {
				$this->sef = strtolower(current($path_parts));
				array_shift($path_parts);
			}

			parse_str($this->url['query'], $this->params);
		}
	}

	private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
	private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	public static function redirect($location)
	{
		$code = 301;
		self::set_code($code, true);
		header("Access-Control-Allow-Origin: *");
		header("Location: " . $location, true, $code);
		exit();
	}

	public static function set_code($code = 200, $redirect = false)
	{
		$info = self::get_code($code);

		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header($protocol . ' ' . $code . ' ' . $info['message']);
		header('Status: ' . $code . ' ' . $info['message']);

		if($redirect != true)
			require_once ROOT . TEMPLATE . '/templates/errors/index.php';
	}

	public static function get_code($code = 200, $redirect = false)
	{
		switch ($code) {
			case 100:
				$message = Langs::get('codes', 'Continue');
				break;
			case 101:
				$message = Langs::get('codes', 'Switching Protocols');
				break;

			case 200:
				$message = Langs::get('codes', 'OK');
				break;
			case 201:
				$message = Langs::get('codes', 'Created');
				break;
			case 202:
				$message = Langs::get('codes', 'Accepted');
				break;
			case 203:
				$message = Langs::get('codes', 'Non-Authoritative Information');
				break;
			case 204:
				$message = Langs::get('codes', 'No Content');
				break;
			case 205:
				$message = Langs::get('codes', 'Reset Content');
				break;
			case 206:
				$message = Langs::get('codes', 'Partial Content');
				break;

			case 300:
				$message = Langs::get('codes', 'Multiple Choices');
				break;
			case 301:
				$message = Langs::get('codes', 'Moved Permanently');
				break;
			case 302:
				$message = Langs::get('codes', 'Moved Temporarily');
				break;
			case 303:
				$message = Langs::get('codes', 'See Other');
				break;
			case 304:
				$message = Langs::get('codes', 'Not Modified');
				break;
			case 305:
				$message = Langs::get('codes', 'Use Proxy');
				break;

			case 400:
				$message = Langs::get('codes', 'Bad Request');
				break;
			case 401:
				$message = Langs::get('codes', 'Unauthorized');
				break;
			case 402:
				$message = Langs::get('codes', 'Payment Required');
				break;
			case 403:
				$message = Langs::get('codes', 'Forbidden');
				break;
			case 404:
				$message = Langs::get('codes', 'Not Found');
				break;
			case 405:
				$message = Langs::get('codes', 'Method Not Allowed');
				break;
			case 406:
				$message = Langs::get('codes', 'Not Acceptable');
				break;
			case 407:
				$message = Langs::get('codes', 'Proxy Authentication Required');
				break;
			case 408:
				$message = Langs::get('codes', 'Request Time-out');
				break;
			case 409:
				$message = Langs::get('codes', 'Conflict');
				break;
			case 410:
				$message = Langs::get('codes', 'Gone');
				break;
			case 411:
				$message = Langs::get('codes', 'Length Required');
				break;
			case 412:
				$message = Langs::get('codes', 'Precondition Failed');
				break;
			case 413:
				$message = Langs::get('codes', 'Request Entity Too Large');
				break;
			case 414:
				$message = Langs::get('codes', 'Request-URI Too Large');
				break;
			case 415:
				$message = Langs::get('codes', 'Unsupported Media Type');
				break;

			case 500:
				$message = Langs::get('codes', 'Internal Server Error');
				break;
			case 501:
				$message = Langs::get('codes', 'Not Implemented');
				break;
			case 502:
				$message = Langs::get('codes', 'Bad Gateway');
				break;
			case 503:
				$message = Langs::get('codes', 'Service Unavailable');
				break;
			case 504:
				$message = Langs::get('codes', 'Gateway Time-out');
				break;
			case 505:
				$message = Langs::get('codes', 'HTTP Version not supported');
				break;

			default:
				exit('Unknown http status code "' . htmlentities($code) . '"');
		}

		return [
			'code' => $code, 
			'message' => $message,
		];
	}
}
