<? require_once dirname(dirname(__FILE__)) . '/include/header.php'; ?>

<section class="auth">
	<div class="container">
		<div class="auth__card">
			<div class="auth__card__header d-flex justify-content-center gap-2 align-items-center">
				<h1 class="auth__card__title mb-0"><?= Langs::get('titles', 'Sign up') ?></h1>
			</div>

			<form class="form" action="" method="post">
				<input type="hidden" class="token" name="token" value="">
				<input type="hidden" name="redirect" value="">

				<div class="form-group auth__card__form-group">
					<input class="form-control auth__card__form-control" name="user[email]" type="email" required placeholder="<?= Langs::get('placeholders', 'Enter email') ?>">
				</div>
				<div class="form-group auth__card__form-group mt-3">
					<input class="form-control auth__card__form-control" name="user[promocode]" type="text" placeholder="<?= Langs::get('titles', 'Promocode') ?>" value="<?= $_GET['promocode'] ? trim(strip_tags($_GET['promocode'])) : '' ?>">
				</div>

				<div class="form-group auth__card__form-group mt-3">
					<small class="text-muted">Нажимая на кнопку "<?= Langs::get('btns', 'Create') ?>", вы соглашаетесь на <a class="text-light-blue" href="/docs/view/privacy">обработку персональных данных</a> в соответствии с политикой нашего сайта</small>
				</div>

				<div class="auth__card__btns">
					<button class="btn btn-main btn-md" name="registration" type="submit"><?= Langs::get('btns', 'Create') ?></button>
				</div>
			</form>
		</div>

		<div class="auth__card__footer">
			<p class="mb-0"><?= Langs::get('descriptions', 'I already have an account') ?></p>
			<a class="auth__card__link" href="/auth"><?= Langs::get('titles', 'Sign in') ?></a>
		</div>
	</div>
</section>

<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<?/* <script type="text/javascript" src="<?= TEMPLATE . '/assets/js/phone-mask.min.js' ?>"></script>*/?>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>