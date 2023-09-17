<nav class="breadcrumbs">
  <ul class="breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">
      <?php foreach($breadcrumb_data as $key=>$item) : ?>
          <li class="breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
              <a href="<?=$item['link']?>" title="<?=$item['title']?>" itemprop="item">
              <span itemprop="name"><?=$item['name']?></span>
              <meta itemprop="position" content="<?=$key?>">
              </a>
          </li>
      <?php endforeach; ?>
    </ul>
</nav>