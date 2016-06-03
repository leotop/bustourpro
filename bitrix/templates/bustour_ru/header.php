<!DOCTYPE HTML>
<html>
<head>

    <meta charset="UTF-8">


    <?$APPLICATION->ShowMeta("robots")?>
    <?$APPLICATION->ShowMeta("keywords")?>
    <?$APPLICATION->ShowMeta("description")?>
    <title><?$APPLICATION->ShowTitle()?></title>
    <?$APPLICATION->ShowHead();?>
    <?IncludeTemplateLangFile(__FILE__);?>
    <?CJSCore::Init(array("jquery"));?>


    <!--[if IE]><script src="js/html5.js"></script><![endif]-->
    <meta id="Viewport" name="viewport" content="width=device-width, target-densitydpi=device-dpi, user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <!-- favicon -->
    <link href="favicon.ico" rel="shortcut icon">

    <!-- document stylesheet files -->
    <link href='http://fonts.googleapis.com/css?family=Roboto+Slab:700,300&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=PT+Serif:400italic,700italic&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
    <link href="/css/all.css" rel="stylesheet" media="screen">
    <!--[if gte IE 9]><link rel="stylesheet" href="css/ie9.css" media="screen, projection"><![endif]-->

    <!-- jQuery scripts -->
    <script src="/js/jquery-1.8.2.min.js"></script>
    <script src="/js/jquery-ui-1.9.0.custom.min.js"></script>
    <script src="/js/widgets.js"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>
    <script src="/js/functions.js"></script>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<!-- wrapper -->
