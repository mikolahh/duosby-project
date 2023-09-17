<?php echo $this->extend('templates/default') ?>
<?php echo $this->section('content') ?>
<section class="items">
  <?= $this->include('items/smart_search_result') ?> 
  <div class="smart-search-overlay"></div>
  <a name="start"></a>
  <div class="container">
  <?= $this->include('items/breadcrumbs') ?>  
    <div class="row">
      <div class="col-12 mb-4 col-lg-4 d-flex flex-column justify-content-center">
        <?= $this->include('items/smart_search') ?> 
      </div>
      <div class="col-12 mb-4 col-lg-8 d-flex flex-column justify-content-center">
        <div class="categories">
          <h2 class="categories__title">
            <?= $page_h ?>
          </h2>
          <ul class="categories__list">
            <?php foreach($items as $item): ?>
              <li class="categories__item">
                <a class="btn btn-info" href="<?=$item['link']?>">
                  <?=$item['title']?>
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