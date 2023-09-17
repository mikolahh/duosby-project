<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Main');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

// для фильтра AuthAdminCheck
$routes->group('', ['filter' => 'AuthAdmin'], function ($routes) {
    // вводим роуты, на которые будет действовать данный фильтр
    $routes->get('/dimon', 'Admin');
    $routes->get('admin/registerduosadmin', 'Admin::register');
});

$routes->get('/', 'Main::index'); // Главная страница
$routes->get('about', 'Main::about');



$routes->get('smart-bot', 'SmartBot::index');
$routes->post('smart-bot', 'SmartBot::index');

$routes->get('smart-bot/del-sessions', 'SmartBot::delSessions');
$routes->post('smart-bot/del-sessions', 'SmartBot::delSessions');



$routes->get('testbot', 'Main::testBot');
$routes->post('testbot', 'Main::testBot');      
$routes->get('deleted-pages/(:any)', 'Main::deletedPages/$1');// Обработка удаленных страниц
$routes->get('parts/(:segment)', 'Parts::part/$1'); // страница товара (part)
$routes->get('kind-1/(:segment)', 'Parts::kind1/$1'); // Обработка part_kind с kind_type = 1, вывод dev_brands для данного part_kind при навигации
$routes->post('part-kind-search', 'Main::partKindSearch'); // 1-й этап smart-search, принимаем part_kind и text_search
$routes->get('kind-2/(:segment)', 'Parts::kind2/$1'); // Обработка part_kind с kind_type = 2, вывод part_sub_kinds для данного part_kind
$routes->get('kind-3/(:segment)', 'Parts::kind3/$1');  // Обработка part_kind с kind_type = 3, вывод parts  для данного part_kind

$routes->get('brands-models/(:any)', 'Parts::brandModel/$1'); // получаем dev_models из dev_brand для определенного part_kind
$routes->get('sub-kinds-parts/(:any)', 'Parts::subKindParts/$1'); // получаем parts для данной part_sub_kind

$routes->post('brand-model', 'Main::brandModel'); // smart-search - получаем dev_models из dev_brand для определенного part_kind 
$routes->post('sub-kind-parts', 'Main::subKindParts'); // smart-search - получаем parts для данной part_sub_kind

$routes->get('dimon/', 'Admin'); // Главная страница админпанели
$routes->post('admin/getprice', 'Admin::getPrice'); // загрузка прайса
$routes->post('admin/getphoto', 'Admin::getPhoto'); // загрузка фото
$routes->post('admin/search-for-edit', 'Admin::searchForEdit'); // поиск комплектующих для загрузки картинок и редактирования

$routes->get('admin/part-for-img/(:segment)', 'Admin::partForImg/$1'); // страница загрузки картинок для конкретной детали
$routes->get('admin/part-for-delete/(:segment)', 'Admin::partForDelete/$1'); // страница подтверждения удаления конкретной детали


$routes->get('admin/auth', 'Admin::auth'); // страница аутентификации администратора
$routes->post('admin/auth', 'Admin::auth'); // обработка формы аутентификации администратора
$routes->get('admin/registerduosadmin', 'Admin::register'); // секретная страница регистрации администратора
$routes->post('admin/registerduosadmin', 'Admin::register'); // обработка данных при регистрации
$routes->post('admin/delete-part', 'Admin::deletePart'); // обработка данных при редактировании part



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
