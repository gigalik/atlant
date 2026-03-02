<?php
class ServicesController
{
  public function index()
  {
    return [
      'services' => Db::connect('services')->where('status', 1,)->where('category', 'physical')->orderBy('rating ASC')->getAll(),
      'servicesLegal' => Db::connect('services')->where('status', 1,)->where('category', 'legal')->orderBy('rating ASC')->getAll(),
    ];
  }

  public function detail()
  {
    if (!$service = Db::connect('services')->where(['sef' => strip_tags($GLOBALS['router']->getSef())])->get())
      Router::redirect('/' . $GLOBALS['router']->getController());

    if ($service->status != 1)
      Router::get_code(404, true);

    Db::connect('services')->where(['id' => (int) $service->id])->update(['views' => (int) $service->views + 1]);

    return [
      'service' => $service,
      'seo' => [
        'title' => $service->seo_title ?: $service->title,
        'description' => strip_tags($service->seo_title ?: $service->description),
        'robots' => [
          'i' => $service->robots_index ?: 'index',
          'f' => $service->robots_follow ?: 'follow',
        ]
      ]
    ];
  }

  public function admin_index()
  {
    App::check_auth(CI);
    App::check_permission($GLOBALS['router']->getController());

    $limit = (int) $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
    $sorting = $_COOKIE['items_sorting'] ?: DEFAULT_SORTING;
    $offset = (int) $_GET['page'] ? ($_GET['page'] - 1) * $limit : 0;

    $where = [];

    return [
      'services' => $services = Db::connect('services')->where($where)->limit($limit)->offset($offset)->orderBy($sorting)->getAll(),
      'total' => count(Db::connect('services')->select('id')->where($where)->getAll())
    ];
  }

  public function admin_add()
  {
    App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
    App::check_permission($GLOBALS['router']->getController());

    if (isset($_POST['add']) || isset($_POST['edit'])) {
      if ($_POST['data'] == null) {
        Notify::createError(Langs::get('messages', 'Data not sending'));
        Router::redirect($_SERVER['HTTP_REFERER']);
      }

      $_POST['data']['sef'] = $_POST['data']['sef'] ?: Text::translit($_POST['data']['title'], 'sef');
      $_POST['data']['content'] = $_POST['data']['content'] ?: '';
      $_POST['data']['seo_title'] = $_POST['data']['seo_title'] ?: $_POST['data']['title'];

      if ((UPLOAD_ERR_OK === $_FILES['image']['error']) && $_POST['deleteImage'] != "on") {
        $image_id = time();

        $_POST['data']['img'] = json_encode([
          'fullsize' => Images::upload_image($image_id, $GLOBALS['router']->getController(), 'fullsize', $_FILES['image']['tmp_name'], ['extention' => 'png', 'new_width' => 1024]),
          'thumb' => Images::upload_image($image_id, $GLOBALS['router']->getController(), 'thumb', $_FILES['image']['tmp_name'], ['extention' => 'png', 'new_width' => 512]),
        ]);
      }

      if (isset($_POST['edit']))
        $service = Services::edit($_POST['data']);
      else if (isset($_POST['add']))
        $service = Services::add($_POST['data']);

      if ($service['code'] != 200 && $service['code'] != 201) {
        Notify::createError($service['message']);
        Router::redirect($_SERVER['HTTP_REFERER']);
      }

      Notify::createSuccess($service['message']);
      Router::redirect($redirect);
    }

    return [
      'service' => (object) [
        'robots_index' => 'index',
        'robots_follow' => 'follow',
        'status' => 1,
        'rating' => (int) (count(Db::connect('services')->select('id')->getAll()) + 1) * 10
      ],
      'services_categories' => Db::connect('services_categories')->where(['status' => 1])->orderBy('title ASC')->getAll(),
      'cities' => null, //Db::connect('cities')->orderBy('title ASC')->getAll(),
      'rating' => (int) (count(Db::connect('services')->select('id')->getAll()) + 1) * 10,
    ];
  }

  public function admin_edit()
  {
    $results = $this->admin_add();

    if (!$service = Db::connect('services')->where(['id' => (int) $_GET['id']])->get()) {
      Notify::createError(Langs::get('messages', 'Not found'));
      Router::redirect($_SERVER['HTTP_REFERER']);
    }

    $results['service'] = $service;

    return $results;
  }

  public function delete()
  {
    App::check_auth(CI);
    App::check_permission($GLOBALS['router']->getController());

    if (isset($_REQUEST['delete']) == false)
      echo Formatting::json(['type' => 'Error', 'message' => 'Delete param not found'], 400);

    $response = Services::delete($_REQUEST);
    echo Formatting::json($response['data'], $response['code']);
  }

  public function api_edit()
  {
    App::check_auth($redirect = $_POST['redirect'] ? base64_decode($_POST['redirect']) : CI);
    App::check_permission($GLOBALS['router']->getController());

    if (isset($_POST['edit'])) {
      if ($_POST['data'] == null)
        echo Formatting::json(['message' => Langs::get('messages', 'Data not sending')], 400);

      $controller = Services::edit($_POST['data']);
      echo Formatting::json(['message' => Langs::get('messages', $controller['message'])], $controller['code']);
    }

    die();
  }
}
