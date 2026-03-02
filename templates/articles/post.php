<? 
	require_once dirname(dirname(__FILE__)) . '/include/header.php';
	Breadcrumb::add(SITE_URL, 'Главная');
	Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['controller_info']->title ?: ucfirst(str_replace('_', ' ', $GLOBALS['router']->getController())));
	if($results['category']) Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController() . '/category/' . $results['category']->sef, $results['category']->title);
	Breadcrumb::add('#', $results['article']->title);
	echo Breadcrumb::out();
?>

<section class="section">
	<div class="container-fluid">
		<h1 class="section__title ps-0"><?= $results['article']->title ?></h1>

		<div class="row">
			<div class="col-md-6">
				<div class="content__img about-block__img sticky-top" itemscope="" itemtype="http://schema.org/ImageObject">
					<meta itemprop="name" content="<?= $results['article']->title ?>">
					<meta itemprop="contentUrl" content="<?= json_decode($results['sevice']->img)->fullsize ?>">
					<figure class="mb-0">
						<picture>
							<img class="prw lazy" src="<?= json_decode($results['article']->icon)->svg ?>" data-src="<?= $results['article']->img ? json_decode($results['article']->img)->fullsize : json_decode($results['article']->icon)->svg ?>" alt="<?= $results['article']->title ?>">
						</picture>
					</figure>
				</div>
			</div>

			<div class="col-md-6">
				<div class="about-block__content">
					<?= $results['article']->content ?>
				</div>
			</div>
		</div>
	</div>
</section>

<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>