<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Filters\SmartSearchDataFilter;
use App\Libraries\GetParts;
use App\Libraries\SmartSearch;

class Parts extends BaseController
{
    protected $builder;
    protected $aliases;
    protected $data;
    protected $part_kinds;
    protected $smart_search;

    public function __construct()
    {        
        $this->aliases = aliases();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('parts');
        $this->smart_search = new SmartSearch();                
        $get_parts = new GetParts();
        $this->data['parts_slider'] = $get_parts->getSlider();
        $this->part_kinds = $get_parts->getPartKinds();
        $this->data['part_kinds'] = $this->part_kinds;       
    }
    // Не используется
    public function index()
    {
        $data = $this->data;
        return 'hello mikola from parts';
    }
    // Страница товара
    public function part($slug)
    {
        $aliases = $this->aliases;
        $data = $this->data;        
        $part_in = $this->builder->where('part_slug', $slug)->get()->getResultArray();
        $part = $part_in[0];
        $data['part'] = $part;
        switch ($part['part_kind_group']) {
            case 1:
                $breadcrumb_data = [
                    [
                        "name" => "Главная",
                        "title" => "Перейти на главную страницу",
                        "link" => site_url('#start'),
                    ],
                    [
                        "name" => $aliases['part_kinds'][$part['part_kind']]['for_menu'],
                        "title" => "Указать бренд для вида комплектующих '{$aliases['part_kinds'][$part['part_kind']]['for_list']}'",
                        "link" => site_url("kind-1/{$part['part_kind']}#start"),
                    ],
                    [
                        "name" => $aliases['part_kinds'][$part['part_kind']]['for_menu'] . " {$part['dev_brand']}",
                        "title" => "Указать модель устройства для вида комплектующих '{$aliases['part_kinds'][$part['part_kind']]['for_list']}' бренда {$part['dev_brand']}",
                        "link" => site_url("brands-models/{$part['dev_brand']}/{$part['part_kind']}#start"),
                    ],
                ];
                break;
            case 2:
                $breadcrumb_data = [
                    [
                        "name" => "Главная",
                        "title" => "Перейти на главную страницу",
                        "link" => site_url('#start'),
                    ],
                    [
                        "name" => $aliases['part_kinds'][$part['part_kind']]['for_menu'] . " (выбор категории)",
                        "title" => "Указать категорию для вида комплектующих '{$aliases['part_kinds'][$part['part_kind']]['for_list']}'",
                        "link" => site_url("kind-2/{$part['part_kind']}#start"),
                    ],
                    [
                        "name" => $aliases['part_kinds'][$part['part_kind']]['for_menu'] . " категории \"{$aliases['part_kinds'][$part['part_kind']]['part_sub_kinds'][$part['part_sub_kind']]['for_list']}\"",
                        "title" => $aliases['part_kinds'][$part['part_kind']]['for_menu'] . " категории '{$aliases['part_kinds'][$part['part_kind']]['part_sub_kinds'][$part['part_sub_kind']]['for_list']}' - выбрать",
                        "link" => site_url("sub-kinds-parts/{$part['part_kind']}/{$part['part_sub_kind']}#start"),
                    ],
                ];
                break;
            case 3:
                $breadcrumb_data = [
                    [
                        "name" => "Главная",
                        "title" => "Перейти на главную страницу",
                        "link" => site_url('#start'),
                    ],
                    [
                        "name" => $aliases['part_kinds'][$part['part_kind']]['for_menu'],
                        "title" => "'{$aliases['part_kinds'][$part['part_kind']]['for_menu']}' - выбрать",
                        "link" => site_url("kind-3/{$part['part_kind']}#start"),
                    ],                   
                ];
                break;            
            default:
                $breadcrumb_data = [];
                break;
        }        
        $data['breadcrumb_data'] = $breadcrumb_data;

        $first_img = $part['part_first_img'];

        if ($first_img) {
            $first_img_alt = $this->smart_search->getPart($slug)[4] . ' - изображение1';
            $first_img_link = base_url("/assets/img/parts/$slug/thumbnails/{$first_img}.webp");
            $first_img_link_popup = base_url("/assets/img/parts/$slug/{$first_img}.webp");
            $first_img_res = "<div class='part-block__img img1'><img src='{$first_img_link}' alt=\"$first_img_alt\"></div>";
            $data['meta_robots'] = "<meta name=\"robots\" content=\"all\">\n";       

        } else {
            $first_img_res = '';
            $data['meta_robots'] = "<meta name=\"robots\" content=\"noindex\">\n";
        }
        $sec_img = $part['part_sec_img'];

        if ($sec_img) {
            $sec_img_link = base_url("/assets/img/parts/$slug/thumbnails/{$sec_img}.webp");
            $sec_img_link_popup = base_url("/assets/img/parts/$slug/{$sec_img}.webp");
            $sec_img_alt = $this->smart_search->getPart($slug)[4] . ' - изображение2';
            $sec_img_res = "<div class='part-block__img img2'><img src='{$sec_img_link}' alt=\"$sec_img_alt\"></div>";
        } else {
            $sec_img_res = '';
        }
        if (empty($part['part_price']) ) {            
            $data['price'] = <<<END
            <span itemprop="price">В данный момент товара нет в наличии</span>
            <span itemprop="priceCurrency"></span>
            END;
        } else {
            $data['price'] = <<<END
                                <span>Цена:</span>
                                <span itemprop="price">{$part['part_price']}</span>
                                <span itemprop="priceCurrency">руб.</span>
            END;
        }
        $data['sec_img_link'] = $sec_img_link ?? '';
        $data['first_img_link'] = $first_img_link ?? '';
        $data['sec_img_link_popup'] = $sec_img_link_popup ?? '';
        $data['first_img_link_popup'] = $first_img_link_popup ?? '';
        $data['sec_img_alt'] = $sec_img_alt ?? '';
        $data['first_img_alt'] = $first_img_alt ?? '';
        $data['first_img_res'] = $first_img_res;
        $data['sec_img_res'] = $sec_img_res;
        $data['title'] = $part['part_seo_title'] . ' ' . 'купить в Минске';
        $data['description'] = $part['part_seo_desc'] . ' ' . 'купить в Минске, ТЦ Ждановичи';
        $data['link_can'] = site_url("parts/{$slug}");
        return view('parts/part', $data);
    }
    // Обработка part_kind с kind_type = 1, вывод dev_brands для данного part_kind
    public function kind1(string $part_kind)
    {
        $data = $this->data;
        $aliases = $this->aliases;
        $data['page_h'] = $aliases['part_kinds']["$part_kind"]['for_menu'] . ' для мобильных устройств - выбор бренда';

        $brands = $this->builder->distinct()->select('dev_brand')->where(['part_kind' => "{$part_kind}",])->get()->getResultArray();

        foreach ($brands as &$item) {
            $item['title'] = $item['dev_brand'];
            $item['link'] = site_url("brands-models/{$item['dev_brand']}/$part_kind#start");
        }
        unset($item);
        $breadcrumb_data = [
            [
                "name" => "Главная",
                "title" => "Перейти на главную страницу",
                "link" => site_url(),
            ],                     
        ];
        $data['breadcrumb_data'] = $breadcrumb_data;
        $data['items'] = $brands;

        $data['title'] = $data['page_h'];
        $data['description'] = $data['page_h'];
        $data['link_can'] = site_url("kind-1/$part_kind");
        $data['meta_robots'] = "<meta name=\"robots\" content=\"noindex\">\n";
        return view('parts/categories', $data);
    }
    // Обработка part_kind с kind_type = 2, вывод part_sub_kinds для данного part_kind
    public function kind2(string $part_kind)
    {
        $data = $this->data;
        $aliases = $this->aliases;
        $data['page_h'] = $aliases['part_kinds']["$part_kind"]['for_menu'] . ' для мобильных устройств - выбор категории';

        $sub_kinds = $this->builder->distinct()->select('part_sub_kind')->where(['part_kind' => "{$part_kind}",])->get()->getResultArray();

        foreach ($sub_kinds as &$item) {
            $item['title'] = $aliases['part_kinds']["$part_kind"]['part_sub_kinds']["{$item['part_sub_kind']}"]['for_list'];
            $item['link'] = site_url("sub-kinds-parts/{$part_kind}/{$item['part_sub_kind']}#start");
        }
        unset($item);
        $breadcrumb_data = [
            [
                "name" => "Главная",
                "title" => "Перейти на главную страницу",
                "link" => site_url('#start'),
            ],                               
        ];
        $data['breadcrumb_data'] = $breadcrumb_data;       
        $data['items'] = $sub_kinds;
        $data['title'] = $data['page_h'];
        $data['description'] = $data['page_h'];
        $data['link_can'] = site_url("kind-2/$part_kind#start");
        $data['meta_robots'] = "<meta name=\"robots\" content=\"noindex\">\n";
        
        return view('parts/categories', $data);
    }
    // Обработка part_kind с kind_type = 3, вывод parts  для данного part_kind
    public function kind3(string $part_kind)
    {
        $data = $this->data;
        $data['page_h'] = $this->aliases['part_kinds']["$part_kind"]['for_menu'] . ' для мобильных устройств';

        $parts = $this->builder->select('part_name, part_slug')->where(['part_kind' => "{$part_kind}",])->get()->getResultArray();
        foreach ($parts as &$item) {
            $item['title'] = $item['part_name'];
            $item['link'] = site_url("parts/{$item['part_slug']}#start");
        }
        $breadcrumb_data = [
            [
                "name" => "Главная",
                "title" => "Перейти на главную страницу",
                "link" => site_url('#start'),
            ],                              
        ];
        $data['breadcrumb_data'] = $breadcrumb_data;
        unset($item);
        $data['items'] = $parts;
        $data['title'] = $data['page_h'];
        $data['description'] = $data['page_h'];
        $data['link_can'] = site_url("kind-3/$part_kind#start");
        $data['meta_robots'] = "<meta name=\"robots\" content=\"noindex\">\n";       
        return view('parts/categories', $data);
    }
    // получаем dev_models из dev_brand для определенного part_kind
    public function brandModel(string $brand, string $part_kind)
    {
        $data = $this->data;
        $aliases = $this->aliases;
        $data['page_h'] = $aliases['part_kinds']["$part_kind"]['for_menu'] . " для мобильных устройств $brand - выбор модели";
        $models = $this->builder->select('dev_model, part_slug')->where(['dev_brand' => "{$brand}", 'part_kind' => "{$part_kind}",])->get()->getResultArray();
        foreach ($models as &$item) {
            $item['title'] = $item['dev_model'];
            $item['link'] = site_url("/parts/{$item['part_slug']}#start");
        }
        unset($item);
        $breadcrumb_data = [
            [
                "name" => "Главная",
                "title" => "Перейти на главную страницу",
                "link" => site_url('#start'),
            ],
            [
                "name" => $aliases['part_kinds'][$part_kind]['for_menu'],
                "title" => "Указать бренд для вида комплектующих '{$aliases['part_kinds'][$part_kind]['for_list']}'",
                "link" => site_url("kind-1/{$part_kind}#start"),
            ],           
        ];
        $data['breadcrumb_data'] = $breadcrumb_data;
        $data['items'] = $models;
        $data['title'] = $data['page_h'];
        $data['description'] = $data['page_h'];
        $data['link_can'] = site_url("brands-models/$brand/$part_kind");
        $data['meta_robots'] = "<meta name=\"robots\" content=\"noindex\">\n";
        return view('parts/categories', $data);
    }
    // Получаем parts для данного  part_sub_kind
    public function subKindParts(string $part_kind, string $part_sub_kind)
    {
        $data = $this->data;
        $aliases = $this->aliases;
        $data['page_h'] = $aliases['part_kinds']["$part_kind"]['for_menu'] . " категории \"{$aliases['part_kinds']["$part_kind"]['part_sub_kinds']["$part_sub_kind"]['for_list']}\"";

        $parts = $this->builder->where(['part_kind' => "{$part_kind}", 'part_sub_kind' => "{$part_sub_kind}",])->get()->getResultArray();
        foreach ($parts as &$item) {
            $item['title'] = $item['part_name'];
            $item['link'] = site_url("parts/{$item['part_slug']}#start");
        }
        unset($item);
        $breadcrumb_data = [
            [
                "name" => "Главная",
                "title" => "Перейти на главную страницу",
                "link" => site_url('#start'),
            ],
            [
                "name" => $aliases['part_kinds'][$part_kind]['for_menu'] . " (выбор категории)",
                "title" => "Указать категорию для вида комплектующих '{$aliases['part_kinds'][$part_kind]['for_list']}'",
                "link" => site_url("kind-2/$part_kind#start"),
            ],                    
        ];
        $data['breadcrumb_data'] = $breadcrumb_data;        
        $data['items'] = $parts;
        $data['title'] = $data['page_h'];
        $data['description'] = $data['page_h'];
        $data['link_can'] = site_url("sub-kinds-parts/$part_kind/$part_sub_kind");
        $data['meta_robots'] = "<meta name=\"robots\" content=\"noindex\">\n";
        return view('parts/categories', $data);
    }
}
