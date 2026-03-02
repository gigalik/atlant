<? 
  require_once dirname(dirname(__FILE__)) . "/include/header.php"; 
  Breadcrumb::add(SITE_URL, 'Главная');
	Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['controller_info']->title);
  if(!empty($results['doc_category'])) Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['doc_category']->title);
  Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['doc']->title);
	Breadcrumb::out();
?>

<section class="docs">
  <div class="container-fluid">
    <div class="mb-5">
      <h1 class="docs__title fw-bold mb-2"><?= $results['doc']->title ?></h1>

      <?= (!empty($results['doc']->description)) ? '<p class="mb-2">' . $results['doc']->description . '</p>' : ''; ?>

      <small class="text-muted"><?= $results['doc']->date_modify != $results['doc']->date_create ? 'Опубликовано: ' . Formatting::date($results['doc']->date_create, 'd.m.y H:i') . ', Обновлено: ' . Formatting::date($results['doc']->date_modify, 'd.m.y H:i') : 'Опубликовано: ' . Formatting::date($results['doc']->date_create, 'd.m.y H:i') ?></small>
    </div>

    <div class="row">
      <div class="col-lg-4 order-1 order-xl-2">
        <div class="p-3 mb-4 bg-light sticky-top rounded" style="top:60px">
          <div class="list-group list-group-flush">
            <? foreach ($results['docs_categories'] as $key => $category) : ?>
              <p class="list-group-item bg-transparent parent border-0 py-1 fw-bold m-0"><?= $category->title ?></p>

              <?
              foreach ($results['docs'] as $key => $doc) :
                if ($doc->category_id == $category->id) :
              ?>
                  <a class="list-group-item list-group-item-action bg-transparent parent border-0 py-1 ms-2 <?= ($GLOBALS['router']->getSef() == $doc->sef) ? ' fw-bold text-deep-green' : 'text-body' ?>" href="<?= ($GLOBALS['router']->getMethodPrefix() ? '/' . substr($GLOBALS['router']->getMethodPrefix(), 0, -1)  : '') . '/docs/view/' .  $doc->sef ?>"><?= $doc->title ?></a>

                  <? if ($GLOBALS['router']->getSef() == $doc->sef) : ?>
                    <div class="parts list-group list-group-flush ms-4" id="target">
                      <?
                      foreach (json_decode($doc->content) as $key_part => $part) :
                        if (!empty($part->title)) :
                      ?>
                        <a class="list-group-item bg-transparent border-0 py-1" href="<?= '#' . $doc->sef . '_part_' . $key_part ?>"><small><?= $part->title ?></small></a>
                      <?
                        endif;
                      endforeach;
                      ?>
                    </div>
                  <? endif; ?>
              <?
                endif;
              endforeach;
              ?>
            <? endforeach; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-8 order-2 order-xl-1">
        <div class="text-block" id="doc_text">
          <?
          foreach (json_decode($results['doc']->content) as $key_part => $part) :
            $content .= '<h2 class="fw-bold mb-5">' . $part->title . '</h2>' . $part->content;
          ?>
            <div class="bg-white mb-4 overflow-auto good-detail__description" id="<?= $results['doc']->sef . '_part_' . $key_part ?>">
              <?= !empty($part->title) ? '<h2 class="fw-bold mb-5">' . $part->title . '</h2>' : '' ?>

              <?= Formatting::remove_attributes($part->content, ['href']) ?>
            </div>
          <? endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<? require_once dirname(dirname(__FILE__)) . "/include/pre_footer.php"; ?>
<style>
  .list-group-item {color: #01052d!important;}
  .list-group-item.active {color: #333EA8!important;}
</style>
<script type="text/javascript">
  $(document).ready(function() {
    $('#doc_text a').attr('target', '_blank').addClass('text-break');
    $('#doc_text p').addClass('text-break');
    $('#doc_text *').removeAttr("style");
    $('#doc_text iframe').css({
      "width": "100%",
    });
    $('#doc_text img').css({
      "width": "auto",
    });

    $('body').scrollspy({
      target: '#target',
      offset: 80
    });

    if (document.location.hash != '') getOverHere('.text-block ' + document.location.hash, -80);

    $('.sidebar .parts a').on('click', function(e) {
      e.preventDefault();
      let anchor = $(this).attr('href');

      getOverHere('.text-block ' + anchor, -80);
    });
  });
</script>
<? require_once dirname(dirname(__FILE__)) . "/include/footer.php"; ?>