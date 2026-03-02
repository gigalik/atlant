<?php
class RssController
{
  function index()
  {
    $results = [
      'information' => Db::connect('information')->where(['lang_code' => $_GET['lang'] ?: DEFAULT_LANG])->get(),
      'articles' => Db::connect('articles')->where(['status' => 1])->in('category_id', [2, 4])->getAll(),
    ];

    header('Content-Type: text/xml; charset=UTF-8');
    header('Content-Type: application/rss+xml; charset=UTF-8');
    header('Content-Type: application/xml; charset=UTF-8');

    echo '<?xml version="1.0" encoding="UTF-8" ?>
      <rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://search.yahoo.com/mrss/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:georss="http://www.georss.org/georss">
        <channel>
          <atom:link href="' . SITE_URL . '/rss/" rel="self" type="application/rss+xml" />
          <title>' . preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['information']->seo_title ?: $results['information']->title) . '</title>
          <link>' . SITE_URL . '</link>
          <description>' . strip_tags(preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['information']->seo_description ?: $results['information']->description)) . '</description>
          <copyright>' . '2015 - ' . date('Y') . '. ' . SITE_URL . '</copyright>
          <generator>Jumpstarter by DevStarter Technology</generator>
          <image>
            <link>' . SITE_URL . '</link>
            <title>' . preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['information']->seo_title ?: $results['information']->title) . '</title>
            <url>' . SITE_URL . '/img/favicons/apple-touch-icon-precomposed.png</url>
          </image>
        <language>' . ($_GET['lang'] ?: DEFAULT_LANG) . '</language>
    ';

    foreach ($results['articles'] as $article) :
      $art_img = json_decode($article->img, true)['fullsize'];
      $author = Db::connect('users')->select('name, surname, email')->where(['id' => $article->author_id])->get();

      $article->content = preg_replace('/[\x00-\x1F\x7F]/u', '', $article->content);
      $article->content = preg_replace('/&#171;/', '"', $article->content);
      $article->content = preg_replace('/&#187;/', '"', $article->content);
      $article->content = preg_replace('/&#8221;/', '"', $article->content);
      $article->content = preg_replace('/&#39;/', '\'', $article->content);
      $article->content = preg_replace('/&nbsp;/', ' ', $article->content);
      $article->content = preg_replace('/&#160;/', ' ', $article->content);
      $article->content = preg_replace('/&mdash;/', '-', $article->content);
      $article->content = preg_replace('/&ndash;/', '-', $article->content);
      $article->content = preg_replace('/&#8211;/', '–', $article->content);
      $article->content = preg_replace('/&amp;/', '&', $article->content);
      $article->content = preg_replace('/&lt;/', '<', $article->content);
      $article->content = preg_replace('/&gt;/', '>', $article->content); 
      $article->content = preg_replace('/&laquo;/', '«', $article->content);
      $article->content = preg_replace('/&raquo;/', '»', $article->content); 

      echo '
        <item>
          <enclosure length="'. filesize(ROOT . $art_img) .'" url="' . SITE_URL . $art_img . '" type="'. mime_content_type(ROOT . $art_img) .'" />
          <title>' . ($article->seo_title ?: $article->title) . '</title>
          <link>' . SITE_URL . '/articles/post/' . $article->sef . '</link>

          <description><![CDATA[' . str_replace(['<p><br></p>', '<img src="/assets/js/ckeditor/kcfinder/', '/admin/templates/assets/js/ckeditor/kcfinder/'], ['', '<img src="' . SITE_URL . '/img/', SITE_URL . '/img/'], Formatting::remove_attributes(strip_tags($article->content, ['p', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']), ['src'])) . ']]></description>

          <author>' . ($author->email ?: $results['information']->email) . ' (' . $results['information']->title . ')</author>
          <pubDate>' . Formatting::date($article->publication_date, DateTime::RFC822) . '</pubDate>
          <guid>' . SITE_URL . '/articles/post/' . $article->sef . '</guid>
        </item>
      ';
    endforeach;

    echo '
        </channel>
      </rss>
    ';
    die();
  }

  function turbo()
  {
    $results = [
      'information' => Db::connect('information')->where(['lang_code' => $_GET['lang'] ?: DEFAULT_LANG])->get(),
      'articles' => Db::connect('articles')->where(['status' => 1])->in('category_id', [2, 4])->getAll(),
    ];

    header('Content-Type: text/xml; charset=UTF-8');
    header('Content-Type: application/rss+xml; charset=UTF-8');
    header('Content-Type: application/xml; charset=UTF-8');

    echo '<?xml version="1.0" encoding="UTF-8" ?>
      <rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
        <channel>
          <title>' . preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['information']->seo_title ?: $results['information']->title) . '</title>
          <link>' . SITE_URL . '</link>
          <description>' . strip_tags(preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['information']->seo_description ?: $results['information']->description)) . '</description>
          <copyright>' . '2015 - ' . date('Y') . '. ' . SITE_URL . '</copyright>
          <generator>Jumpstarter by DevStarter Technology</generator>
          <turbo:analytics type="Yandex" id="32969884"></turbo:analytics>
          <image>
            <link>' . SITE_URL . '</link>
            <title>' . preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['information']->seo_title ?: $results['information']->title) . '</title>
            <url>' . SITE_URL . '/img/favicons/apple-touch-icon-precomposed.png</url>
          </image>
        <language>' . ($_COOKIE['language'] ?: DEFAULT_LANG) . '</language>
    ';

    foreach ($results['articles'] as $article) :
      $art_img = json_decode($article->img, true)['fullsize'];
      $author = Db::connect('users')->select('name, surname, email')->where(['id' => $article->author_id])->get();

      $article->content = preg_replace('/[\x00-\x1F\x7F]/u', '', $article->content);
      $article->content = preg_replace('/&#171;/', '"', $article->content);
      $article->content = preg_replace('/&#187;/', '"', $article->content);
      $article->content = preg_replace('/&#8221;/', '"', $article->content);
      $article->content = preg_replace('/&#39;/', '\'', $article->content);
      $article->content = preg_replace('/&nbsp;/', ' ', $article->content);
      $article->content = preg_replace('/&#160;/', ' ', $article->content);
      $article->content = preg_replace('/&mdash;/', '-', $article->content);
      $article->content = preg_replace('/&ndash;/', '-', $article->content);
      $article->content = preg_replace('/&#8211;/', '–', $article->content);
      $article->content = preg_replace('/&amp;/', '&', $article->content);
      $article->content = preg_replace('/&lt;/', '<', $article->content);
      $article->content = preg_replace('/&gt;/', '>', $article->content); 
      $article->content = preg_replace('/&laquo;/', '«', $article->content);
      $article->content = preg_replace('/&raquo;/', '»', $article->content); 

      echo '
        <item turbo="true">
          <enclosure length="'. filesize(ROOT . $art_img) .'" url="' . SITE_URL . $art_img . '" type="'. mime_content_type(ROOT . $art_img) .'" />
          <title>' . ($article->seo_title ?: $article->title) . '</title>
          <turbo:extendedHtml>true</turbo:extendedHtml>
          <link>' . SITE_URL . '/articles/post/' . $article->sef . '</link>
          <turbo:source>' . SITE_URL . '/articles/post/' . $article->sef . '</turbo:source>
          <turbo:content><![CDATA[<figure><img src="' . SITE_URL . $art_img . '"></figure>' . str_replace(['<p><br></p>', '<img src="/assets/js/ckeditor/kcfinder/', '/admin/templates/assets/js/ckeditor/kcfinder/'], ['', '<img src="' . SITE_URL . '/img/', SITE_URL . '/img/'], Formatting::remove_attributes(strip_tags($article->content, ['p', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']), ['src'])) . ']]></turbo:content>
          <author>' . ($author->email ?: $results['information']->email) . ' (' . $results['information']->title . ')</author>
          <pubDate>' . Formatting::date($article->publication_date, DateTime::RFC822) . '</pubDate>
        </item>
      ';
    endforeach;

    echo '
        </channel>
      </rss>
    ';
    die();
  }

  function market()
  {
    $category_where = [];
    $category_where['status'] = 1;
    $category_where['lang_code'] = $_GET['lang'] ?: DEFAULT_LANG;

    $product_where = [];
    $product_where['status'] = 1;
    $product_where['lang_code'] = $_GET['lang'] ?: DEFAULT_LANG;

    $results = [
      'information' => Db::connect('information')->where(['lang_code' => $_GET['lang'] ?: DEFAULT_LANG])->get(),
      'goods' => Db::connect('products')->where($product_where)->getAll(),
      'goods_categories' => Db::connect('products_categories')->where($category_where)->getAll(),
    ];

    header('Content-Type: text/xml; charset=UTF-8');
    header('Content-Type: application/rss+xml; charset=UTF-8');
    header('Content-Type: application/xml; charset=UTF-8');

    echo '<?xml version="1.0" encoding="UTF-8"?>
      <yml_catalog date="' . Formatting::date(date('Y-m-d H:i:s'),DateTime::RFC822) . '">
        <shop>
          <name>' . preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['information']->title) . '</name>
          <company>' . preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['information']->title) . '</company>
          <url>' . SITE_URL . '</url>
          <platform>Jumpstarter by DevStarter Technology</platform>
          <agency>DevStarter Technology</agency>
          <email>' . $results['information']->email . '</email>
          <currencies>
            <currency id="RUR" rate="1"/>
          </currencies>
          <categories>';

          foreach ($results['goods_categories'] as $category):
            echo '<category id="' . ($category->id ?: $category['id']) . '">' . ($category->title ?: $category['title']) . '</category>';
            // foreach (GoodsSubcategories::getList(1, (int) ($category->id ?: $category['id'])) as $subcategory) :
            //   echo '<category id="' . $category->id . $subcategory->id . '" parentId="' . $category->id . '">' . $subcategory->title . '</category>';
            // endforeach;
          endforeach;
          
          echo '
          </categories>
          
          <delivery-options>
            <option cost="0" days="1" />
          </delivery-options>
          <cpa>1</cpa>
          <offers>';

    foreach ($results['goods'] as $good):
      $art_img = json_decode($good->img, true)['full']['path'];
      $advantages = $good->advantages ? implode(', ', array_column(json_decode($good->advantages, true), 'text')) : [];

      echo '
            <offer id="' . $good->id . '" available="' . ($good->status != 0 ? 'true' : 'false') . '">
              <url>' . SITE_URL . '/catalog/detail/' . $good->sef . '/</url>
              <price>' . ((($good->discount != 0) || ($good->price_discount != 0)) ? $good->price_discount : $good->price) . '</price>' . ((($good->discount != 0) || ($good->price_discount != 0)) ? '
              <oldprice>' . $good->price . '</oldprice>' : '') . '
              <currencyId>RUR</currencyId>';
              foreach(json_decode($good->category, true) as $key => $value):
                echo '<categoryId>' . $value . '</categoryId>';
              endforeach;
      echo '
              <picture>' . SITE_URL . $art_img . '</picture>
              <store>true</store>
              <pickup>true</pickup>
              <delivery>true</delivery>
              <weight>' . $good->weight / 1000 . '</weight>
              <name>' . htmlspecialchars($good->title) . '</name>
              <description><![CDATA[' . htmlspecialchars(
                $good->description .
                (!empty($advantages) ? '<h2>Преимущества<h2>' . $advantages : '')
              ) . ']]></description>
              <vendor>' . $results['information']->title . '</vendor>
              <sales_notes>Необходима предоплата</sales_notes>
              <manufacturer_warranty>true</manufacturer_warranty>
              <country_of_origin>Россия</country_of_origin>';
              foreach(json_decode($good->functions, true) as $key => $value):
                echo '<param name="' . $value['title']  . '">' . strip_tags($value['description'])  . '</param>';
              endforeach;
      echo '
              <cpa>1</cpa>
            </offer>';
    endforeach;
    echo '
          </offers>
        </shop>
      </yml_catalog>
    ';
    die();
  }
}
