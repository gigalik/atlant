<?php
class HomepageController
{
  public function index()
  {
    return [
      'services' => Db::connect('services')->where('status', 1,)->where('category', 'physical')->orderBy('rating ASC')->getAll(),
      'servicesLegal' => Db::connect('services')->where('status', 1,)->where('category', 'legal')->orderBy('rating ASC')->getAll(),
    ];
  }

  public function cabinet_index()
  {
    App::check_auth(CI);

    return [



      'seo' => [
        'title' => Langs::get('headers', 'Personal account | %SITE_NAME%'),
        'robots' => [
          'i' => 'noindex',
          'f' => 'nofollow'
        ]
      ]
    ];
  }

  public function admin_index()
  {
    App::check_auth($redirect = CI);
    App::check_permission_manual([1, 2, 3]);

    return [
      'orders' => $orders = Db::connect('orders')->where(['status_id' => 1])->orderBy('c_date DESC')->getAll() ?: [],
      'payment_orders' => Db::connect('orders')->select('id, price')->where(['user_id' => User::get_data()['id'], 'payment' => 1])->getAll() ?: [],
      'orders_statuses' => Db::connect('orders_statuses')->where(['status' => 1])->orderBy('rating ASC')->getAll() ?: [],
      'requests' => $requests = Db::connect('requests')->where(['status_id' => 1])->getAll() ?: [],
      'requests_statuses' => Db::connect('requests_statuses')->where('status', 1)->orderBy('rating ASC')->getAll() ?: [],
      'reviews' => $reviews = Db::connect('reviews')->where(['status' => 0])->getAll() ?: [],
      // 'goods' => !empty($reviews) ? Db::connect('goods')->select('id, title, sef, img')->in('id', array_column($reviews, 'good_id'))->getAll() : [],
      // 'users' => Db::connect('users')->select('id, name, surname, phone, username, img')->in('id', array_column(array_merge($orders, $requests, $reviews), 'user_id'))->getAll() ?: [],
      'users' => Db::connect('users')->select('id, name, surname, phone, username, img')->getAll(),
      'payment_types' => Db::connect('payment_types')->where(['status' => 1])->orderBy('rating ASC')->getAll(),
    ];
  }
}