<div class="wrapper">
    <!-- header -->
    <header class="header">
        <div class="header-holder clearfix">
            <nav class="nav">
                <ul class="nav-list">
                    <li><a href="#top">Контакты</a></li>
                    <li><a href="#capabilities">Возможности</a></li>
                    <li><a href="#whywe">Почему мы</a></li>
                    <li><a href="#cost">Тарифы</a></li>
                    <li><a href="#partners">Партнеры</a></li>
                    <li><a href="#contacts">Контакты</a></li>
                </ul>
            </nav>
            <div class="logo"></div>
        </div>
    </header>
    <!-- header end -->

    <!-- content -->
    <div class="content">

        <!-- top-information -->
        <div class="top-information" id="top">
            <div class="hideOf" data-effect="drop">
                <h1>bustourpro</h1>
                <p>Самый удобный инструмент для управления автобусными турами.</p>
                <ul class="information-list">
                    <li><span><i class="icon-heart"></i></span><em>удобство пользования</em></li>
                    <li><span><i class="icon-fns"></i></span><em>широкий функционал</em></li>
                    <li><span><i class="icon-shield"></i></span><em>надежность 24/7</em></li>
                </ul>
                <a href="#registration" class="btn-orange popup-handler">попробовать бесплатно</a>
            </div>
        </div>
        <!-- top-information -->

        <!-- whywe -->
        <div class="capabilities content-box" id="capabilities">
            <div class="hideOf" data-effect="fade">
                <div class="box-title">
                    <div class="number"><img src="img/nums/01.png" alt=""/></div>
                    <h2>Возможности системы</h2>
                    <p>Bustourpro имеет множество преимуществ для учета и управления автобусными турами.</p>
                    <span class="bd"></span>
                </div>
                <div class="capabilities-content">

                    <div class="tabs-wrap">
                        <div class="tab-controls">
                            <ul class="tabs-list">
                                <li class="active"><a href="">Для туроператоров</a></li>
                                <li><a href="">Для турагенств</a></li>
                            </ul>
                        </div>
                        <div class="tabs">
                            <div class="tab">
                                <ul class="capabilities-list">
                                    <li>
                                        <div class="wrap-bl">
                                            <div class="capabilities-holder">
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img01.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Формирование туров</a></div>
                                                            <p>Самое удобное и быстрое формирование автобусных туров на весь сезон с учетом особенностей автобусных туров.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img02.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Работа с агентствами</a></div>
                                                            <p>Регистрация агентств в системе после одобрения администратором, формирование рейтинга агентств.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img03.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Управление заявками</a></div>
                                                            <p>Автоматический прием заявок от агентств, работа со статусами заказа, автоматическая рассылка агентствам информации о турах.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img04.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">механизм расчета и скидки</a></div>
                                                            <p>Управление любыми механизмами расчета и учет всех возможных скидок без необходимости допрограммировать систему. </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="wrap-bl">
                                            <div class="capabilities-holder">
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img01.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Формирование туров</a></div>
                                                            <p>Самое удобное и быстрое формирование автобусных туров на весь сезон с учетом особенностей автобусных туров.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img02.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Работа с агентствами</a></div>
                                                            <p>Регистрация агентств в системе после одобрения администратором, формирование рейтинга агентств.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img03.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Управление заявками</a></div>
                                                            <p>Автоматический прием заявок от агентств, работа со статусами заказа, автоматическая рассылка агентствам информации о турах.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img04.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">механизм расчета и скидки</a></div>
                                                            <p>Управление любыми механизмами расчета и учет всех возможных скидок без необходимости допрограммировать систему. </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="wrap-bl">
                                            <div class="capabilities-holder">
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img01.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Формирование туров</a></div>
                                                            <p>Самое удобное и быстрое формирование автобусных туров на весь сезон с учетом особенностей автобусных туров.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img02.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Работа с агентствами</a></div>
                                                            <p>Регистрация агентств в системе после одобрения администратором, формирование рейтинга агентств.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img03.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Управление заявками</a></div>
                                                            <p>Автоматический прием заявок от агентств, работа со статусами заказа, автоматическая рассылка агентствам информации о турах.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img04.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">механизм расчета и скидки</a></div>
                                                            <p>Управление любыми механизмами расчета и учет всех возможных скидок без необходимости допрограммировать систему. </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab">
                                <ul class="capabilities-list">
                                    <li>
                                        <div class="wrap-bl">
                                            <div class="capabilities-holder">
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img01.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Формирование туров</a></div>
                                                            <p>Самое удобное и быстрое формирование автобусных туров на весь сезон с учетом особенностей автобусных туров.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img02.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Работа с агентствами</a></div>
                                                            <p>Регистрация агентств в системе после одобрения администратором, формирование рейтинга агентств.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img03.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Управление заявками</a></div>
                                                            <p>Автоматический прием заявок от агентств, работа со статусами заказа, автоматическая рассылка агентствам информации о турах.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img04.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">механизм расчета и скидки</a></div>
                                                            <p>Управление любыми механизмами расчета и учет всех возможных скидок без необходимости допрограммировать систему. </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="wrap-bl">
                                            <div class="capabilities-holder">
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img01.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Формирование туров</a></div>
                                                            <p>Самое удобное и быстрое формирование автобусных туров на весь сезон с учетом особенностей автобусных туров.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img02.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Работа с агентствами</a></div>
                                                            <p>Регистрация агентств в системе после одобрения администратором, формирование рейтинга агентств.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img03.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Управление заявками</a></div>
                                                            <p>Автоматический прием заявок от агентств, работа со статусами заказа, автоматическая рассылка агентствам информации о турах.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img04.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">механизм расчета и скидки</a></div>
                                                            <p>Управление любыми механизмами расчета и учет всех возможных скидок без необходимости допрограммировать систему. </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="wrap-bl">
                                            <div class="capabilities-holder">
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img01.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Формирование туров</a></div>
                                                            <p>Самое удобное и быстрое формирование автобусных туров на весь сезон с учетом особенностей автобусных туров.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img02.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Работа с агентствами</a></div>
                                                            <p>Регистрация агентств в системе после одобрения администратором, формирование рейтинга агентств.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img03.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">Управление заявками</a></div>
                                                            <p>Автоматический прием заявок от агентств, работа со статусами заказа, автоматическая рассылка агентствам информации о турах.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="capabilities-box">
                                                    <div class="block">
                                                        <figure><img src="pic/carusel/img04.png" alt=""/></figure>
                                                        <div class="info">
                                                            <div class="title"><a href="">механизм расчета и скидки</a></div>
                                                            <p>Управление любыми механизмами расчета и учет всех возможных скидок без необходимости допрограммировать систему. </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- whywe end -->

        <!-- presentation -->
        <div class="presentation">
            <div class="hideOf" data-effect="fade">
                <div class="line"></div>
                <div class="bus-00"><i class="icon-bus0"></i></div>
                <div class="presentation-holder">
                    <div class="presentation-box">
                        <figure><img src="img/bg/screen.png" alt=""/></figure>
                        <div class="info">
                            <h3><span>Презентация</span> системы</h3>
                            <p>Посмотреть презентацию системы перед решением ею пользоваться.</p>
                            <a href="" class="btn-tn">скачать pdf <i class="arrow-wh"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- presentation end -->

        <!-- whywe -->
        <div class="whywe content-box" id="whywe">
            <div class="hideOf" data-effect="drop">
                <div class="bg"></div>
                <div class="box-title">
                    <div class="number"><img src="img/nums/02.png" alt=""/></div>
                    <h2>почему bustourpro</h2>
                    <p>Bustourpro имеет множество преимуществ для учета и управления автобусными турами.</p>
                    <span class="bd"></span>
                </div>
                <div class="max-wrap">
                    <ul class="whywe-list">
                        <li>
                            <div class="whywe-box">
                                <figure><img src="pic/why/img01.png" alt=""/></figure>
                                <article>
                                    <div>
                                        <span>01</span>
                                        <h3>Программное обеспечение создавалось при участие экспертов Яндекс и ФРИИ</h3>
                                        <p>А значит, данное программное обеспечение учитывает все подребности современной автобусной туриндустрии.</p>
                                    </div>
                                </article>
                            </div>
                        </li>
                        <li class="right-side">
                            <div class="whywe-box">
                                <figure><img src="pic/why/img02.png" alt=""/></figure>
                                <article>
                                    <div>
                                        <span>02</span>
                                        <h3>В проектировании участвовали  9 туроператоров и 11 внешнех экспертов</h3>
                                        <p>Именно поэтому система очень удобна, ее проектировали туроператоры для себя. Это не то программное обеспечение, которое вас ограничит</p>
                                    </div>
                                </article>
                            </div>
                        </li>
                        <li>
                            <div class="whywe-box">
                                <figure><img src="pic/why/img03.png" alt=""/></figure>
                                <article>
                                    <div>
                                        <span>03</span>
                                        <h3>Программное обеспечение работает на платформе 1С-Битрикс</h3>
                                        <p>Все прелести 1С-Битрикс обеспечивают самую быструю и надежную работу системы</p>
                                    </div>
                                </article>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- whywe end -->

        <!-- cost -->
        <div class="cost" id="cost">
            <div class="hideOf" data-effect="fade">
                <div class="cost-title">
                    <div class="cost-title-holder">
                        <div class="bus-01"><i class="icon-bus"></i></div>
                        <div class="cost-title-box">
                            <div class="title-button"><a href="#registration" class="btn-orange popup-handler">оценить все преимущества</a></div>
                            <div class="title-text">
                                <div class="number"><img src="img/nums/03.png" alt=""/></div>
                                <h2>стоимость системы</h2>
                                <i class="arrow-pp-orange"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="versions">
                    <div class="vbox vclouds">
                        <div class="holder">
                            <figure><img src="img/bg/clouds.png" alt=""/></figure>
                            <div class="info">
                                <h3>Работа в облачном <br/> сервисе</h3>
                                <p>7 туроператоров используют облачную вверсию.</p>
                                <div class="price"><span>10 <span class="icon-rblue"></span> <b>/</b></span> <em>За каждого оформленного <br/> пассажира или тура.</em></div>
                                <a href="" class="btn-tn">подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="vbox vboxed">
                        <div class="holder">
                            <figure><img src="img/bg/box.png" alt=""/></figure>
                            <div class="info">
                                <h3>коробочная версия <br/> системы</h3>
                                <p>14 туроператоров используют облачную вверсию</p>
                                <div class="price">75 000 <span class="icon-rblack"></span></div>
                                <a href="" class="btn-tn">подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- cost end -->

        <!-- articles -->
        <div class="articles content-box" id="partners">
            <div class="hideOf" data-effect="fade">
                <div class="articles-holder">
                    <div class="title"><span>Авторитетные издания пишут о Bustourpro:</span></div>
                    <div class="articles-boxs">
                        <ul class="articles-list">
                            <li><a href=""><img src="pic/articles/img01.png" alt=""/><img src="pic/articles/img01_h.png" alt=""/></a></li>
                            <li><a href=""><img src="pic/articles/img02.png" alt=""/><img src="pic/articles/img02_h.png" alt=""/></a></li>
                            <li><a href=""><img src="pic/articles/img03.png" alt=""/><img src="pic/articles/img03_h.png" alt=""/></a></li>
                            <li><a href=""><img src="pic/articles/img04.png" alt=""/><img src="pic/articles/img04_h.png" alt=""/></a></li>
                        </ul>
                    </div>
                </div>
                <div class="box-title">
                    <div class="number"><img src="img/nums/04.png" alt=""/></div>
                    <h2>партнеры проекта</h2>
                    <p>Bustourpro имеет множество преимуществ для учета и управления автобусными турами.</p>
                    <span class="bd"></span>
                </div>
                <div class="partners">
                    <ul class="partners-list">
                        <li><a href=""><img src="pic/partners/img01.png" alt=""/><img src="pic/partners/img01_h.png" alt=""/></a></li>
                        <li><a href=""><img src="pic/partners/img02.png" alt=""/><img src="pic/partners/img02_h.png" alt=""/></a></li>
                        <li><a href=""><img src="pic/partners/img03.png" alt=""/><img src="pic/partners/img03_h.png" alt=""/></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- articles end -->

        <!-- contacts -->
        <div class="contacts" id="contacts">
            <div class="hideOf" data-effect="drop">
                <div class="bus-02"><i class="icon-bus"></i></div>
                <div class="contact-holder">
                    <div class="max-wrap">
                        <div class="number"><img src="img/nums/05.png" alt=""/></div>
                        <h2>контакты</h2>
                        <div class="contacts-box">
                            <div class="phone">+7 495 <strong>223 55 77</strong></div>
                            <div><p>Адрес: г. Москва, проспект Мира 5, офис 231</p></div>
                            <div><a href="mailto:info@bustourpro.ru">info@bustourpro.ru</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- contacts end -->

        <!-- map -->
        <div class="map-holder">
            <div class="hideOf" data-effect="fade">
                <div id="map" class="map"></div>
            </div>
        </div>
        <!-- map end -->

    </div>
    <!-- content end -->
