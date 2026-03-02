<? require_once dirname(dirname(__FILE__)) . '/include/header.php'; ?>

<section class="auth">
	<div class="container">
		<div class="auth__card">
			<div class="auth__card__header d-flex justify-content-center gap-2 align-items-center">
				<h1 class="auth__card__title mb-0"><?= Langs::get('titles', 'Sign in') ?></h1>
			</div>

			<form class="form" action="/auth" method="post">
				<input type="hidden" class="token" name="token" value="">
				<input type="hidden" name="redirect" value="">

				<div class="form-group auth__card__form-group">
					<input class="form-control auth__card__form-control" name="user[email]" type="email" required placeholder="<?= Langs::get('placeholders', 'Enter email') ?>">
					<input class="form-control auth__card__form-control" name="user[password]" type="password" required placeholder="<?= Langs::get('placeholders', str: 'Enter password') ?>">
				</div>

				<div class="auth__card__btns">
					<button class="btn btn-main btn-md" name="login" type="submit"><?= Langs::get('titles', str: 'Sign in') ?></button>
					<a class="btn btn-md ms-auto" href="/auth/signup"><?= Langs::get('titles', str: 'Sign up') ?></a>
				</div>
			</form>
		</div>

		<div class="auth__card__footer">
			<p class="mb-0"><?= Langs::get('descriptions', str: 'I forgot my password') ?></p>
			<a class="auth__card__link" href="/auth/restore"><?= Langs::get('btns', str: 'Restore') ?></a>
		</div>
	</div>
</section>

<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<?/* <script type="text/javascript" src="<?= TEMPLATE . '/assets/js/phone-mask.min.js' ?>"></script>*/?>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>