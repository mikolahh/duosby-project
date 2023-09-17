<?php echo $this->extend('templates/default') ?>
<?php echo $this->section('content') ?>

<section class="main">
  <div class="smart-search-overlay"></div> 
  <?= $this->include('items/smart_search_result') ?>
  <a name="start"></a>
  <div class="container">
    <h1>Комплектующие для мобильных устройств</h1>
    <div class="row">
      <div class="col-12 mb-4 col-lg-4 d-flex flex-column justify-content-center">
      <?= $this->include('items/smart_search') ?>       
      </div>
      <div class="col-12 col-lg-8 d-flex flex-column justify-content-center">
        <div class="categories">
          <h2 class="categories__title">
            Выбор по навигации
          </h2>
          <ul class="categories__list">
            <?php foreach ($part_kinds as $item) : ?>
            <li class="part-kinds__item categories__item">
              <a class="btn btn-info" href="<?= $item['link'] ?>">
                <?= $item['for_menu'] ?>
              </a>
            </li>            
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>    
  </div>   
</section>  

<?php echo $this->endSection() ?>