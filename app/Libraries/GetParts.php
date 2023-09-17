<?php
namespace App\Libraries;

class GetParts
{
  protected $aliases;
  protected  $db;
  protected $builder;
  protected $data;
  protected $smart_search;

  public function __construct()
  {
    helper('my'); 
    $this->aliases = aliases();   
    $this->db = \Config\Database::connect();
    $this->builder = $this->db->table('parts');
    $this->smart_search = new SmartSearch();
  }

  public function getPartKinds()
  {
    // Получаем part_kinds для kind_type = 1
    $part_kinds_type1 = $this->builder->distinct()->select('part_kind')->where(['dev_brand !=' => '',])->get()->getResultArray();
    foreach ($part_kinds_type1 as &$item) {
        $item['link'] = site_url("kind-1/{$item['part_kind']}#start");
    }
    unset($item);
    // Получаем part_kinds для kind_type = 2
    $part_kinds_type2 = $this->builder->distinct()->select('part_kind')->where(['part_sub_kind !=' => '', 'part_price !=' => 0,])->get()->getResultArray();
    foreach ($part_kinds_type2 as &$item) {
        $item['link'] = site_url("kind-2/{$item['part_kind']}#start");
    }
    unset($item);
    // Получаем part_kinds для kind_type = 3
    $part_kinds_type3 = $this->builder->distinct()->select('part_kind')->where(['part_sub_kind' => '', 'dev_brand' => '', 'part_price !=' => 0,])->get()->getResultArray();
    foreach ($part_kinds_type3 as &$item) {
        $item['link'] = site_url("kind-3/{$item['part_kind']}#start");
    }
    unset($item);
    // Собираем все part_kinds
    $part_kinds = array_merge($part_kinds_type1, $part_kinds_type2, $part_kinds_type3);
    foreach ($part_kinds as &$item) {
        $item['for_list'] = $this->aliases['part_kinds']["{$item['part_kind']}"]['for_list'];
        $item['for_menu'] = $this->aliases['part_kinds']["{$item['part_kind']}"]['for_menu'];
    }
    unset($item);        
    $this->data['part_kinds'] = $part_kinds;
    return $part_kinds;
  }
  public function getSlider()
  {
    // Получаем новые поступления вывод в слайдере хедера (view/templates/default)
    // $parts_slider = $this->builder->where(['part_en_slider' => 1])->get()->getResultArray();
    $parts_slider = $this->builder->where(['part_en_slider !=' => 0])->get()->getResultArray();
    foreach ($parts_slider as &$item) {
        if ($item['part_desc']) {
            $item['part_desc_slider'] = "<p class='header-slider__desc'>{$item['part_desc']}</p>";
        } else {
            $item['part_desc_slider'] = '';
        }
        $item['title_for_alt'] = $this->smart_search->getPart($item['part_slug'])[4];
        if ($item['part_en_slider'] == 1) {
          $item['part_img'] = $item['part_first_img'];
        } else {
          $item['part_img'] = $item['part_sec_img'];
        }
    }
    unset($item);
    $this->data['parts_slider'] = $parts_slider;
    return $parts_slider;
  }
  


}