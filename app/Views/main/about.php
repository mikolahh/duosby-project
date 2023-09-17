<?php echo $this->extend('templates/default') ?>
<?php echo $this->section('content') ?>
<section class="about">
  <a name="start"></a>
  <div class="container">
  <?= $this->include('items/breadcrumbs') ?>
    <div class="row">
      <div class="col-12 mt-2 col-md-6 offset-md-3">
        <h1 class="about-title">О нас</h1>
        <div class="about-info">
          <p>
            Магазин запчастей для мобильных устройств Duos.by работает больше 10 лет в сфере торговли запчастями к мобильным устройствам.
          </p>
          <p>
            Мы предлагаем  широкий ассортимент запчастей к смартфонам, планшетам, умным часам и многое другое.
          </p>
          <p>
            Наша торговая точка расположена: Беларусь, Минск, ул Тимирязева 127/4 Радиомаркет, павильон Д21. Работаем Вторник-Воскресенье с 9.30-16.30 Выходной- Понедельник.
          </p>
            Приглашаем к сотрудничеству заинтересованных лиц и компании по приобретению качественных запчастей на взаимовыгодных условиях.
        </div>
      </div>
    </div>    
  </div>
</section>
<?php echo $this->endSection() ?>