<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $meta_robots ?>
    <meta name="yandex-verification" content="151268cb7a96b9d6" />
    <link rel="canonical" href="<?= $link_can ?? '' ?>" />
    <meta name="description" content="<?= $description ?? '' ?>" />    
	<meta property="og:title" content="DuoS.by - магазин комплектующих для мобильных устройств">
	<meta property="og:description" content="Продажа деталей и комплектующих для различных моделей мобильных телефонов и планшетов">
	<meta property="og:type" content="article">
	<meta property="og:image" content="<?= base_url('assets/img/description.jpg') ?>">
	<meta property="og:site_name" content="DuoS.by">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <title><?= $title ?? '' ?></title>    
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">       
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 mb-3 col-md-6 mb-md-2 col-xl-3">
                    <a class="header-logo" href="<?= site_url() ?>" title="Duos.by - магазин комплектующих для мобильных устройств: главная страница">                        
                    </a>
                </div>               
                <div class="col-12 mb-3 col-md-6 col-xl-4">
                    <div class="new-parts">
                        <h3 class="new-parts__title">
                            Новые поступления
                        </h3>
                        <div class="header-slider">
                            <?php foreach ($parts_slider as $item) : ?>
                                <a class="header-slider__item" href="<?= site_url("parts/{$item['part_slug']}#start") ?>" title="<?= $item['title_for_alt'] . '- смотреть подробнее' ?>">
                                    <div class="header-slider__offer">
                                        <span class="header-slider__title">
                                            <?= $item['part_h'] ?>
                                        </span>
                                        <?= $item['part_desc_slider'] ?>
                                    </div>
                                    <div class="header-slider__img">
                                        <img src="<?= base_url("assets/img/parts/{$item['part_slug']}/thumbnails/{$item['part_img']}.webp") ?>" alt="<?=$item['title_for_alt']?>">
                                    </div>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-2 col-md-6 col-xl-2">
                    <div class="header-loc">
                        <div class="header-loc__photo">
                            <img src="<?= base_url('assets/img/duos-plane.webp') ?>" alt="Магазин Duos.by фото">
                        </div>                      
                    </div>
                </div>
                <div class="col-12 mb-3 col-md-6 mb-md-2 col-xl-3">
                    <div class="header-contacts">
                        <span class="header-contact">г.Минск, ТЦ "Ждановичи"</span>
                        <span class="header-contact">ул. Тимирязева - 127/4</span>
                        <span class="header-contact">Радиомаркет, пав. № D21</span>
                        <span class="header-contact">С 9.30 до 16.30</span>
                        <span class="header-contact">Выходной - понедельник</span>
                        <div class="header-contact phones">
                           <a href="https://t.me/Duosby" rel="nofollow noopener noreferrer" target="_blank">+375 25 5-301-300</a>
                           <a href="viber://chat?number=%2B375255301300" rel="nofollow noopener noreferrer" target="_blank">+375 25 5-301-300</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-nav">
                <ul class="header-nav__list">
                    <button class="header-nav__btn btn-close"></button>
                    <li class="header-nav__item">
                        <a href="<?= site_url() ?>">Главная</a>
                    </li>
                    <li class="header-nav__item">
                        <a href="<?= site_url('about#start') ?>">О нас</a>
                    </li>                    
                </ul>
                <div class="header-nav__burger show-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="header-social">
                    <span>Бот - сотрудник</span>
                    <a href="https://t.me/DuosSmart_bot" rel="nofollow noopener noreferrer" target="_blank">@DuosSmart_bot</a>
                    <a href="https://t.me/DuosPart_bot" rel="nofollow noopener noreferrer" target="_blank">@DuosPart_bot<span> (старая версия)</span></a>
                </div>
            </div>
        </div>
    </header>
    <?php $this->renderSection('content'); ?>
    <footer class="footer">
        <div class="container">
            <div class="footer-offer">
                <div class="footer-contacts">
                    <span class="footer-contact">г.Минск, ТЦ "Ждановичи"</span>
                    <span class="footer-contact">ул. Тимирязева - 127/4</span>
                    <span class="footer-contact">Радиомаркет, пав. № D21</span>
                    <span class="footer-contact">С 9.30 до 16.30</span>
                    <span class="footer-contact">Выходной - понедельник</span>
                    <div class="footer-contact phones">
                        <a href="https://t.me/Duosby" rel="nofollow noopener noreferrer" target="_blank">+375 25 5-301-300</a>
                        <a href="viber://chat?number=%2B375255301300" rel="nofollow noopener noreferrer" target="_blank">+375 25 5-301-300</a>                        
                    </div>
                </div>
                <span class="hr"></span>
                <div class="footer-copyright">
                <span>&copy;Duos.by</span>
                <a href="https://mikalay.tech" rel="nofollow noopener noreferrer" target="_blank">Проект разработан https://mikalay.tech</a>
                </div>                                    
            </div>                            
        </div>
    </footer>
    <script src="<?= base_url('assets/js/jquery-3.6.4.min.js') ?>" defer></script>
    <script src="<?= base_url('assets/js/slick.min.js') ?>" defer></script>
    <script src="<?= base_url('assets/js/slider.js') ?> " defer></script>   
    <script src="<?= base_url('assets/js/main.js') ?>" defer></script>
    
</body>

</html>