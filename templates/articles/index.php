<? 
	require_once dirname(dirname(__FILE__)) . '/include/header.php'; 

	Breadcrumb::add(SITE_URL, 'Главная');
	Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['controller_info']->title ?: ucfirst(str_replace('_', ' ', $GLOBALS['router']->getController())));

	if($results['category']) Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['category']->title);

	echo Breadcrumb::out();
?>

<section class="section-gray">
	<div class="container-fluid">
		<h2 class="section__title"><?= $results['category']->title ?: $results['controller_info']->title ?></h2>

		<div class="news-detail">
			<? foreach($results['news'] as $post): ?>
				<a class="news-detail__item" href="/articles/post/<?= $post->sef ?>">
					<div class="news__info">
						<div class="news__date"><?= Formatting::date($post->publication_date, 'd.m.Y') ?></div>
						<h3 class="news__title sp-line-3"><?= $post->title ?></h3>
					</div>
					<div class="news__img"><img class="obj-fit-cover lazy" src="<?= $post->img ? Images::get($post, 'thumb') : '/img/no-img.png' ?>" data-src="<?= $post->img ? Images::get($post, 'fullsize') : '/img/no-img.png' ?>" /></div>
				</a>
			<? endforeach; ?>
		</div>

		<? require_once dirname(dirname(__FILE__)) . '/include/callback.php'; ?>
	</div>
</section>
<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>