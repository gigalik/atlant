<? require_once dirname(dirname(__FILE__)) . '/include/header.php';

Breadcrumb::add(SITE_URL, 'Главная');
Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['controller_info']->title);
Breadcrumb::out();
?>
<section class="casesDetail">
    <div class="container-fluid">
        <h2 class="casesDetail__titles">Кейсы</h2>
        <img src="" class="casesDetail__img" alt="">
        <p class="casesDetail__description">Копания предъявила моему клиенту иск о возмещении убытков за нарушение сроков доставки  угля по программе «Северный завоз».
В результате проведенной работы,  собранных доказательств и продуманной стратегии в иске   было полностью отказано. 
Более того в ходе рассмотрения дела нами был заявлен встречный иск, который был удовлетворен.  Т.е. мой клиент сэкономил 1 300 000 руб. и еще и получил доход 178.500 руб.
</p>
    </div>
</section>
<? require_once dirname(dirname(__FILE__)) . '/include/callback.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?> 