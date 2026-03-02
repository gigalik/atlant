<div class="offcanvas offcanvas-top offcanvas-menu" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
	<header class="offcanvas__header">
		<div class="offcanvas-header">
			<a href="<?= SITE_URL ?>"><img src="/img/logo.svg" class="logo-main" alt="<?= $results['information']->title ?>"></a>
			<button type="button" class="btn-close ms-auto" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		</div>
		<div class="offcanvas-body bg-white">
			<ul class="header__nav-canvas">
				<li class="nav-items"><a href="/about">О компании</a></li>
				<li class="nav-items"><a href="/services">Услуги</a></li>
				<li class="nav-items"><a href="/cases">Кейсы</a></li>
				<li class="nav-items"><a href="/contacts">Контакты</a></li>
			</ul>
			<div class="button-container"><a href="#" class="button">Записаться</a></div>
		</div>
	</header>
</div>