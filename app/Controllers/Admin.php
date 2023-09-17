<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Xls;
use App\Libraries\Webp;
use App\Libraries\Hash;

class Admin extends BaseController
{
    protected $builder;
    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->builder = $this->db->table('parts');
    }
    public function index()
    {
        return view('admin/index');
    }
    // Контроллер самого процесса загрузки прайса
    public function getPrice()
    {
        if ($this->request->is('ajax')) {
            $rules = [
                'xls' => [
                    'rules' => 'uploaded[xls]',
                    'errors' => [
                        'uploaded' => 'Файл должен быть обязательно выбран'
                    ]
                ]
            ];
            $cells = cells(); // my_helper                      
            $validation = $this->validate($rules);
            $data['valid_status'] = $validation;
            $data['upload_status'] = false;

            if (!$validation) {
                $validation_object = $this->validator;
                $data['xls_valid'] = display_error($validation_object, 'xls');
                return $this->response->setJSON($data);
            } else {
                $data['xls_valid'] = false;
                // формируем путь к директории, где будут храниться загружаемые файлы			
                $uploaddir = ROOTPATH . 'public/assets/price/'; // 1 способ - абсолютный путь
                //$uploaddir = './assets/price/';    // 2 способ - относительный путь
                // используем методы, предоставленные CodeIgniter
                $file_object = $this->request->getFile('xls'); // получаем CodeIgniter\HTTP\Files\UploadedFile Object
                if ($file_object->isValid() && !$file_object->hasMoved()) {
                    $file_object->move($uploaddir, $name = null, true); // сохраняем старое название и делаем перезапись			
                    $file = $uploaddir . $file_object->getName(); // получаем шаш загруженный файл

                    // передаем наш файл и конфигурационный массив во вспомогательную библиотеку Xls метод upload, котрый преобразует xls документ в массив данных, обработает специальным образом и запишет в таблицу part бд                    
                    $query = Xls::upload($file, $cells);
                    $data['query'] = $query;

                    if (isset($query)) {
                        $data['upload_status'] = true;
                        return $this->response->setJSON($data);
                    } else {
                        $data['upload_status'] = false;
                        return $this->response->setJSON($data);
                    }
                }
            }
            return $this->response->setJSON($data);
        }
    }
    // Старт загрузки картинок - поиск необходимой part
    public function searchForEdit()
    {
        if ($this->request->is('ajax')) {
            $rules = [
                'textSearch' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'Поле поиска не должно быть пустым'
                    ]
                ]
            ];            
            $validation = $this->validate($rules);
            $data['valid_status'] = $validation;
            $data['search_status'] = false;
            if (!$validation) {
                $validation_object = $this->validator;
                $data['search_valid_message'] = display_error($validation_object, 'textSearch');
                return $this->response->setJSON($data);
            } else {
                $data['search_valid_message'] = false;
                $match = $this->request->getPost('textSearch');
                $match = (string)$match;
                $data['match'] = $match;
                $limit = 1000;
                $parts_search_count = $this->builder->like('dev_brand', "%{$match}%")->orLike('dev_model', "%{$match}%")->orLike('part_name', "%{$match}%")->orLike('part_kind', "%{$match}%")->orLike('part_sub_kind', "%{$match}%")->orLike('part_slug', "%{$match}%")->orLike('part_desc', "%{$match}%")->orLike('part_param', "%{$match}%")->countAllResults();
                $parts_search_res = $this->builder->like('dev_brand', "%{$match}%")->orLike('dev_model', "%{$match}%")->orLike('part_name', "%{$match}%")->orLike('part_kind', "%{$match}%")->orLike('part_sub_kind', "%{$match}%")->orLike('part_slug', "%{$match}%")->orLike('part_desc', "%{$match}%")->orLike('part_param', "%{$match}%")->limit($limit)->get()->getResultArray();
                $parts_search_count = $parts_search_count;
                $data['parts_search_res'] = json_encode($parts_search_res);
                if ($parts_search_count && $data['parts_search_res']) {
                    if ($limit >= $parts_search_count) {
                        $data['search_status'] = true;
                        $data['search_message'] = "<div class='alert alert-success'>Получено {$parts_search_count} результатов</div>";
                    } else {
                        $data['search_status'] = true;
                        $data['search_message'] = "<div class='alert alert-warning'>Показано только {$limit} результатов из {$parts_search_count}, задайте более точные условия поиска</div>";
                    }
                } else {
                    $data['search_status'] = false;
                    $data['search_message'] = "<div class='alert alert-danger'>Поиск не дал результатов</div>";
                }
                return $this->response->setJSON($data);
            }
        }
    }
    // Страница загрузки картинок для конкретной модели
    public function partForImg($slug)
    {
        $part_in = $this->builder->select('part_h, part_first_img, part_sec_img, part_slug')->where('part_slug', $slug)->get()->getResultArray();        
        $data['part'] = $part_in[0];
        return view('admin/partForImg', $data);
    }
    // Страница подтверждения конкретной модели 
    public function partForDelete($slug)
    {   
        $part_in = $this->builder->where('part_slug', $slug)->get()->getResultArray();
        $data['part'] = $part_in[0];              
        return view('admin/deletePart', $data);       
    }
    // Контроллер самого процесса загрузки фото
    public function getPhoto()
    {
        if ($this->request->is('ajax')) {
            $rules = [
                'photo1' => [
                    'rules' => 'uploaded[photo1]',
                    'errors' => [
                        'uploaded' => 'Фото должно быть сделано'
                    ]
                ],
            ];
            $validation = $this->validate($rules);
            $data['valid_status'] = $validation;
            $data['upload_status'] = false;
            if (!$validation) {
                $validation_object = $this->validator;
                $data['photo1_valid'] = display_error($validation_object, 'photo1');
            } else {
                $data['photo1_valid'] = false;
                $image = \Config\Services::image();
                // $image = service('image'); 
                $photos_object = $this->request->getFiles();
                $photo1_object = $photos_object['photo1'];
                $photo2_object = $photos_object['photo2'];
                $slug = $this->request->getVar('slug');
                $data['slug'] = $slug;
                $upload_dir = ROOTPATH . "public/assets/img/parts/$slug";
                $thumb_dir = $upload_dir . '/thumbnails';
                $names_old = $this->builder->select('part_first_img, part_sec_img')->where(['part_slug' => $slug])->get()->getResultArray();
                $name1_old = $names_old[0]['part_first_img'] ?? '';
                $name2_old = $names_old[0]['part_sec_img'] ?? '';
                // Обработка photo1
                if ($photo1_object->isValid() && !$photo1_object->hasMoved()) {
                    $filename1_temp = 'file1_temp'; // назначаем временное название                    
                    $photo1_object->move($upload_dir, $filename1_temp . '.jpg', true); // создаем картинку под временным названием
                    if (!is_dir($thumb_dir)) {
                        mkdir($thumb_dir); // создаем директорию под миниатюры 
                    }                   
                    $photo1_temp = $upload_dir . '/' . $filename1_temp . '.jpg'; // собственно временная картинка
                    $filename1 = getRandomFileName($upload_dir, 'jpg'); // получаем будующее название файлы
                    $photo1 = $upload_dir . '/' . $filename1 . '.jpg'; // будующая основная картинка
                    $photo1_thumb = $thumb_dir . '/' . $filename1 . '.jpg'; // будующая миниатюра                  
                }                             
                // Обрабатываем временное фото, если оно с iphone
                $photo1_exif = exif_read_data($photo1_temp);
                $photo1_orient = $photo1_exif['Orientation'] ?? false;
                if (!empty($photo1_orient)) {
                    switch ($photo1_orient) {
                        case 8:
                            $image->withFile($photo1_temp)->rotate(90)->save($photo1_temp);
                            break;
                        case 3:
                            $image->withFile($photo1_temp)->rotate(180)->save($photo1_temp);
                            break;
                        case 6:
                            $image->withFile($photo1_temp)->rotate(270)->save($photo1_temp);
                            break;
                    }                                
                }
                imageresize($photo1, $photo1_temp, 1500, 2000, 100); // получаем слегка уменьшенное photo1                
                imageresize($photo1_thumb, $photo1_temp, 360, 480, 100); // получаем миниатюру                   
                unlink($photo1_temp); // удаляем временное фото
                $photo1_res = Webp::create($photo1, 80); // преобразуем основное изображение в webp
                $photo1_thumb_res = Webp::create($photo1_thumb, 10); // преобразуем миниатюру в webp
                unlink($photo1); // удаляем основное фото в формате jpg
                unlink($photo1_thumb); // удаляем миниатюру в формате jpg
                // Обработка photo2
                if ($photo2_object->isValid() && !$photo2_object->hasMoved()) {
                    $filename2_temp = 'file2_temp'; // назначаем временное название                    
                    $photo2_object->move($upload_dir, $filename2_temp . '.jpg', true); // создаем картинку под временным названием       
                    $photo2_temp = $upload_dir . '/' . $filename2_temp . '.jpg'; // собственно временная картинка
                    $filename2 = getRandomFileName($upload_dir, 'jpg'); // получаем будующее название файлы
                    $photo2 = $upload_dir . '/' . $filename2 . '.jpg'; // будующая основная картинка
                    $photo2_thumb = $thumb_dir . '/' . $filename2 . '.jpg'; // будующая миниатюра    
                   
                    // Обрабатываем фото, если оно с iphone
                    $photo2_exif = exif_read_data($photo2_temp);
                    $photo2_orient = $photo2_exif['Orientation'] ?? false;
                    if (!empty($photo2_orient)) {
                        switch ($photo2_orient) {
                            case 8:
                                $image->withFile($photo2_temp)->rotate(90)->save($photo2_temp);
                                break;
                            case 3:
                                $image->withFile($photo2_temp)->rotate(180)->save($photo2_temp);
                                break;
                            case 6:
                                $image->withFile($photo2_temp)->rotate(270)->save($photo2_temp);
                                break;
                        }
                    }
                    imageresize($photo2, $photo2_temp, 1500, 2000, 100); // получаем слегка уменьшенное photo2                
                    imageresize($photo2_thumb, $photo2_temp, 360, 480, 100); // получаем миниатюру                   
                    unlink($photo2_temp); // удаляем временное фото
                    $photo2_res = Webp::create($photo2, 80); // преобразуем основное изображение в webp
                    $photo2_thumb_res = Webp::create($photo2_thumb, 10); // преобразуем миниатюру в webp
                    unlink($photo2); // удаляем основное фото в формате jpg
                    unlink($photo2_thumb); // удаляем миниатюру в формате jpg                 
                } else {
                    $filename2 = '';
                    $photo2_res = true;
                }
                $file1_old = $upload_dir . '/' . $name1_old . '.webp';
                $file1_old_thumb = $upload_dir . '/thumbnails/' . $name1_old . '.webp';
                $file2_old = $upload_dir . '/' . $name2_old . '.webp';
                $file2_old_thumb = $upload_dir . '/thumbnails/' . $name2_old . '.webp';

                if (file_exists($file1_old)) {
                    unlink($file1_old);
                    unlink($file1_old_thumb);
                }
                if (file_exists($file2_old)) {
                    unlink($file2_old);
                    unlink($file2_old_thumb);
                }
                $this->builder->set(['part_first_img' => $filename1, 'part_sec_img' => $filename2])->where(['part_slug' => $slug])->update();
                if ($photo2_res && $photo1_res) {
                    $data['upload_status'] = true;
                } else {
                    $data['upload_status'] = false;
                }
            }
            return $this->response->setJSON($data);
        }
    }
    //  Обработка данных при удалении part
    public function deletePart()
    {
        if ($this->request->is('ajax')) {            
            $action = $this->request->getPost('action');
            $id = $this->request->getPost('id');           
            $data['action'] = $action;
            $data['id'] = $id;
            $query = $this->builder->where(['id' => $id])->delete();
                if (!$query) {
                    $data['response_fail'] = 'Что-то пошло не так. Удаление не удалась';
                } else {
                    $data['response_successe'] = 'Удаление завершено';
                }           
            return $this->response->setJSON($data);
        }
    } 
    // Аутентификация администратора
    public function auth()
    {
        $data = [];
        if ($this->request->getPost()) {
            $rules = [
                'name' => [
                    'rules' => 'required|is_not_unique[admins.name]',
                    'errors' => [
                        'required' => "Поле \"Логин\" обязательно для заполнения",
                        'is_not_unique' => 'Данный логин не зарегистрирован'
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[5]|max_length[12]',
                    'errors' => [
                        'required' => "Поле \"Пароль\" обязательно для заполнения",
                        'min_length' => 'Пароль должен содержать минимум 5 знаков',
                        'max_length' => 'Пароль должен содержать максимум 12 знаков'
                    ]
                ],
            ];
            $validation = $this->validate($rules);
            if (!$validation) {
                $data['validation_object'] = $this->validator;
            } else {
                // Принимаем пост-данные из формы
                $name = $this->request->getPost('name');
                $password =  $this->request->getPost('password');
                $builder = $this->db->table('admins');    // получаем экземпляр класса Query Builder				
                $admin_info = $builder->where('name', $name)->limit(1)->get()->getResultArray(); // Обращаемся к бд и получаем инфу о первом попавшемся (он должен быть единственным) пользователе с данным name	
                $check_password = Hash::check($password, $admin_info[0]['password']);
                if (!$check_password) {
                    return redirect()->back()->with('fail', 'Введен неправильный пароль');
                } else {
                    $admin_id = $admin_info[0]['id']; // Получаем id администратора
                    session()->set('loggedAdmin', $admin_id); // записываем в сессию id администратора
                    return redirect()->to('/dimon'); // переходим в админпанель                
                }
            }
        }
        return view('admin/auth', $data);
    }
    // Регистрация администратора, работаем через ajax-запрос без формы, используем штатный валидатор от codeigniter (helper form), используем один экшн и для вывода страницы и для обработки данных
    public function register()
    {
        if ($this->request->is('ajax')) {
            // прописываем правила валидации
            $rules = [
                'login' => [
                    'rules' => 'required|is_unique[admins.name]',
                    'errors' => [
                        'required' => "Поле \"Логин\" обязательно для заполнения",
                        'is_unique' => "Данный логин уже используется"
                    ]
                ],
                'password' => [
                    'rules' => 'required|min_length[5]|max_length[12]',
                    'errors' => [
                        'required' => "Поле \"Пароль\" обязательно для заполнения",
                        'min_length' => 'Пароль должен содержать минимум 5 знаков',
                        'max_length' => 'Пароль должен содержать максимум 12 знаков'
                    ]
                ],
                'cpassword' => [
                    'rules' => 'required|min_length[5]|max_length[12]|matches[password]',
                    'errors' => [
                        'required' => "Поле \"Подтвердите пароль\" обязательно для заполнения",
                        'min_length' => 'Пароль должен содержать минимум 5 знаков',
                        'max_length' => 'Пароль должен содержать максимум 12 знаков',
                        'matches' => 'Пароль не соответствует ранее введенному'
                    ]
                ]

            ];
            $validation = $this->validate($rules); //bool
            $data['status_valid'] = $validation;
            if (!$validation) {
                $validation_object = $this->validator;
                $data['login_valid'] = display_error($validation_object, 'login');
                $data['password_valid'] = display_error($validation_object, 'password');
                $data['cpassword_valid'] = display_error($validation_object, 'cpassword');
                return $this->response->setJSON($data);
            } else {
                $name = $this->request->getPost('login');
                $password =  $this->request->getPost('password');

                //Формируем данные для отправки
                $values = [
                    'name' => $name,
                    'password' => Hash::make($password) // Пароль предварительно хешируем кастомной функцией из библиотеки
                ];
                $builder = $this->db->table('admins');

                $query = $builder->insert($values);
                if (!$query) {
                    $data['response_fail'] = 'Что-то пошло не так. Регистрация не удалась';
                    return $this->response->setJSON($data);
                } else {
                    $data['response_successe'] = 'Новый администратор успешно зарегистрирован';
                    return $this->response->setJSON($data);
                }
            }
        }
        return view('admin/register');
    }
}
