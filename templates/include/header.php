<!DOCTYPE html>
<html lang="ru" class="h-100">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="keywords" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['keywords']) ?>">
	<meta name="description" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['description']) ?>">
	<meta name="author" content="<?= $results['information']->title ?>">
	<meta name="csrf-token" content="<?= csrf_token() ?>">
	<meta property="og:locale" content="<?= $_COOKIE['lang'] ?: DEFAULT_LANG ?>" />
	<meta property="og:title" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['title']) ?>">
	<meta property="og:site_name" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['title']) ?>">
	<meta property="og:url" content="<?= SITE_URL . $_SERVER["REQUEST_URI"]; ?>">
	<meta property="og:description" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['description']) ?>">
	<meta property="og:type" content="website">
	<meta property="og:image" content="<?= $results['seo']['img'] ?>" />
	<meta property="og:image:secure_url" content="<?= $results['seo']['img'] . '?' . strtotime(date('ymd')) ?>" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="400" />
	<meta property="og:image:height" content="300" />
	<meta name="twitter:title" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['title']) ?>" />
	<meta name="twitter:description" content="<?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['description']) ?>" />
	<meta name="twitter:site" content="<?= $_SERVER["REQUEST_URI"]; ?>" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:image" content="<?= $results['seo']['img'] . '?' . strtotime(date('Y-m-d')) ?>" />
	<meta name="apple-mobile-web-app-title" content="<?= $results['information']->title ?: $results['seo']['title'] ?>">
	<meta name="application-name" content="<?= $results['information']->title ?: $results['seo']['title'] ?>">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/img/favicons/mstile-144x144.png">
	<meta name="msapplication-config" content="/img/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	<meta name="robots" content="<?= ($results['seo']['robots']['i'] ?: 'noindex') . ',' . ($results['seo']['robots']['f'] ?: 'nofollow') ?>">
	<link rel="icon" type="image/png" href="/img/favicons/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/img/favicons/favicon.svg" />
	<link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png" />
	<link rel="manifest" href="/application/manifest.json">
	<link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#ffffff">
	<link rel="shortcut icon" href="/favicon.ico?v=2">
	<link rel="icon" type="image/x-icon" href="/favicon.ico?v=2">
	<title><?= preg_replace('/\"([^\"]*)\"/ismU', '&laquo;$1&raquo;', $results['seo']['title']) ?></title>
	<link href="/views/default/assets/css/style.min.css<?= '?v=' . strtotime(date('y-m-d')) ?>" rel="stylesheet" as="style" onload="this.rel='stylesheet'">
	<noscript>
		<link rel="stylesheet" href="/views/default/assets/css/style.min.css<?= '?v=' . strtotime(date('y-m-d')) ?>">
	</noscript>
	<? foreach (App::get_scripts('head') as $script) echo $script->code; ?>
</head>

<body class="d-flex flex-column min-vh-100">
	<header class="header ">
		<div class="container-fluid">
			<div class="d-flex">
				<div class="d-flex logo">
					<a href="<?= SITE_URL ?>"><img src="/img/logo.svg" class="logo-main" alt="<?= $results['information']->title ?>"></a>
				</div>

				<? // if (!App::is_mobile()): 
				?>
				<div class="nav-container d-none d-lg-block">
					<nav class="nav">
						<ul class="d-flex">
							<li class="nav-items"><a href="/about">О компании</a></li>
							<li class="nav-items"><a href="/services">Услуги</a></li>
							<li class="nav-items"><a href="/cases">Кейсы</a></li>
							<li class="nav-items"><a href="/contacts">Контакты</a></li>
						</ul>
						<div class="button-container"><a href="#" class="button">Записаться</a></div>
					</nav>
				</div>
				<? // endif; 
				?>
				<!--</div>-->
				<button class="header__menu-btn d-lg-none ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu"><? require ROOT . "/img/icons/burger.svg"; ?></button>
			</div>
	</header>

	<main class="flex-grow-1" role="main">