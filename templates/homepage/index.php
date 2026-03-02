<? require_once dirname(dirname(__FILE__)) . '/include/header.php'; ?>
<section class="mainBanner">
	<div class="container-fluid">
		<div class="mainBanner__container">
			<div class="mainBanner__container__info">
				<h3 class="mainBanner__container__info__title">Консультации адвоката по налоговым спорам</h3>
				<p class="mainBanner__container__info__description">Наши юристы с опытом работы в налоговых органах, профильно специализируются на представлении ваших интересов в налоговых органах и судах в Новокузнецке.</p>
			</div>
			<div class="mainBanner__container__img">
				<img src="/img/homepage/Prortrait.png" class="obj-fit-contain" alt="">
			</div>
		</div>
	</div>
</section>

<section class="advantages section">
	<div class="container-fluid">
		<h3 class="advantages__title">Мы уверены, что поможем вам, потому что</h3>

		<div class="advantages__container">
			<div class="row">
				<div class="col-md-6">
					<div class="advantages__container__items">
						<img class="advantages__container__icon" src="/img/homepage/Icons.svg" alt="">
						<p class="advantages__container__description">Хорошо разбираемся в законодательстве и способны адаптировать стратегии под конкретные потребности клиентов</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="advantages__container__items">
						<img class="advantages__container__icon" src="/img/homepage/Icons2.svg" alt="">
						<p class="advantages__container__description">Имеем опыт работы в различных отраслях и юрисдикциях, что позволяет предлагать комплексные решения и избегать возможных проблем</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="advantages__container__items">
						<img class="advantages__container__icon" src="/img/homepage/icons3.svg" alt="">
						<p class="advantages__container__description">Мы профессионалы с высокой квалификацией, поэтому можем гарантировать качественное исполнение работы в срок</p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="advantages__container__items">
						<img class="advantages__container__icon" src="/img/homepage/icons4.svg" alt="">
						<p class="advantages__container__description">Нестандартно подходим к решению вопросов</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="section banner">
	<div class="container-fluid">
		<div class="banner__row row">
			<div class="col-md-7">
				<div class="banner__info">
					<a href="/about" class="banner__info__container">
						<h2 class="banner__info__name">Сергей Георгиевский</h2>
						<div class="banner__info__arrow"><? require ROOT . "/img/homepage/arrow.svg" ?></div>
					</a>

					<p class="banner__info__profession">Эксперт по налоговому праву</p>
					<p class="banner__info__description">Налоговый адвокат. Эксперт по минимизации рисков и
						последствий для руководителей малого и среднего бизнеса при состоявшемся конфликте с правоохранительными и
						налоговыми органами.</p>
					<p class="banner__info__description">Эксперт по защите ИП и юридических лиц в спорах с
						правоохранительными и налоговыми органами по вопросам налогообложения. </p>
				</div>
			</div>

			<div class="col-md-5">
				<div class="banner__img">
					<img class="obj-fit-cover" src="/img/homepage/Portrait2.png" alt="Портрет">
				</div>
			</div>
		</div>
	</div>