</div>
<!-- wrapper end -->

<!-- footer -->
<footer class="footer">
    <div class="clearfix">
        <p>Создание сайта – <a href="">компания WebGK</a></p>
        <div class="logos">
            <a href=""><img src="pic/footer-logos/img01.png" alt=""/></a>
            <a href=""><img src="pic/footer-logos/img02.png" alt=""/></a>
        </div>
    </div>
</footer>
<!-- footer end -->


<script>
    //отправка формы и проверка регистрации
    function formSubmit() {
        var email = $("#reg_email").val();
        var login = $("#reg_login").val();
        var password = $("#reg_password").val();
        var confirm_password = $("#reg_confirm_password").val();

        $.post("/ajax/newAccount.php",{login:login, email:email, password:password, confirm_password:confirm_password },
            function(data){
                if(data == "OK") {
                    document.location.href="http://" + login + ".bustourpro.ru";
                }
                else {
                    alert(data);
                }
            }
        )  
    }


</script>

<div id="registration" class="popup registration">
    <div class="popup-inner">
        <h3>регистрация</h3>
        <div class="bd"></div>
        <div class="reg-form">
            <form action="#" method="post" id="registrationForm">

                <div class="form-line"  >
                    <div class="input-wrap">
                        <div class="input-holder" style="width: 300px; float:left"><div><input type="text" placeholder="имя домена (логин)" name="login" id="reg_login"/></div></div>
                        <div style="float: left; height: 50px; line-height: 50px; font-size: 50px; font-weight: bold; text-transform: uppercase; color:#fff">.bustourpro.ru</div>
                    </div>
                </div>       

                <div class="form-line">
                    <div class="input-wrap">
                        <div class="input-holder"><div><input type="text" placeholder="E-mail" name="email" id="reg_email"/></div></div>
                    </div>
                </div>
                <div class="form-line">
                    <div class="input-wrap">
                        <div class="input-holder"><div><input type="password" placeholder="Пароль" name="password" id="reg_password"/></div></div>
                    </div>
                </div>
                <div class="form-line">
                    <div class="input-wrap">
                        <div class="input-holder"><div><input type="password" placeholder="Подтверждение пароля" name="confirm_password" id="reg_confirm_password"/></div></div>
                    </div>
                </div>
                <i class="arrow-pp-point"></i>
                <div class="submit"><div class="btn-tn" onclick="formSubmit();">зарегистрироваться<input type="submit" value="" style="display: none;"/></div></div>
            </form>
        </div>
    </div>
