<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\GetParts;
use App\Libraries\SmartSearch;
use App\Bots\BotApi;
use App\Bots\MyBot;

class Main extends BaseController
{
    protected $builder;
    protected $aliases;
    protected $data;
    protected $part_kinds;

    public function __construct()
    {        
        $this->aliases = aliases();
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('parts');
        $get_parts = new GetParts();
        $this->data['parts_slider'] = $get_parts->getSlider();
        $this->part_kinds = $get_parts->getPartKinds();
        $this->data['part_kinds'] = $this->part_kinds;       
    }
    // Вывод главной страницы
    public function index()
    {
        $data = $this->data;
        $data['title'] = "Комплектующие и запчасти для мобильных телефонов купить в Минске";
        $data['description'] = "Магазин запчастей и комплектующих для телефонов, планшетов и других мобильных устройств|Минск, ТЦ Ждановичи";
        $data['link_can'] = site_url();
        $data['meta_robots'] = "<meta name=\"robots\" content=\"all\">\n";
        return view('main/index', $data);
    }
    // Smart-search - 1-этап обработка part_kind и text_search 
    public function partKindSearch()
    {
        //   Валидация-part_kind должен быть выбран 
        if ($this->request->is('ajax')) {
            $rules = [
                'part_kind' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Вид комплектующих должен быть выбран'
                    ]
                ]
            ];
            $validation = $this->validate($rules);
            $data['valid_status'] = $validation;
            if (!$validation) {
                $validation_object = $this->validator;
                $data['part_kind_valid_message'] = display_error($validation_object, 'part_kind');
                return $this->response->setJSON($data);
            }
            $part_kind = $this->request->getPost('part_kind');
            $match = $this->request->getPost('text_search');
            $part_kind = (string)$part_kind;
            $match = (string)$match;
            // $match = $this->db->escape($match);                  
            $limit1 = 20; // максимальное кол-во parts, которые напрямую, минуя dev_brand или part_kind, могут выводиться на первом этапе
            $smart_search = new SmartSearch;
            $res = $smart_search->Stage1($part_kind, $match, $limit1);
            $data['res'] = $res;
            $action_numb = $res[0];
            $arr_data = $res[1];
            $search_res_message = $res[2];
            $action_message = $res[3];
            $data['action_numb'] = $action_numb;
            $data['search_res_message'] = $search_res_message;
            $data['action_message'] = $action_message;
            $data['arr_data'] = $arr_data;
            return $this->response->setJSON($data);
        }
    }
    // smart-search 2 этап
    // получаем dev_models из dev_brand для определенного part_kind
    public function brandModel()
    {
        if ($this->request->is('ajax')) {
            $dev_brand = $this->request->getPost('first_param');
            $part_kind = $this->request->getPost('second_param');
            $limit2 = 60; // максимальное кол-во parts, одновременно выводимых на экран        

            $count = $this->builder->where(['dev_brand' => "{$dev_brand}", 'part_kind' => "{$part_kind}",])->countAllResults();
            if ($count <= $limit2) {
                $data['search_res_message'] = "Показаны все варианты($count)";
            } else {
                $data['search_res_message'] = "<span>Получены варианты: {$count}</span><span>Показаны: {$limit2}</span>";
            }
            $data['action_message'] = "Выберите нужную модель мобильного устройства, если таковая имеется";
            $models = $this->builder->select('dev_model, part_slug')->where(['dev_brand' => "{$dev_brand}", 'part_kind' => "{$part_kind}"])->get()->getResultArray();
            foreach ($models as &$item) {
                $item['title'] = $item['dev_model'];
                $item['link'] = site_url("/parts/{$item['part_slug']}");
            }
            unset($item);
            $data['arr_data'] = array_chunk($models, $limit2);
            $data['action_numb'] = 2;
            return $this->response->setJSON($data);
        }
    }
    // Получаем parts для данного  part_sub_kind
    public function subKindParts()
    {
        if ($this->request->is('ajax')) {
            $part_sub_kind = $this->request->getPost('first_param');
            $part_kind = $this->request->getPost('second_param');

            $count = $this->builder->where(['part_kind' => "{$part_kind}", 'part_sub_kind' => "{$part_sub_kind}",])->countAllResults();

            $limit2 = 60;

            if ($count <= $limit2) {
                $data['search_res_message'] = "Показаны все варианты($count)";
            } else {
                $data['search_res_message'] = "<span>Получены варианты: {$count}</span><span>Показаны: {$limit2}</span>";
            }
            $data['action_message'] = "Выберите нужный, если таковой имеется";

            $parts = $this->builder->select('part_name, part_slug')->where(['part_kind' => "{$part_kind}", 'part_sub_kind' => "{$part_sub_kind}", 'part_price !=' => 0,])->get()->getResultArray();
            foreach ($parts as &$item) {
                $item['title'] = $item['part_name'];
                $item['link'] = site_url("parts/{$item['part_slug']}");
            }
            unset($item);
            $data['arr_data'] = array_chunk($parts, $limit2);
            $data['action_numb'] = 4;
            return $this->response->setJSON($data);
        }
    }
    // Страница о нас
    public function about()
    {
        $data = $this->data;
        $data['meta_robots'] = "<meta name=\"robots\" content=\"noindex\">\n";
        $data['title'] = "О нас. Магазин комплектующих для мобильных устройств";
        $data['description'] = "Магазин запчастей и комплектующих для телефонов, планшетов и других мобильных устройств|Минск, ТЦ Ждановичи";
        $data['link_can'] = site_url('about');
        $data['breadcrumb_data'] = [
            [
                "name" => "Главная",
                "title" => "Перейти на главную страницу",
                "link" => site_url(),
            ],
        ];
        return view('main/about', $data);
    }
    // Обработка удаленных страниц
    public function deletedPages($seg1 = false, $seg2 = false)
    {
        $data = $this->data;
        $data['meta_robots'] = "<meta name=\"robots\" content=\"noindex\">\n";
        header("HTTP/1.1 410 Gone");
        header("Status: 410 Gone");
        header("Retry-After: 3600");
        return view('errors/error_410', $data);
    }
   
}
