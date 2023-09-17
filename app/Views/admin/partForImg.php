<?php echo $this->extend('templates/admin') ?>

<?php echo $this->section('content') ?>

<section class="send-photo">
  <a name="start"></a>
  <div class="container">
    <div class="row">
      <div class="col-12 col-lg-8 offset-lg-2">
        <h3 class="send-photo-title">
          <?= $part['part_h'] . " - загрузка фото" ?>
        </h3>
        <div id="upload-photo-result"></div>
        <?= csrf_field() ?>
        <form id="send-photo-form" class="send-photo-form">
          <input id="send-photo-hidden" type="hidden" name="slug" value="<?= $part['part_slug'] ?>">
          <div class="send-photo-form__group form-group">
            <div class="send-photo-form__img">
              <img src="<?= base_url() . "assets/img/parts/{$part['part_slug']}/{$part['part_first_img']}.webp" ?>" alt="">
            </div>
            <label for="photo1">Фото-1</label>
            <input id="send-photo1-input" class="form-control" type="file" name="photo1" accept="image/*" />
            <!-- accept="image/*" -->
            <span id="photo1-valid" class="text-danger"></span>
          </div>
          <div class="send-photo-form__group form-group">
            <div class="send-photo-form__img">
              <img src="<?= base_url("assets/img/parts/{$part['part_slug']}/{$part['part_sec_img']}.webp") ?>" alt="">
            </div>
            <label for="photo2">Фото-2</label>
            <input id="send-photo2-input" class="form-control" type="file" name="photo2" accept="image/*" />
            <!-- accept="image/*"  -->
            <!-- <span id="photo2-valid" class="text-danger"></span> -->
          </div>
          <div class="form-group d-flex justify-content-around align-items-center">
            <button id="send-photo-button" class="btn btn-primary" name="btn" value="photo" type="submit">Отправить фотки</button>
            <a href="<?= site_url("/dimon") ?>">Admin</a>
          </div>
        </form>
      </div>
    </div>
</section>




<?php echo $this->endSection() ?>