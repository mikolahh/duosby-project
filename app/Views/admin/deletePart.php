<?php echo $this->extend('templates/admin') ?>
<?php echo $this->section('content') ?>
<section class="delete-part">
  <a name="start"></a>
  <div class="container">
    <div class="row">
      <div class="col col-md-8 offset-md-2 d-flex flex-column">
        <h3 class="text-center mb-3"><?= "{$part['part_h']} \n подтвердить удаление" ?>
        </h3>
        <div class="delete-result"></div>      
        <div class="form-group  d-flex flex-column gap-2 align-items-center flex-sm-row justify-content-around">          
          <button type="submit" class="btn btn-danger delete-button" name='' value="<?= $part['id'] ?>">
            Удалить
          </button>
          <a class="btn btn-info" href="<?= site_url('/dimon') ?>">
            Вернуться в админпанель
          </a>
        </div>
      </div>
    </div>
    
  </div>

</section>




<?php echo $this->endSection() ?>