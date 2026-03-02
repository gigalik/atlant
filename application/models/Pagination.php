<?php
require_once(ROOT . '/vendor/autoload.php');

class Pagination
{
  /**
   * Ссылок навигации на страницу
   */
  public int $max = 3;

  /**
   * Ключ для GET, в который пишется номер страницы
   */
  public string $index = 'page';

  /**
   * Текущая страница
   */
  public int $current_page;

  /**
   * Общее количество записей
   */
  public int $total;

  /**
   * Записей на страницу
   */
  public int $per_page;

  public int $amount;

  /**
   * Флаги для кнопок "вперед/назад"
   */
  public bool $is_first_page;
  public bool $is_last_page;

  /**
   * Флаги для кнопок "первая/последняя страница"
   */
  public bool $show_first_page = false;
  public bool $show_last_page = false;

  /**
   * Для циклов
   */
  public int $from_page;
  public int $to_page;


  /**
   * Запуск необходимых данных для навигации
   * @param int $total общее количество записей
   * @param int $current_page текущая страница
   */
  public function __construct(int $total, string $index = 'page', int $per_page = null, int $max = 3)
  {
    # Устанавливаем общее количество записей
    $this->total = $total;
    $this->max = $max;

    if ($per_page == null) {
      # Устанавливаем количество записей на страницу
      if ($GLOBALS['router']->getMethodPrefix() == 'admin_')
        $per_page = $_COOKIE['admin_items_per_page'] ?: ADMIN_ITEMS_PER_PAGE;
      else
        $per_page = $_COOKIE['items_per_page'] ?: DEFAULT_ITEMS_PER_PAGE;
    }

    $this->per_page = $per_page;

    # Устанавливаем ключ в url
    $this->index = $index;

    # Устанавливаем количество страниц
    $this->amount = $this->amount();

    # Устанавливаем номер текущей страницы
    $this->setCurrentPage($_GET[$index] ?: 0);

    $this->is_first_page = $this->current_page == 1;
    $this->is_last_page = $this->current_page == $this->amount;

    $limits = $this->limits();

    $this->from_page = $limits[0];
    $this->to_page = $limits[1];

    if ($this->amount >= $max) {
      $this->show_first_page = $this->from_page > 1;
      $this->show_last_page = $this->to_page < $this->amount;
    }
  }

  /**
   *  Для вывода ссылок
   * 
   *  HTML-код со ссылками навигации
   */
  public function get()
  {
    # Получаем ограничения для цикла
    $limits = $this->limits();

    $result = [];
    # Генерируем ссылки
    for ($page = $limits[0]; $page <= $limits[1]; $page++) {
      $result[] = [
        'active' => $page == $this->current_page,
        'title' => $page,
        'href' => $page == $this->current_page ? '#' :  $this->getHref($page)
      ];
    }

    // # Если ссылки создались
    // if (count($result) > 0) {
    //   # Если текущая страница не первая
    //   if ($this->current_page > 1)
    //     # Создаём ссылку "На первую"
    //     $result = array_merge([[
    //       'active' => false,
    //       'title' => '&laquo;',
    //       'href' => $this->getHref(1)
    //     ]], $result);

    //   # Если текущая страница не первая
    //   if ($this->current_page < $this->amount)
    //     # Создаём ссылку "На последнюю"
    //     $result = array_merge($result, [[
    //       'active' => false,
    //       'title' => '&raquo;',
    //       'href' => $this->getHref($this->amount)
    //     ]]);
    // }

    return $result;
  }

  /**
   * Для генерации HTML-кода ссылки
   * @param integer $page - номер страницы
   * 
   * @return
   */
  protected function getHref($page)
  {
    $currentURI = explode('?', $_SERVER['REQUEST_URI'])[0];
    $_GET[$this->index] = $page;

    # Формируем HTML код ссылки и возвращаем
    return $currentURI . '?' . http_build_query($_GET);
  }

  /**
   *  Для получения, откуда стартовать
   * 
   * @return массив с началом и концом отсчёта
   */
  protected function limits()
  {
    if ($this->amount < $this->max)
      return [1, $this->amount];

    $offset = $this->get_offset();

    # Вычисляем начало отсчёта
    $from = max($this->current_page - $offset, 1);
    $to = min($this->current_page + $offset, $this->amount);

    $count = $to - $from;

    $add_right = $this->current_page <= $offset ? $this->max - $count - 1 : 0;
    $add_left = $this->current_page > ($this->amount - $offset) ? $this->max - $count - 1 : 0;

    # Возвращаем
    return array($from - $add_left, $to + $add_right);
  }

  protected function get_offset()
  {
    return round(($this->max % 2 == 0 ? $this->max : $this->max - 1) / 2);
  }

  /**
   * Для установки текущей страницы
   * 
   * @return
   */
  protected function setCurrentPage($currentPage)
  {
    # Получаем номер страницы
    $this->current_page = $currentPage;

    # Если текущая страница боле нуля
    if ($this->current_page > 0) {
      # Если текунщая страница меньше общего количества страниц
      if ($this->current_page > $this->amount)
        # Устанавливаем страницу на последнюю
        $this->current_page = $this->amount;
    } else
      # Устанавливаем страницу на первую
      $this->current_page = 1;
  }

  /**
   * Для получеия общего числа страниц
   * 
   * @return int число страниц
   */
  protected function amount()
  {
    # Делим и возвращаем
    return ceil($this->total / $this->per_page);
  }
}
