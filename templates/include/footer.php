	<div class="offcanvas offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="offcanvas">
		<div class="offcanvas-header">
			<div class="container d-flex align-items-center">
				<h5 class="offcanvas-title offcanvas__title d-inline-block text-truncate mb-0" id="offcanvasLabel"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
		</div>
		<div class="offcanvas-body position-relative container"></div>
	</div>

	<script type="text/javascript">
		$(".offcanvas_btn").on("click", function (e) {
			offcanvas($(this).data("prefix"), $(this).data("controller"), $(this).data("action"), $(this).data("header"), $(this).data("params"));
		});

		function offcanvas(prefix = null, controller, action, header, params) {
			$("#offcanvas .offcanvas-title").empty().html(header);
			$.get(prefix + controller + "/" + action + "?" + $.param(params), function (data) {
				$("#offcanvas .offcanvas-body").html(data);

				document.getElementById("offcanvas").addEventListener("show.bs.offcanvas", async (event) => {
					// $("#offcanvas .offcanvas-body .select2").select2({
					// 	theme: "bootstrap-5",
					// 	width: "auto",
					// 	dropdownParent: document.querySelector(".offcanvas-body form") != null ? ".offcanvas-body form" : null,
					// });
				});
			});
		}

		document.getElementById("offcanvas")?.addEventListener("hidden.bs.offcanvas", (event) => {
			$("#offcanvas .offcanvas-title").empty();
			$("#offcanvas .offcanvas-body").empty();
		});
	</script>

	<script type="text/javascript">$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});</script>
	<? foreach (App::get_scripts('bottom') as $script) echo $script->code; ?>

	<? if ($_COOKIE['policy'] != 'yes'): ?>
		<div class="toast bg-white border-0 fixed-bottom show mb-2 mx-2 cookie_policy" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 10px;">
			<div class="toast-body p-3">
				<small class="d-block mb-2"><?= Langs::get('messages', 'cookie') ?></small>

				<div class="d-flex align-items-center justify-content-between mt-2 pt-2">
					<a class="btn btn-sm border-0 me-3 fs-6 lh-1 px-3 py-2" href="/docs/view/cookie" target="_blank"><?= Langs::get('btns', 'More') ?></a>
					<button type="button" class="btn btn-sm btn-main close me-3 fs-6 lh-1 px-3 py-2" data-bs-dismiss="toast"><?= Langs::get('btns', 'Agree') ?></button>
				</div>
			</div>
		</div>
		<script type="text/javascript">$(document).ready(function() {$('.cookie_policy .close, .cookie_policy .alert').on('click', function() {$.post('/app/cookie', {title: 'policy',val: 'yes'})});});</script>
	<? endif; ?>

	<div class="toasts-place" style="position: absolute;top: 1rem;right: 1rem;z-index: 2000;">
		<?
		if (!empty($_SESSION['notify'])) :
			foreach ($_SESSION['notify'] as $toast) :
		?>
				<div class="toast bg-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
					<div class="toast-header text-light bg-<?= $toast['type'] ?: 'dark' ?> border-0">
						<strong class="me-auto"><?= $toast['title'] ?></strong>
						<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
					</div>
					<div class="toast-body"><?= $toast['text'] ?></div>
				</div>
			<? endforeach; ?>

			<script type="text/javascript">$(document).ready(function() {$('.toast').toast({'delay': 5000}).toast('show');});</script>
		<?
			$_SESSION['notify'] = null;
		endif;
		?>
	</div>
</body>
</html>