<?php
class Breadcrumb
{
  private function __construct(){ /* ... @return ClassName */}  // We protect from creation through "new ClassName"
  private function __clone(){ /* ... @return ClassName */}  // We protect from creation through "cloning"
  private function __wakeup(){ /* ... @return ClassName */}  // We protect from creation through "unserialize"

	private static $_items = array();

	public static function add($url, $name)
	{
		self::$_items[] = array($url, $name);
	}

	public static function out()
	{
		$res = '
      <section class="breadcrumbs mb-4">
        <div class="container-fluid">
          <nav itemscope="" itemtype="http://schema.org/BreadcrumbList" id="breadcrumbs" aria-label="breadcrumb" class="page__breadcrumb">
            <ol class="breadcrumb d-flex">
    ';

		$i = 1;
    $count = count(self::$_items);

		foreach (self::$_items as $key => $row) 
    {
      if(($key + 1) != $count)
      {
        $res .= '
          <li class="breadcrumb-item" itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem">
            <a href="' . $row[0] . '" itemprop="item">
              ' . $row[1] . '
              <meta itemprop="name" content="' . $row[1] . '">
            </a>
            <meta itemprop="position" content="' . ++$i . '">
          </li>
        ';
      } else {
        $res .= '
          <li class="breadcrumb-item active" itemscope="" itemprop="itemListElement" itemtype="http://schema.org/ListItem">
            ' . $row[1] . '
            <meta itemprop="name" content="' . $row[1] . '">
            <meta itemprop="position" content="' . ++$i . '">
          </li>
        ';
      }
		}
		
    $res .= '
            </ol>
          </nav>
        </div>
      </section>
    ';

		echo $res;
	}
}