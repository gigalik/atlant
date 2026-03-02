<section class="section request">
	<div class="container-fluid">
		<div class="request__form">
			<h3 class="request__form__title">Получить консультацию</h3>
			<p class="request__form__description">Оставьте нам свои контакты и мы свяжемся с вами для уточнения деталей</p>

			<form action="/requests/add" method="post">
				<div class="row mb-4">
					<div class="col-md-4">
						<input class="form-control request__form__data" type="text" placeholder="Имя">
					</div>
					<div class="col-md-4">
						<input class="form-control request__form__data" type="phone" placeholder="Телефон">
					</div>
					<div class="col-md-4">
						<input class="form-control request__form__data" type="email" placeholder="Email">
					</div>
				</div>

				<textarea class="form-control request__form__message" name="" id="" placeholder="Ваше сообщение"></textarea>

				<div class="request__form__footer">
					<button class="btn btn-border request__form__btn mb-4">Связаться</button>
					<p class="request__form__info">Нажимая на кнопку Связаться вы соглашаетесь с <a href="" class="request__form__info__link">Политикой конфиденциальности</a> сайта</p>
				</div>
			</form>
		</div>
	</div>
</section>