</div>

<div id="thank" class="popup thank">
    <div class="popup-inner">
        <h3>Cпасибо за регистрацию!</h3>
        <p>Дальнейшие инструкции высланы на Вашу почту.</p>
    </div>
</div>

<div class="preloader">
    <div id="loaderImage"></div>
    <script type="text/javascript">
        var cSpeed=7;
        var cWidth=100;
        var cHeight=33;
        var cTotalFrames=12;
        var cFrameWidth=100;
        var cImageSrc='img/preloader.gif';

        var cImageTimeout=false;
        var cIndex=0;
        var cXpos=0;
        var cPreloaderTimeout=false;
        var SECONDS_BETWEEN_FRAMES=0;

        function startAnimation(){
            document.getElementById('loaderImage').style.backgroundImage='url('+cImageSrc+')';
            document.getElementById('loaderImage').style.width=cWidth+'px';
            document.getElementById('loaderImage').style.height=cHeight+'px';
            FPS = Math.round(100/cSpeed);
            SECONDS_BETWEEN_FRAMES = 1 / FPS;
            cPreloaderTimeout=setTimeout('continueAnimation()', SECONDS_BETWEEN_FRAMES/1000);
        }

        function continueAnimation(){
            cXpos += cFrameWidth;
            cIndex += 1;
            if (cIndex >= cTotalFrames) {
                cXpos =0;
                cIndex=0;
            }

            if(document.getElementById('loaderImage'))
                document.getElementById('loaderImage').style.backgroundPosition=(-cXpos)+'px 0';

            cPreloaderTimeout=setTimeout('continueAnimation()', SECONDS_BETWEEN_FRAMES*1000);
        }

        function stopAnimation(){
            clearTimeout(cPreloaderTimeout);
            cPreloaderTimeout=false;
        }

        function imageLoader(s, fun)
        {
            clearTimeout(cImageTimeout);
            cImageTimeout=0;
            genImage = new Image();
            genImage.onload=function (){cImageTimeout=setTimeout(fun, 0)};
            genImage.onerror=new Function('alert(\'Could not load the image\')');
            genImage.src=s;
        }
        new imageLoader(cImageSrc, 'startAnimation()');
    </script>
        </div>