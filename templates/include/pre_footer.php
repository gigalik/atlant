</main>

<section class="icons">
	<div class="icons__fixed-icons">
		<a href="tel:<?= Formatting::phone(json_decode($results['information']->phone, true)[0]['number']) ?>" class="icons__fixed-icons__call">
			<img src="/img/icons/call.svg" class="call-icon" alt="">
		</a>

		<a href="#" class="icons__fixed-icons__scroll scroll-to-top" id="scroll-to-top">
			<img src="/img/icons/scroll.svg" class="scroll-icon" alt="">
		</a>
	</div>
</section>

<div class="footer">
	<div class="container-fluid">
		<div class="footer-content row">
			<div class="footer-left col-md-6">
				<div class="logo">
					<a href="<?= SITE_URL ?>"><img src="/img/logo.svg" class="logo-main" alt="<?= $results['information']->title ?>"></a>
				</div>
				<div class="social-icons d-flex">
					<a href="#"><img src="/img/icons/telegram.svg" class="footer-icon telegram" alt="Telegram"></a>
					<a href="#"><img src="/img/icons/vk.svg" class="footer-icon vk" alt="VK"></a>
					<a href="#"><img src="/img/icons/star.svg" class="footer-icon star" alt="Star"></a>
					<a href="#"><img src="/img/icons/rutube.svg" class="footer-icon rutube" alt="RuTube"></a>
				</div>
			</div>

			<div class="col-md-6">

				<div class="footer-middle d-none d-lg-block">
					<ul class="footer__menu d-flex p-0">
						<li class="footer-services"><a href="/about">О компании</a></li>
						<li class="footer-services"><a href="/services">Услуги</a></li>
						<li class="footer-services"><a href="/cases">Кейсы</a></li>
						<li class="footer-services"><a href="/contacts">Контакты</a></li>
					</ul>
				</div>


				<div class="footer-right d-md-flex">
					<p class="footer-info"><?= $results['information']->address ?></p>
					<div class="footer-info__contain">
						<? foreach (json_decode($results['information']->phone) as $phone): ?>
							<a href="<?= Formatting::phone($phone->number) ?>" class="footer-info"><?= Formatting::phone($phone->number, $phone->type) ?></a>
						<? endforeach; ?>
						<a href="<?= 'mailto:' . $results['information']->email ?>" class="footer-info"><?= $results['information']->email ?></a>
					</div>
				</div>
			</div>
		</div>

		<div class="footer__block">
			<div class="footer__copyright me-auto">
				<div class="fw-300 mb-3">&copy; <?= date('Y') ?> <?= json_decode($results['information']->requisites)->title ?: $results['information']->title ?></div>
			</div>
			<a href="" class="footer__policy" target="_blank">Политика конфиденциальности</a>
			<div class="footer__developer fw-300 justify-content-sm-end justify-content-center">
				<a class="developer" href="//devstarter.technology?utm_source=<?= $_SERVER['SERVER_NAME'] ?>" target="_blank">Developed<span class="love">&nbsp;with&nbsp;❤️</span> in Dev<span class="fw-sb">Starter<sup>®</sup></span></a>
			</div>
		</div>
	</div>
	</footer>

	<script type="text/javascript" asynс src="<?= TEMPLATE . '/assets/js/jquery.min.js?1' ?>"></script>
	<script type="text/javascript" src="<?= TEMPLATE . '/assets/js/bootstrap.bundle.min.js' ?>"></script>
	<script type="text/javascript" src="<?= TEMPLATE . '/assets/js/scripts.min.js' ?>"></script>
	<script type="text/javascript" src="<?= TEMPLATE . '/assets/js/lazy-load.min.js' ?>"></script>
	<script type="text/javascript" defer src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
	<? require_once __DIR__ . '/offcanvas_menu.php' ?>
	<script>
		document.getElementById('scroll-to-top').addEventListener('click', function(e) {
			e.preventDefault();
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		});
	</script>
	<script type="text/javascript" defer>
		$(document).ready(function() {
			var fired = false;
			ymaps.ready(getObjects);
			var myMap;

			function getObjects() {
				$(".map").empty();
				init([{
					title: "<?= $results['information']->title ?>. Зыряновская улица, 48",
					lon: 87.355421,
					lat: 53.798087,
				}]);
			}

			function init(arObjects) {
				let center_lat = 0,
					center_lon = 0;

				myMap = new ymaps.Map("map", {
					center: [(center_lat / arObjects.length), (center_lon / arObjects.length)],
					zoom: 14,
					controls: ['smallMapDefaultSet']
				});

				myMap.controls.remove('zoomControl');
				myMap.controls.remove('searchControl');
				myMap.controls.remove('typeSelector');
				myMap.controls.remove('fullscreenControl');
				myMap.controls.remove('routeButtonControl');
				myMap.controls.remove('trafficControl');
				myMap.controls.remove('geolocationControl');
				myMap.controls.remove('rulerControl');

				$.each(arObjects, function(i, val) {
					center_lat += parseFloat(val.lat);
					center_lon += parseFloat(val.lon);

					myMap.geoObjects.add(new ymaps.Placemark([val.lat, val.lon], {
						balloonContentHeader: val.title,
					}));
				});

				let x = center_lat / arObjects.length,
					y = center_lon / arObjects.length;

				myMap.setCenter([x, y], 14, {
					checkZoomRange: true
				});

				if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
					myMap.behaviors.disable('drag');
				}
			}
		});
	</script>