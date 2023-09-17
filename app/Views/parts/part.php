<?php echo $this->extend('templates/default') ?>
<?php echo $this->section('content') ?>
<section class="part">
    <div class="popup-img">
        <button type="button"></button>
        <div class="popup-img__img1">            
            <img src="<?=$first_img_link_popup ?? ''?>" alt="<?=$first_img_alt ?? ''?>">
        </div>
        <div class="popup-img__img2">            
            <img src="<?=$sec_img_link_popup ?? ''?>" alt="<?=$sec_img_alt ?? ''?>">
        </div>
    </div>    
    <div class="popup-img-overlay"></div>
    <?= $this->include('items/smart_search_result') ?>
    <div class="smart-search-overlay"></div>    
    <div class="container">    
        <?= $this->include('items/breadcrumbs') ?>
        <div class="row">
            <div class="col-12 mb-4 col-lg-4 d-flex flex-column justify-content-center">
                <?= $this->include('items/smart_search') ?>
            </div>
            <div class="col-12 col-lg-8 d-flex flex-column justify-content-center">
            <a name="start"></a>
                <div itemscope itemtype="https://schema.org/Product" class="part-block">
                    <h1 itemprop="name" class="part-block__title"><?= $part['part_h'] ?></h1>
                    <div itemprop="image" class="part-block__gallery">
                        <?= $first_img_res ?>
                        <?= $sec_img_res ?>
                    </div>
                    <div itemprop="offers" itemscope itemtype="https://schema.org/Offer" class="part-block__price">
                        <?= $price ?>
                    </div>
                    <span itemprop="description" class="part-block__desc"><?= $part['part_desc'] ?></span>
                    <div class="part-block__info">
                        <p>
                            <span>
                                Приобрести данный товар можно по адресу:
                                Минск, ТД Ждановичи, ул Тимирязева 127/4 Радиомаркет, павильон Д21. Время работы с 9-30 до 16-30. Для получения дополнительной информации обращайтесь:
                            </span>
                            <a href="https://t.me/Duosby">@duos_by</a>
                            <a href="viber://chat?number=%2B375255301300" rel="nofollow noopener noreferrer" target="_blank">+375 25 5-301-300(Viber)</a>
                        </p>
                        <p>
                            <span>
                                Все вопросы по ремонту мобильных устройств и установке комплектующих:
                            </span>
                            <a href="tel: +375291169801">+375291169801(viber, telegram)</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $this->endSection() ?>