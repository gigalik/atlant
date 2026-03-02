<section class="section contacts">
	<div class="container-fluid">
		<div class="row">
			<a href="/contacts">
				<div class="arrow-container align-items-end">
					<h2 class="titles">Контакты</h2>
					<div class="arrow"><? require ROOT . "/img/homepage/arrow.svg" ?></div>
				</div>
			</a>

			<div class="contacts__info row">
				<div class="contacts__info__address col-md-4">
					<p class="contacts__info__address__description">«Георгиевский С.В» <br><?= $results['information']->address ?></p>
				</div>
				<div class="contacts__info__contact col-md-4">
					<? foreach (json_decode($results['information']->phone) as $phone): ?>
						<a href="<?= Formatting::phone($phone->number) ?>" class="contacts__info__contact__description d-block"><?= Formatting::phone($phone->number, $phone->type) ?></a>
					<? endforeach; ?>
					<a href="<?= 'mailto:' . $results['information']->email ?>" class="contacts__info__contact__description d-block"><?= $results['information']->email ?></a>
				</div>

				<div class="contacts__info__social-icons col-md-4">
					<!-- <? foreach ($results['social_networks'] as $social): ?>
						<a href="<?= $social->link ?>" target="_blank"><img src="/img/icons/telegram.svg" class="contacts__info__social-icons__img telegram" alt="<?= $social->title ?>"></a>
					<? endforeach; ?> -->

					<a href="#"><img src="/img/icons/telegram.svg" class="contacts__info__social-icons__img telegram" alt="Telegram"></a>
					<a href="#"><img src="/img/icons/vk.svg" class="contacts__info__social-icons__img vk" alt="VK"></a>
					<a href="#"><img src="/img/icons/star.svg" class="contacts__info__social-icons__img star" alt="Дзен"></a>
					<a href="#"><img src="/img/icons/rutube.svg" class="contacts__info__social-icons__img rutube" alt="RuTube"></a>
				</div>
			</div>
		</div>
		<div class="contacts__map-wrap">
			<div class="contact-map">
				<div id="map" class="map w-100 h-100"></div>
			</div>
		</div>
	</div>
</section>