<?php echo $this->extend('templates/default') ?>
<?php echo $this->section('content') ?>

<section class="error">
  <h1>Запрашиваемая страница была удалена</h1>
  <p>Вы можете получить необходимую информацию, пользуясь интерфейсом сайта</p>
  <a href="<?= site_url() ?>">Перейти на главную страницу</a>
</section>

<?php echo $this->endSection() ?>