</section>
<section class="section services">
	<div class="container-fluid">
		<a href="/services" class="arrow-container align-items-end">
			<h2 class="titles">Что мы можем вам предложить</h2>
			<div class="arrow"><? require ROOT . "/img/homepage/arrow.svg" ?></div>
		</a>


		<a href="/services" class="services__btn">Для физических лиц:</a>


		<!-- <div class="row">
			<div class="col-12 col-lg-8">
				<div class="services__itemsBig">
					<img class="services__itemsBig__img" src="/img/homepage/casesIcons/icon1.svg" alt="">
					<div class="services__itemsBig__title">Банкротство физических лиц</div>
					<p class="services__itemsBig__description">- В судах ( гражданские споры, административные, арбитражные и
						уголовные дела)</p>
					<p class="services__itemsBig__description">- В налоговых органах</p>
					<p class="services__itemsBig__description">- В службе судебных приставов</p>
				</div>

				<div class="row">
					<div class="col-12 col-lg-6">
						<div class="services__items">
							<img class="services__items__img" src="/img/homepage/casesIcons/icon2.svg" alt="">
							<div class="services__items__title">Защита интересов в спорах&nbsp;с&nbsp;организациями ресурсоснабжения
							</div>
						</div>
					</div>

					<div class="col-12 col-lg-6">
						<div class="services__items">
							<img class="services__items__img" src="/img/homepage/casesIcons/icon3.svg" alt="">
							<div class="services__items__title">Медиация</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-12 col-lg-4">
				<div class="services__items">
					<img class="services__items__img" src="/img/homepage/casesIcons/icon4.svg" alt="">
					<div class="services__items__title">Составление жалоб, претензий, исков</div>
				</div>
				<div class="services__items">
					<img class="services__items__img" src="/img/homepage/casesIcons/icon5.svg" alt="">
					<div class="services__items__title">Правовая экспертиза договоров и документов</div>
				</div>
				<div class="services__items">
					<img class="services__items__img" src="/img/homepage/casesIcons/icon6.svg" alt="">
					<div class="services__items__title">Правовая поддержка начинающих предпринимателей</div>
				</div>
				<div class="services__items">
					<img class="services__items__img" src="/img/homepage/casesIcons/icon7.svg" alt="">
					<div class="services__items__title">Банкротство</div>
				</div>
			</div>
		</div> -->
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


		<!-- <div class="row">
			<div class="col-12 col-lg-8">
				<div class="services__itemsBigJuridical col-8">
					<img class="services__itemsBigJuridical__img" src="/img/homepage/casesIcons/icon8.svg" alt="">
					<div class="services__itemsBigJuridical__title">Налоговые споры</div>
					<p class="services__itemsBigJuridical__description">- Сопровождение при налоговых проверках</p>
					<p class="services__itemsBigJuridical__description">- Обжалование действий, решений налоговых органов</p>
					<p class="services__itemsBigJuridical__description">- Снижение налоговых доначислений</p>
				</div>
			</div>
			<div class="col-12 col-lg-4">
				<div class="services__itemsBigJuridical">
					<img class="services__itemsBigJuridical__img" src="/img/homepage/casesIcons/icon9.svg" alt="">
					<div class="services__itemsBigJuridical__title">Арбитраж</div>
					<p class="services__itemsBigJuridical__description">- Хозяйственные споры</p>
				</div>
			</div>
		</div> -->
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

