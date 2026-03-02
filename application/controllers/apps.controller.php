<?php 
class AppsController
{
  public function index() {
    Router::set_code(403);
  }

  public function cabinet_index () {
    App::check_auth(CI);

    return [
      'apps' => Db::connect('apps')->orderBy('status DESC, rating ASC')->getAll(),
      'total' => (int) count(Db::connect('apps')->select('id')->getAll()),
    ];
  }
}
