<?php echo $this->extend('templates/admin') ?>

<?php echo $this->section('content') ?>
<section class="admin">
  <a name="start"></a>
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-8 offset-md-2">
        <div class="admin-tabs">
          <ul class="admin-tabs__caption">
            <li class="btn btn-secondary admin-tab-btn actived">Загрузка прайса</li>
            <li class="btn btn-secondary admin-tab-btn">Редактирование</li>            
          </ul>
          <div class="admin-tabs__content actived">            
            <form id="send-price-form" class="d-flex flex-column align-items-center gap-2" enctype="multipart/form-data">
              <!-- Выводим в браузер скрытое поле -->
              <?= csrf_field() ?>
              <div id="upload-price-result"></div>
              <div class="form-group d-flex flex-column gap-4">
                <label for="xls">Выберите файл для загрузки</label>
                <input id="send-price-input" class="form-control-file mt-2" type="file" name="xls" />
                <span id="xls-valid" class="text-danger"></span>
              </div>
              <div class="form-group mt-3">
                  <button id="send-price-button" class="btn btn-primary" name="btn" value="price" type="submit">Отправить</button>              
              </div>
            </form>                        
          </div>
          <div id="admin_search" class="admin-tabs__content">         
            <div class="form-group text-center">
                <label for="search">Наберите модель устройства или наименование детали</label>
                <input type="search" class="form-control mt-1 mb-1" name="search" placeholder="" value="">
                <div class="search-valid-message"></div>
            </div>
            <div class="form-group d-flex justify-content-around align-items-center mt-2">
                <button type="submit" class="btn btn-primary" name="btn" value="forImg">Найти</button>
                <!-- <button class="btn btn-secondary">Отмена</button> -->
            </div>
            <div class="search-result-message"></div>
            <ul class="search-result-list"></ul>                       
          </div>          
        </div>
      </div>
    </div>
  </div>
</section>
<?php echo $this->endSection() ?>