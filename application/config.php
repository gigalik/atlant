<?php
ini_set('display_errors', false);
date_default_timezone_set('Asia/Novokuznetsk');

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

define('DEFAULT_CONTROLLER', 'homepage');
define('DEFAULT_ACTION', 'index');
define('DEFAULT_LANG', 'ru');

define('ITEMS_PER_PAGE_LIST', [10, 25, 50, 100]);
define('ADMIN_ITEMS_PER_PAGE', 25);
define('DEFAULT_ITEMS_PER_PAGE', 24);
define('DEFAULT_SORTING', 'id asc');
define('DEFAULT_SORTING_TYPE', 'asc');
define('DEFAULT_DISPLAY_STATUS', 1);

$protocol = ($_SERVER['HTTP_X_FORWARDED_PORT'] == 443) ? 'https://' : 'http://';
define('SITE_URL', $protocol . $_SERVER['SERVER_NAME']);

/*** PATH ***/
define('VIEWS', 'views');
define('TEMPLATE', DS . VIEWS . DS . 'default');
define('CABINET_TEMPLATE', DS . VIEWS . DS . 'cabinet');
define('ADMIN_TEMPLATE', DS . VIEWS . DS . 'admin');
define('CONF', __DIR__ . DS . 'data');

define('MODELS', __DIR__ . DS . 'models');
define('CONTROLLERS', __DIR__ . DS . 'controllers');
define('EXTENSIONS', __DIR__ . DS . 'extensions');
define('UTILITIES', __DIR__ . DS . 'utilities/templates');
define('SALT', '20d8b019b4c89b09f5ad6ced93fef0bcdb1f232a5d625090fb2cebedaa43a428');

define('IMG', 'img');

/*** AUTOLOADER ***/
spl_autoload_register(function ($class_name) {
  $is_controller = preg_match('/Controller$/', $class_name);
  $path = $is_controller ? CONTROLLERS . DS . preg_replace('/controller$/', '.', strtolower($class_name)) . 'controller.php' : MODELS . DS . ucfirst($class_name) . '.php';

  if (file_exists($path)) {
    require_once($path);
  } elseif ($is_controller) {
    Router::set_code(404);
  } else
    throw new Exception('Model "' . $class_name . '" not found!');
});

set_exception_handler(function ($exception) {
  $message = '<b>=====[' . SITE_URL . ']=====</b><br>';
  $message .= '<b>Message:</b> ' . $exception->getMessage() . '<br>';
  $message .= '<b>File:</b> ' . $exception->getFile() . ':' . $exception->getLine() . '<br>';
  $message .= '<b>User IP:</b> ' . $_SERVER['REMOTE_ADDR'] . '<br>';
  $message .= '<b>REQUEST URI:</b> ' . SITE_URL . $_SERVER["REQUEST_URI"] . '<br>';
  $message .= '<b>HTTP REFERER:</b> ' . ($_SERVER["HTTP_REFERER"] != null ? $_SERVER["HTTP_REFERER"] : 'UNKNOWN') . '<br>';
  $message .= '<b>Time:</b> ' . date('d.m.Y H:i:s') . '<br>';
  $message .= '<br>';

  foreach ($exception->getTrace() as $index => $trace) {
    $message .=  '<b>=====[FRAME ' . ($index + 1) . ']=====</b><br>';
    $message .=  '<b>Function:</b> ' . ($trace['class'] != null ? $trace["class"] . $trace["type"] : '') . $trace["function"] . '()<br>';

    if ($trace['file'] != null)
      $message .= '<b>File:</b> ' . $trace['file'] . ':' . $trace['line'] . '<br>';

    $message .= '<br>';
  }

  $ips = [
    '95.181.85.91',
  ];

  if (in_array($_SERVER["REMOTE_ADDR"], $ips)) {
    echo '<pre>';
    echo $message;
    echo '</pre>';
  }

  Router::set_code(500);
});
