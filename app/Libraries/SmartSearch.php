<?php
namespace App\Libraries;

class SmartSearch
{ 
  protected $aliases;
  protected  $db;
  protected $builder;

  public function __construct()
  {
    helper('my'); 
    $this->aliases = aliases();   
    $this->db = \Config\Database::connect();
    $this->builder = $this->db->table('parts');
  }
  public function Stage1 (string $part_kind, string $match, int $limit1)  
  {    
    $match_isset = (bool)$match;
    $part_kind_group = $this->builder->distinct()->select('part_kind_group')->where(['part_kind' => "{$part_kind}"])->get()->getResultArray()[0]['part_kind_group'];            
    if ($match_isset) {      
      $search_kind_count = $this->builder->where("part_kind = '{$part_kind}' && (dev_brand_model LIKE '%{$match}%' || part_name LIKE '%{$match}%' || part_h LIKE '%{$match}%' || part_desc LIKE '%{$match}%' || part_slug LIKE '%{$match}%' || part_param LIKE '%{$match}%' || part_kind LIKE '%{$match}%' || part_sub_kind LIKE '%{$match}%')")->countAllResults();                       
    } else {
        $search_kind_count = 0;
    }            
    $kind_count = $this->builder->where(['part_kind' => "{$part_kind}"])->countAllResults();
    switch (true) {
      case $search_kind_count == 0:
          $count = $kind_count;
          $query_method = 'kind';// без поиска                  
          break;
      case $search_kind_count < $kind_count:
          $count = $search_kind_count;
          $query_method = 'kind-match'; // с поиском         
          break;                      
    }    
    switch ($part_kind_group) {
      case 1:
          if ($count > $limit1) {
              $action_numb = 1;
              switch (true) {
                case $match_isset && $search_kind_count:
                    $search_res_message = "Найдены варианты($count)";
                    break;
                case $match_isset && !$search_kind_count:
                    $search_res_message = "Совпадений не найдено, получены все варианты($count)";
                    break;                            
                default:
                $search_res_message = "Получены все варианты($count)";
                    break;                                
                    break;
              }                                           
              $action_message = "Выберите бренд для дальнейшего поиска";
          } else {
              $action_numb = 2;
              switch (true) {
                case $match_isset && $search_kind_count:
                    $search_res_message = "Найдены и показаны все варианты($count)";
                    break;
                case $match_isset && !$search_kind_count:
                    $search_res_message = "Совпадений не найдено, показаны все варианты($count)";
                    break;                            
                default:
                $search_res_message = "Показаны все варианты($count)";
                    break;                                
                    break;
              }                                             
              $action_message = "Выберите нужную модель мобильного устройства, если таковая имеется";                    
          }                          
          break;
      case 2:
          if ($count > $limit1) {
              $action_numb = 3;
              switch (true) {
                case $match_isset && $search_kind_count:
                    $search_res_message = "Найдены варианты($count)";
                    break;
                case $match_isset && !$search_kind_count:
                    $search_res_message = "Совпадений не найдено, получены все варианты($count)";
                    break;                            
                default:
                $search_res_message = "Получены все варианты($count)";
                    break;                                
                    break;
              }                      
              $action_message = "Выберите категорию для дальнейшего поиска";
          } else {
              $action_numb = 4;
              switch (true) {
                case $match_isset && $search_kind_count:
                    $search_res_message = "Найдены и показаны варианты($count)";
                    break;
                case $match_isset && !$search_kind_count:
                    $search_res_message = "Совпадений не найдено, получены и показаны все варианты($count)";
                    break;                            
                default:
                $search_res_message = "Получены и показаны все варианты($count)";
                    break;                                
                    break;
              }                       
              $action_message = "Выберите нужный, если таковой имеется";
          }                    
          break;
      case 3:
          $action_numb = 4; 
          switch (true) {
            case $match_isset && $search_kind_count:
                $search_res_message = "Найдены и показаны все варианты($count)";
                break;
            case $match_isset && !$search_kind_count:
                $search_res_message = "Совпадений не найдено, получены и показаны все варианты($count)";
                break;                            
            default:
            $search_res_message = "Получены и показаны все варианты($count)";
                break;                                
                break;
          }                                      
          $action_message = "Выберите нужyный, если таковой имеется";                      
          break;               
    }
      switch ($action_numb) {
        case 1:
          $arr_data = self::Action1($part_kind, $match, $query_method);
          break;
        case 2:
          $arr_data = self::Action2($part_kind, $match, $query_method);
          break;
        case 3:
          $arr_data = self::Action3($part_kind, $match, $query_method);
          break;
        case 4:
          $arr_data = self::Action4($part_kind, $match, $query_method);
          break;     
      }  
      $res = [];
      array_push($res, $action_numb, $arr_data, $search_res_message, $action_message);    
    return $res;
  }  
  protected function Action1(string $part_kind, string $match, string $query_method)
  { 
    switch ($query_method) {
      case 'kind':
        $brands = $this->builder->distinct()->select('dev_brand')->where(['part_kind' => "{$part_kind}",])->get()->getResultArray();        
        break;      
      case 'kind-match':
        $brands = $this->builder->distinct()->select('dev_brand')->where("part_kind = '{$part_kind}' && (dev_brand_model LIKE '%{$match}%' || part_name LIKE '%{$match}%' || part_h LIKE '%{$match}%' || part_desc LIKE '%{$match}%' || part_slug LIKE '%{$match}%' || part_param LIKE '%{$match}%' || part_kind LIKE '%{$match}%' || part_sub_kind LIKE '%{$match}%')")->get()->getResultArray();
        break;
    } 
    foreach ($brands as &$item) {
        $item['title'] = $item['dev_brand'];
        $item['action'] = "/brand-model";
        $item['part_kind'] = $part_kind;
    } 
    unset($item);
    return $brands;
  }
  protected function Action2(string $part_kind, string $match, string $query_method)
  { 
    switch ($query_method) {
      case 'kind':
        $items = $this->builder->select('dev_brand, dev_model, part_slug, id')->where(['part_kind' => "{$part_kind}"])->get()->getResultArray();      
        break;
      case 'kind-match':
        $items = $this->builder->select('dev_brand, dev_model, part_slug, id')->where("part_kind = '{$part_kind}' && (dev_brand_model LIKE '%{$match}%' || part_name LIKE '%{$match}%' || part_h LIKE '%{$match}%' || part_desc LIKE '%{$match}%' || part_slug LIKE '%{$match}%' || part_param LIKE '%{$match}%' || part_kind LIKE '%{$match}%' || part_sub_kind LIKE '%{$match}%')")->get()->getResultArray();                
        break;
    }      
    foreach ($items as &$item) {
        $item['title'] = "<span>{$item['dev_brand']}</span><span>{$item['dev_model']}</span>";
        $item['link'] = site_url("/parts/{$item['part_slug']}");
    }    
    unset($item);
    return $items;
  }
  protected function Action3(string $part_kind, string $match, string $query_method)
  { 
    switch ($query_method) {
      case 'kind':
        $items = $this->builder->distinct()->select('part_sub_kind')->where(['part_kind' => "{$part_kind}"])->get()->getResultArray();      
        break;              
      case 'kind-match':
        $items = $this->builder->distinct()->select('part_sub_kind')->where("part_kind = '{$part_kind}' && (dev_brand_model LIKE '%{$match}%' || part_name LIKE '%{$match}%' || part_h LIKE '%{$match}%' || part_desc LIKE '%{$match}%' || part_slug LIKE '%{$match}%' || part_param LIKE '%{$match}%' || part_kind LIKE '%{$match}%' || part_sub_kind LIKE '%{$match}%')")->get()->getResultArray();        
        break;
    }    
    foreach ($items as &$item) {
       $item['title'] = $this->aliases['part_kinds']["$part_kind"]['part_sub_kinds']["{$item['part_sub_kind']}"]['for_list'] ;
       $item['action'] = "/sub-kind-parts";
       $item['part_kind'] = $part_kind;    
    }
    unset($item);        
    return $items;
  }
  protected function Action4(string $part_kind, string $match, string $query_method)
  { 
    switch ($query_method) {
      case 'kind':
        $items = $this->builder->select('part_name, part_slug, id')->where(['part_kind' => "{$part_kind}"])->get()->getResultArray();      
        break;              
      case 'kind-match':
        $items = $this->builder->select('part_name, part_slug')->where("part_kind = '{$part_kind}' && (dev_brand_model LIKE '%{$match}%' || part_name LIKE '%{$match}%' || part_h LIKE '%{$match}%' || part_desc LIKE '%{$match}%' || part_slug LIKE '%{$match}%' || part_param LIKE '%{$match}%' || part_kind LIKE '%{$match}%' || part_sub_kind LIKE '%{$match}%')")->get()->getResultArray();        
        break;
    }    
    foreach ($items as &$item) {
        $item['title'] = $item['part_name'];
        $item['link'] = site_url("parts/{$item['part_slug']}");
    }
    unset($item);              
    return $items;
  }
  public function getBrandModel($part_kind, $dev_brand, $limit)
  {
    $count = $this->builder->where(['dev_brand' => "{$dev_brand}", 'part_kind' => "{$part_kind}", 'part_price !=' => 0])->countAllResults();
    if ($count <= $limit) {
        $search_res_message = "Показаны все варианты($count)";        
    } else {
        $search_res_message = "Получены варианты: {$count}, Показаны: {$limit}";        
    }
    $action_message = "Выберите нужную модель мобильного устройства, если таковая имеется";    
    $models = $this->builder->select('dev_model, part_slug, id')->where(['dev_brand' => "{$dev_brand}", 'part_kind' => "{$part_kind}"])->get()->getResultArray();
    foreach ($models as &$item) {
        $item['title'] = $item['dev_model'];
        $item['link'] = site_url("/parts/{$item['part_slug']}");        
    }
    unset($item);
    $arr_data = array_chunk($models, $limit);
    $res = [];
    array_push($res, $arr_data, $search_res_message, $action_message);   
    return $res;

  }
  public function getSubKindParts($part_kind, $part_sub_kind, $limit)
  {
    $count = $this->builder->where(['part_kind' => "{$part_kind}", 'part_sub_kind' => "{$part_sub_kind}", 'part_price !=' => 0])->countAllResults();
    if ($count <= $limit) {
      $search_res_message = "Показаны все варианты($count)";      
    } else {
        $search_res_message = "Получены варианты: {$count}, Показаны: {$limit}";        
    }
    $action_message = "Выберите нужный, если таковой имеется";    
    $parts = $this->builder->select('part_name, part_slug, id')->where(['part_kind' => "{$part_kind}", 'part_sub_kind' => "{$part_sub_kind}", 'part_price !=' => 0,])->get()->getResultArray();
    foreach ($parts as &$item) {
      $item['title'] = $item['part_name'];
      $item['link'] = site_url("parts/{$item['part_slug']}");
    }
    unset($item);
    $arr_data = array_chunk($parts, $limit);
    $res = [];
    array_push($res, $arr_data, $search_res_message, $action_message);   
    return $res;
  }
  public function getPart($slug)
  {
    $aliases = aliases();    
    $part_in = $this->builder->where('part_slug', $slug)->get()->getResultArray();
    $part = $part_in[0];
    $dev_kind = $part['dev_kind'];
    $part_kind = $part['part_kind'];
    $part_sub_kind = $part['part_sub_kind'] ?? '';
    $brand_model = $part['dev_brand_model'] ?? '';
    $part_price = $part['part_price'];
    $part_name = $part['part_name'] ?? '';
    $part_kind_group = $part['part_kind_group'];
    if ($part_price == 0) {
      $price = "<b>В настоящее время данного товара нет в наличии</b>";
    } else {
      $price = "Цена: <b>$part_price руб.</b>";
    }
    if ($part_kind_group == 1) {
      $title_for_bot = "{$aliases['part_kinds'][$part_kind]['for_h']} {$aliases['dev_kinds'][$dev_kind]['for_dev']}" . PHP_EOL .  "<b>$brand_model</b>";
      $title_for_alt = "{$aliases['part_kinds'][$part_kind]['for_h']} {$aliases['dev_kinds'][$dev_kind]['for_dev']}" . ' ' . "$brand_model";
    } else {
      $title_for_bot = "{$aliases['part_kinds'][$part_kind]['part_sub_kinds'][$part_sub_kind]['for_h']}" . PHP_EOL . "<b>$part_name</b>";
      $title_for_alt = "{$aliases['part_kinds'][$part_kind]['part_sub_kinds'][$part_sub_kind]['for_h']}" . ' ' . "$part_name";
    }
    
    $global_info_for_bot = 'Приобрести данный товар можно по адресу:' . PHP_EOL .  'Минск, ТД Ждановичи, ул Тимирязева 127/4 Радиомаркет, павильон Д21.' . PHP_EOL . 'Время работы с <b>9-30</b> до <b>16-30</b>.' . PHP_EOL . PHP_EOL . 'Для получения дополнительной информации обращайтесь:' . PHP_EOL . '@<b>duosby</b>' . PHP_EOL . PHP_EOL . 'Все вопросы по ремонту мобильных устройств и установке комплектующих:' . PHP_EOL . '+375291169801(viber, telegram)';
    $description = $part['part_desc'];    
    $arr_data = [];
    array_push($arr_data, $title_for_bot, $price, $description, $global_info_for_bot, $title_for_alt);
    return $arr_data;
  }
  public function getPartForBot($part_id)
  {
    $aliases = aliases();    
    $part_in = $this->builder->where('id', $part_id)->get()->getResultArray();
    $part = $part_in[0];
    $dev_kind = $part['dev_kind'];
    $part_kind = $part['part_kind'];
    $part_sub_kind = $part['part_sub_kind'] ?? '';
    $brand_model = $part['dev_brand_model'] ?? '';
    $part_price = $part['part_price'];
    $part_name = $part['part_name'] ?? '';
    $part_kind_group = $part['part_kind_group'];
    if ($part_price == 0) {
      $price = "<b>В настоящее время данного товара нет в наличии</b>";
    } else {
      $price = "Цена: <b>$part_price руб.</b>";
    }
    if ($part_kind_group == 1) {
      $title_for_bot = "{$aliases['part_kinds'][$part_kind]['for_h']} {$aliases['dev_kinds'][$dev_kind]['for_dev']}" . PHP_EOL .  "<b>$brand_model</b>";
      $title_for_alt = "{$aliases['part_kinds'][$part_kind]['for_h']} {$aliases['dev_kinds'][$dev_kind]['for_dev']}" . ' ' . "$brand_model";
    } else {
      $title_for_bot = "{$aliases['part_kinds'][$part_kind]['part_sub_kinds'][$part_sub_kind]['for_h']}" . PHP_EOL . "<b>$part_name</b>";
      $title_for_alt = "{$aliases['part_kinds'][$part_kind]['part_sub_kinds'][$part_sub_kind]['for_h']}" . ' ' . "$part_name";
    }
    
    $global_info_for_bot = 'Приобрести данный товар можно по адресу:' . PHP_EOL .  'Минск, ТД Ждановичи, ул Тимирязева 127/4 Радиомаркет, павильон Д21.' . PHP_EOL . 'Время работы с <b>9-30</b> до <b>16-30</b>.' . PHP_EOL . PHP_EOL . 'Для получения дополнительной информации обращайтесь:' . PHP_EOL . '@<b>duosby</b>' . PHP_EOL . PHP_EOL . 'Все вопросы по ремонту мобильных устройств и установке комплектующих:' . PHP_EOL . '+375291169801(viber, telegram)';
    $description = $part['part_desc'];    
    $arr_data = [];
    array_push($arr_data, $title_for_bot, $price, $description, $global_info_for_bot, $title_for_alt);
    return $arr_data;

  }
  public function getImg($slug)
  {
    $part_in = $this->builder->where('part_slug', $slug)->get()->getResultArray();
    $part = $part_in[0];    
    $first_img_isset = boolval($part['part_first_img']);    
    $sec_img_isset = boolval($part['part_sec_img']);
    if ($first_img_isset) {      
      $first_img_name = "{$part['part_first_img']}.webp";
      $first_img = base_url("assets/img/parts/$slug/thumbnails/$first_img_name");     
      $arr_data = [];
      array_push($arr_data, $first_img, $first_img_name);
      return $arr_data;
    } else {
      return false;
    }
  }
  public function getImgForBot($part_id)
  {
    $part_in = $this->builder->where('id', $part_id)->get()->getResultArray();
    $part = $part_in[0];    
    $first_img_isset = boolval($part['part_first_img']);    
    $sec_img_isset = boolval($part['part_sec_img']);
    if ($first_img_isset) {
      $slug = $part['part_slug'];      
      $first_img_name = "{$part['part_first_img']}.webp";
      $first_img = base_url("assets/img/parts/$slug/thumbnails/$first_img_name");     
      $arr_data = [];
      array_push($arr_data, $first_img, $first_img_name);
      return $arr_data;
    } else {
      return false;
    }

  }
  public function getAllPartsForBot($match, $limit = 10)
  {
    $aliases = aliases();
    $search_res_count = $this->builder->orLike(['dev_brand' => $match, 'dev_model' => $match, 'dev_brand_model' => $match, 'part_name' => $match, 'part_kind' => $match, 'part_sub_kind' => $match, 'part_slug' => $match, 'part_h' => $match, 'part_seo_title' => $match, 'part_seo_desc' => $match, 'part_desc' => $match])->countAllResults();

    $search_res = $this->builder->orLike(['dev_brand' => $match, 'dev_model' => $match, 'dev_brand_model' => $match, 'part_name' => $match, 'part_kind' => $match, 'part_sub_kind' => $match, 'part_slug' => $match, 'part_h' => $match, 'part_seo_title' => $match, 'part_seo_desc' => $match, 'part_desc' => $match])->limit($limit)->get()->getResultArray();

    switch (true) {
      case $search_res_count == 0:
        $search_res_message = "<i>Поиск не дал результатов</i>";
        $action_message = "<i>Попробуйте задать другие условия поиска</i>";
        $count = 0;
        break;
      case $search_res_count <= $limit:
        $search_res_message = "<i>Получены и показаны все результаты(<b>$search_res_count</b>).</i>";
      $action_message = "<i>Выберите подходящий вариант, если таковой имеется.</i>";
      $count = $search_res_count;
        break;
      case $search_res_count > $limit:
        $search_res_message = "<i>Получены варианты-<b>$search_res_count</b>. Показаны-<b>$limit</b></i>";
        $action_message = "<i>Выберите подходящий, если таковой имеется, либо задайте более точные условия поиска.</i>";
        $count = $limit;
        break;      
    }
    $parts = [];
    foreach ($search_res as &$item) {
      $part_kind = $item['part_kind'];
      $dev_kind = $item['dev_kind'];
      $brand_model = $item['dev_brand_model'];
      $part_sub_kind = $item['part_sub_kind'];
      $part_name = $item['part_name'];
      $part = [];
      if ($item['part_kind_group'] == 1) {
        $part['title_for_bot'] = "{$aliases['part_kinds'][$part_kind]['for_h']} {$aliases['dev_kinds'][$dev_kind]['for_dev']}" . PHP_EOL .  "<b>$brand_model</b>";        
      } else {
        $part['title_for_bot'] = "{$aliases['part_kinds'][$part_kind]['part_sub_kinds'][$part_sub_kind]['for_h']}" . PHP_EOL . "<b>$part_name</b>";       
      }
      $part['slug'] = $item['part_slug'];
      $part['part_id'] = $item['id'];          
      array_push($parts, $part);      
    }
    unset($item);
    $result = [];
    array_push($result, $parts, $search_res_message, $action_message, $count);
    return $result;        
  } 
}