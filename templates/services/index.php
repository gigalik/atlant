<?
require_once dirname(dirname(__FILE__)) . '/include/header.php';

Breadcrumb::add(SITE_URL, 'Главная');
Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['controller_info']->title);
if (!empty($results['parent_category'])) Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController() . DS . 'category' . DS . $results['parent_category']->sef, $results['parent_category']->title);
if (!empty($results['category'])) Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['category']->title);
Breadcrumb::out();
?>

<section class="servicesMain">
	<div class="container-fluid">
		<h2 class="servicesMain__titles">Услуги</h2>

		<a href="/services" class="services__btn">Для физических лиц:</a>


		<div class="row mb-md-4 mb-2">
			<? foreach ($results['services'] as $service): ?>
				<div class="col-md-6 col-xl-4 mb-4">
					<div class="servicesMain__items">
						<img class="servicesMain__items__img" src="<?= $service->icon ?>" alt="">
						<div class="servicesMain__items__title"><?= $service->title ?></div>
					</div>
				</div>
			<? endforeach; ?>
		</div>
		<a href="/services" class="services__btn">Для юридических лиц:</a>


		<div class="row">
			<? foreach ($results['servicesLegal'] as $service): ?>
				<div class="col-md-6 col-xl-4 mb-4">
					<div class="servicesMain__items">
						<img class="servicesMain__items__img" src="<?= $service->icon ?>" alt="">
						<div class="servicesMain__items__title"><?= $service->title ?></div>
					</div>
				</div>
			<? endforeach; ?>
		</div>
	</div>
</section>

<? require_once dirname(dirname(__FILE__)) . '/include/cabinet.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/contacts.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<script type="text/javascript" src="<?= TEMPLATE . '/assets/js/slick.min.js' ?>"></script>
<script>
	$('.intro-slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: false,
		speed: 1500,
		autoplaySpeed: 5700,
		infinite: false,
		arrows: false,
		dots: true,
		fade: false,
		pauseOnFocus: true,
		pauseOnDotsHover: true,
	});
</script>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>