<section class="section cases">
	<div class="container-fluid">
		<a href="/cases" class="arrow-container align-items-end">
			<h2 class="titles">Ознакомьтесь с нашими кейсами</h2>
			<div class="arrow"><? require ROOT . "/img/homepage/arrow.svg" ?></div>
		</a>

		<div class="cases-slider">
			<div class="specialists-slider__items">
				<div class="case d-flex flex-column h-100">
					<img class="cases__container__img" src="/img/homepage/case1.png" alt="">

					<p class="cases__container__category">Банкротство физических лиц</p>

					<h3 class="cases__container__title">Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>

					<p class="cases__container__description sp-line-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua ad minim veniam</p>

					<div class="cases__container__specialist d-flex align-items-center mt-auto">
						<img class="cases__container__specialist__icon" src="/img/icons/people1.png" alt="">
						<h5 class="cases__container__specialist__name">Владимир Кошкин</h5>

						<div class="cases__container__specialist__btn-container ms-auto">
							<a href="#" class="btn btn-blue cases__container__specialist__btn-container__btn">
								<p class="mb-0 d-none d-xl-block">Ознакомиться</p>
								<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="specialists-slider__items">
				<div class="case d-flex flex-column h-100">
					<img class="cases__container__img" src="/img/homepage/case2.png" alt="">
					<p class="cases__container__category">Раздел имущества</p>
					<h3 class="cases__container__title">Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
					<p class="cases__container__description sp-line-3">magna aliqua ad minim veniam</p>
					<div class="cases__container__specialist d-flex align-items-center mt-auto">
						<img class="cases__container__specialist__icon" src="/img/icons/people2.png" alt="">
						<h5 class="cases__container__specialist__name">Мария Рогозова</h5>
						<div class="cases__container__specialist__btn-container ms-auto">
							<a href="#" class=" btn btn-blue cases__container__specialist__btn-container__btn">
								<p class="mb-0 d-none d-xl-block">Ознакомиться</p>
								<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="specialists-slider__items">
				<div class="case d-flex flex-column h-100">
					<img class="cases__container__img" src="/img/homepage/case3.png" alt="">
					<p class="cases__container__category">Ликвидация предприятия</p>
					<h3 class="cases__container__title">Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
					<p class="cases__container__description sp-line-3">Loremdo
						eiusmod tempor incididunt ut labore et dolore magna aliqua ad minim veniam</p>
					<div class="cases__container__specialist d-flex align-items-center mt-auto">
						<img class="cases__container__specialist__icon" src="/img/icons/people3.png" alt="">
						<h5 class="cases__container__specialist__name">Дарья Морозова</h5>
						<div class="cases__container__specialist__btn-container ms-auto">
							<a href="#" class=" btn btn-blue cases__container__specialist__btn-container__btn">
								<p class="mb-0 d-none d-xl-block">Ознакомиться</p>
								<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</section>

<section class="section reviews">
	<div class="container-fluid">
		<a href="/reviews" class="arrow-container align-items-end">
			<h2 class="titles">Мнение наших клиентов</h2>
			<!--<div class="arrow"><? require ROOT . "/img/homepage/arrow.svg" ?></div>
			-->
		</a>

		<div class="row review-slider">
			<? for ($i = 0; $i < 10; $i++): ?>
				<div class="col-md-5">
					<div class="reviews__container">
						<h5 class="reviews__container__name">Леонид</h5>
						<div class="reviews__container__text">
							<p class="reviews__container__review">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
								tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
								ullamco laboris nisi ut aliquip ex ea commodo Lorem, ipsum dolor sit amet consectetur adipisicing elit. Praesentium, deserunt iste reprehenderit eveniet perspiciatis magnam molestiae, nulla similique sit unde dolorum ullam? Deserunt beatae, sit earum cumque adipisci neque? Non possimus illum blanditiis tempore. </p>
							<span class="link-blue">Читать полностью</span>
						</div>
						<img class="reviews__container__icon" src="/img/icons/reviews1.svg" alt="">
					</div>
				</div>
			<? endfor; ?>
		</div>
	</div>
</section>

