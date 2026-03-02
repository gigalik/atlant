<? require_once dirname(dirname(__FILE__)) . '/include/header.php'; ?>

<section class="auth">
	<div class="container">
		<div class="auth__card">
			<div class="auth__card__header d-flex justify-content-center gap-2 align-items-center">
				<h1 class="auth__card__title mb-0"><?= Langs::get('titles', 'Restore password') ?></h1>
			</div>

			<ul class="nav nav-tabs" id="authTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tel-tab" type="button" role="tab" aria-controls="tel" aria-selected="true"><?= Langs::get('titles', 'phone') ?></button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" data-bs-toggle="tab" data-bs-target="#email-tab" type="button" role="tab" aria-controls="email" aria-selected="false"><?= Langs::get('titles', 'email') ?></button>
				</li>
			</ul>

			<div class="tab-content" id="authTabContent">
				<div class="tab-pane show active" id="tel-tab" role="tabpanel" aria-labelledby="tel-tab">
					<form class="form" action="/auth/restore" method="post">
						<input type="hidden" class="token" name="token" value="">

						<div class="form-group auth__card__form-group">
							<input class="form-control auth__card__form-control" name="user[phone]" type="tel" required placeholder="<?= Langs::get('placeholders', 'Enter phone number') ?>">
						</div>
						<div class="auth__card__btns">
							<button class="btn btn-main btn-md" name="restore" type="submit"><?= Langs::get('btns', 'Restore') ?></button>
						</div>
					</form>
				</div>

				<div class="tab-pane" id="email-tab" role="tabpanel" aria-labelledby="email-tab">
					<form class="form" action="" method="post">
						<input type="hidden" class="token" name="token" value="">
						<input type="hidden" name="redirect" value="">

						<div class="form-group auth__card__form-group">
							<input class="form-control auth__card__form-control" name="user[email]" type="email" required placeholder="<?= Langs::get('placeholders', 'Enter email') ?>">
						</div>

						<div class="auth__card__btns">
							<button class="btn btn-main btn-md" name="restore" type="submit"><?= Langs::get('btns', 'Restore') ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="auth__card__footer">
			<p class="mb-0"><?= Langs::get( 'descriptions', 'I remembered my password') ?></p>
			<a class="auth__card__link" href="/auth"><?= Langs::get('titles', 'Sign in') ?></a>
		</div>
	</div>
</section>

<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<script type="text/javascript" src="<?= TEMPLATE . '/assets/js/phone-mask.min.js' ?>"></script>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>