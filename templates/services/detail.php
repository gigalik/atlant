<?
	require_once dirname(dirname(__FILE__)) . '/include/header.php';

	Breadcrumb::add(SITE_URL, 'Главная');
	Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['controller_info']->title ?: 'Услуги');
	Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['service']->title);
	Breadcrumb::out();
?>

<section class="section">
	<div class="container-fluid">
		<h1 class="section__title ps-0"><?= $results['service']->title ?></h1>

		<div class="row">
			<div class="col-md-6">
				<div class="content__img about-block__img sticky-top" itemscope="" itemtype="http://schema.org/ImageObject">
					<meta itemprop="name" content="<?= $results['service']->title ?>">
					<meta itemprop="contentUrl" content="<?= json_decode($results['sevice']->img)->fullsize ?>">
					<figure class="mb-0">
						<picture>
							<img class="prw lazy" src="<?= json_decode($results['service']->icon)->svg ?>" data-src="<?= $results['service']->img ? json_decode($results['service']->img)->fullsize : json_decode($results['service']->icon)->svg ?>" alt="<?= $results['service']->title ?>">
						</picture>
					</figure>
				</div>
			</div>

			<div class="col-md-6">
				<div class="about-block__content">
					<?= $results['service']->content ?>

					<div class="btn__wrap">
						<a href="" class="btn btn-green">Записаться</a>
						<a href="" class="btn btn-gray">Прайс-лист</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<? require_once dirname(dirname(__FILE__)) . '/include/doctor.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/reviews.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/callback.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<script type="text/javascript" src="<?= TEMPLATE . '/assets/js/slick.min.js' ?>"></script>
<script type="text/javascript">
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

	//sliders mobile
	mobile();

	function mobile() {
		if ($(window).width() < 768) {
			$(".mobile-slider").slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				autoplay: false,
				speed: 1500,
				autoplaySpeed: 5700,
				arrows: false,
				dots: false,
			});
		}
	}

	$(window).resize(mobile);
</script>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>