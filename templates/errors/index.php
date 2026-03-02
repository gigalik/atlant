<? require_once dirname(dirname(__FILE__)) . '/include/header.php'; ?>

<section class="error text-center d-flex align-items-center">
  <div class="container">
    <h1 class="display-1"><?= http_response_code() ?></h1>
    <div class="error__lead mb-4"><p><?= Router::get_code(http_response_code())['message'] ?></p></div>
    <a class="btn btn-main lg" href="<?= SITE_URL ?>"><?= Langs::get('btns', 'Return to home') ?></a>
  </div>
</section>

<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>