<section class="section specialists">
	<div class="container-fluid">
		<a href="/team" class="arrow-container align-items-end">
			<h2 class="titles">Для вас работают</h2>
			<div class="arrow"><? require ROOT . "/img/homepage/arrow.svg" ?></div>
		</a>

		<div class="specialists-slider">
			<div class="specialists-slider___items">
				<div class="specialists__container">
					<img class="specialists__container__img" src="/img/homepage/specialists1.png" alt="">
					<h3 class="specialists__container__name">Владимир Кошкин</h3>
					<p class="specialists__container__post">Должность</p>
					<div class="specialists__container__btn-container">
						<a href="#" class=" btn btn-blue specialists__container__btn-container__btn">
							<p class="mb-0">К кейсам</p>
							<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
						</a>
					</div>
				</div>
			</div>
			<div class="specialists-slider___items">
				<div class="specialists__container">
					<img class="specialists__container__img" src="/img/homepage/specialists2.png" alt="">
					<h3 class="specialists__container__name">Мария Рогозова</h3>
					<p class="specialists__container__post">Должность</p>
					<div class="specialists__container__btn-container">
						<a href="#" class="btn btn-blue  specialists__container__btn-container__btn">
							<p class="mb-0">К кейсам</p>
							<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
						</a>
					</div>
				</div>
			</div>
			<div class="specialists-slider___items">
				<div class="specialists__container">
					<img class="specialists__container__img"
						src="/img/homepage/specialists3.png" alt="">
					<h3 class="specialists__container__name">Дарья Морозова</h3>
					<p class="specialists__container__post">Должность</p>
					<div class="specialists__container__btn-container">
						<a href="#" class="btn btn-blue  specialists__container__btn-container__btn">
							<p class="mb-0">К кейсам</p>
							<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
						</a>
					</div>
				</div>
			</div>
			<div class="specialists-slider___items">
				<div class="specialists__container">
					<img class="specialists__container__img" src="/img/homepage/specialists1.png" alt="">
					<h3 class="specialists__container__name">Владимир Кошкин</h3>
					<p class="specialists__container__post">Должность</p>
					<div class="specialists__container__btn-container">
						<a href="#" class="btn btn-blue  specialists__container__btn-container__btn">
							<p class="mb-0">К кейсам</p>
							<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
						</a>
					</div>
				</div>
			</div>
			<div class="specialists-slider___items">
				<div class="specialists__container">
					<img class="specialists__container__img" src="/img/homepage/specialists2.png" alt="">
					<h3 class="specialists__container__name">Мария Рогозова</h3>
					<p class="specialists__container__post">Должность</p>
					<div class="specialists__container__btn-container">
						<a href="#" class="btn btn-blue  specialists__container__btn-container__btn">
							<p class="mb-0">К кейсам</p>
							<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
						</a>
					</div>
				</div>
			</div>
			<div class="specialists-slider___items">
				<div class="specialists__container">
					<img class="specialists__container__img"
						src="/img/homepage/specialists3.png" alt="">
					<h3 class="specialists__container__name">Дарья Морозова</h3>
					<p class="specialists__container__post">Должность</p>
					<div class="specialists__container__btn-container d-flex">
						<a href="#" class="btn btn-blue  specialists__container__btn-container__btn">
							<p class="mb-0">К кейсам</p>
							<div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<? require_once dirname(dirname(__FILE__)) . '/include/consultation.php'; ?>

<? require_once dirname(dirname(__FILE__)) . '/include/contacts.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<script type="text/javascript" asynс src="<?= TEMPLATE . '/assets/js/slick.min.js?1' ?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.review-slider').slick({
			infinite: true,
			dots: true,
			arrows: false,
			slidesToShow: 2,
			slidesToScroll: 2,
			responsive: [{
				breakpoint: 991,
				settings: {
					dots: false,
					slidesToShow: 1,
					slidesToScroll: 1
				}
			}]
		});

		$('.specialists-slider').slick({
			infinite: true,
			arrows: false,
			slidesToShow: 3,
			slidesToScroll: 3,
			dots: true,
			responsive: [{
					breakpoint: 991,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: 769,
					settings: {
						dots: false,
						slidesToShow: 1,
						slidesToScroll: 1
					}
				}
			]
		});
	});
	$(document).ready(function() {
		$('.cases-slider').slick({
			infinite: true,
			arrows: false,
			slidesToShow: 3,
			slidesToScroll: 3,
			dots: true,
			responsive: [{
					breakpoint: 1200,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				},
				{
					breakpoint: 769,
					settings: {
						dots: false,
						slidesToShow: 1,
						slidesToScroll: 1
					}
				}
			]
		});
	});
</script>
<script>
	$(document).ready(function() {
		$(".reviews__container__review").each(function() {
			if ($(this).height() > 153) {
				$(this).addClass("long");
				$(this).next(".link-blue").removeClass("d-none");
			}
		});
		$(".link-blue").click(function() {
			$(this).addClass("d-none");
			$(this).prev(".reviews__container__review").removeClass("long");
		});
	});
</script>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>