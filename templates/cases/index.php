<? require_once dirname(dirname(__FILE__)) . '/include/header.php';

Breadcrumb::add(SITE_URL, 'Главная');
Breadcrumb::add(SITE_URL . DS . $GLOBALS['router']->getController(), $results['controller_info']->title);
Breadcrumb::out();
?>
<section class="section casesMain">
    <div class="container-fluid">
        <h2 class="servicesMain__titles">Кейсы</h2>

        <div class="row">
            <div class="col-md-6 col-lg-4">
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

            <div class="col-md-6 col-lg-4">
                <div class="case d-flex flex-column h-100">
                    <img class="cases__container__img" src="/img/homepage/case2.png" alt="">
                    <p class="cases__container__category">Раздел имущества</p>
                    <h3 class="cases__container__title">Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
                    <p class="cases__container__description sp-line-3">magna aliqua ad minim veniam</p>
                    <div class="cases__container__specialist d-flex align-items-center mt-auto">
                        <img class="cases__container__specialist__icon" src="/img/icons/people2.png" alt="">
                        <h5 class="cases__container__specialist__name">Мария Рогозова</h5>
                        <div class="cases__container__specialist__btn-container ms-auto">
                            <a href="#" class="btn btn-blue cases__container__specialist__btn-container__btn">
                                <p class="mb-0 d-none d-xl-block">Ознакомиться</p>
                                <div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
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
                            <a href="#" class="btn btn-blue cases__container__specialist__btn-container__btn">
                                <p class="mb-0 d-none d-xl-block">Ознакомиться</p>
                                <div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
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

            <div class="col-md-6 col-lg-4">
                <div class="case d-flex flex-column h-100">
                    <img class="cases__container__img" src="/img/homepage/case2.png" alt="">
                    <p class="cases__container__category">Раздел имущества</p>
                    <h3 class="cases__container__title">Lorem ipsum dolor sit amet, consectetur adipiscing elit</h3>
                    <p class="cases__container__description sp-line-3">magna aliqua ad minim veniam</p>
                    <div class="cases__container__specialist d-flex align-items-center mt-auto">
                        <img class="cases__container__specialist__icon" src="/img/icons/people2.png" alt="">
                        <h5 class="cases__container__specialist__name">Мария Рогозова</h5>
                        <div class="cases__container__specialist__btn-container ms-auto">
                            <a href="#" class="btn btn-blue cases__container__specialist__btn-container__btn">
                                <p class="mb-0 d-none d-xl-block">Ознакомиться</p>
                                <div class="arrow-mini"><? require ROOT . "/img/homepage/arrow-mini.svg" ?></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
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
                            <a href="#" class="btn btn-blue cases__container__specialist__btn-container__btn">
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
<section class="icons">
    <div class="icons__fixed-icons">
        <a href="tel:89059118422" class="icons__fixed-icons__call">
            <img src="/img/icons/call.svg" class="call-icon" alt="">
        </a>
        <a href="#" class="icons__fixed-icons__scroll scroll-to-top" id="scroll-to-top">
            <img src="/img/icons/scroll.svg" class="scroll-icon" alt="">
        </a>
    </div>
</section>
<? require_once dirname(dirname(__FILE__)) . '/include/callback.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/pre_footer.php'; ?>
<? require_once dirname(dirname(__FILE__)) . '/include/footer.php'; ?>