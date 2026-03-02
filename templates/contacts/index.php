<?
	require_once dirname(dirname(__FILE__)) . '/include/header.php';
	Breadcrumb::add(SITE_URL, 'Главная');
	Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['controller_info']->title);
	Breadcrumb::out();
?>

<section class="contact">
	<div class="container-fluid">
		<h2 class="contact__titles">Контакты</h2>

		<div class="contact__info row">
			<div class="contact__info__inn col-md-2">
				<p class="contact__info__inn__data">ИНН: <?= json_decode($results['information']->requisites)->tin ?></p>
				<p class="contact__info__inn__data">ОГРН: <?= json_decode($results['information']->requisites)->psrn ?></p>
				<p class="contact__info__inn__data">КПП: <?= json_decode($results['information']->requisites)->kpp ?></p>
			</div>

			<div class="contact__info__address col-md-4">
				<p class="contact__info__address__description"><?= json_decode($results['information']->requisites)->title ?></p>
				<p class="contact__info__address__description"><?= $results['information']->address ?></p>
			</div>

			<div class="contact__info__contact col-md-3">
				<? foreach(json_decode($results['information']->phone) as $phone): ?>
					<a href="<?= Formatting::phone($phone->number) ?>" class="contacts__info__contact__description"><?= Formatting::phone($phone->number, $phone->type) ?></a>
					<? endforeach; ?>
					<a href="<?= 'mailto:' . $results['information']->email ?>" class="contacts__info__contact__description"><?= $results['information']->email ?></a>
			</div>

			<div class="contact__info__social-icons d-flex col-md-3">
				<a href="#"><img src="/img/icons/telegram.svg" class="contact__info__social-icons__img telegram" alt="Telegram"></a>
				<a href="#"><img src="/img/icons/vk.svg" class="contact__info__social-icons__img vk" alt="VK"></a>
				<a href="#"><img src="/img/icons/star.svg" class="contact__info__social-icons__img star" alt="Star"></a>
				<a href="#"><img src="/img/icons/rutube.svg" class="contact__info__social-icons__img rutube" alt="RuTube"></a>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container-fluid">
		<div class="contact-map">
			<div id="map" class="map w-100 h-100"></div>
		</div>
	</div>
</section>

<? require_once dirname(dirname(__FILE__)) . '/include/callback.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<script type="text/javascript" defer src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
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
			}
			]);
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
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>