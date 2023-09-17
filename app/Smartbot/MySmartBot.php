<?php 
namespace App\Smartbot;
use DateTimeZone;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Utils;
use App\Libraries\SmartSearch;
use App\Libraries\GetParts;

class MySmartBot extends MyBotCore
{   
  private $location = 'Europe/Minsk';
  private $date_format = 'Y-m-d H:i:s';
  private $date_time_zone_obj;
  private $get_parts;
  private $smart_search;

  public function __construct($token, $host_bot, $data = [])
  {     
    $db =  \Config\Database::connect();     
    $builder_bot = $db->table('users_bot_sessions');
    $this->date_time_zone_obj = new DateTimeZone($this->location); 
    parent::__construct($token, $host_bot, $data,  $builder_bot);
    $this->get_parts = new GetParts(); 
    $this->smart_search = new SmartSearch();       
  }
  public function inputData()
  {     
    $this->preSelector(); 
    $this->getSessionData();     
    $this->actionSelect();    
  }
  private function actionSelect()
  {            
    try {
      switch (true) {
        case $this->update_obj->messageIsset():
          switch (true) {
            case $this->update_obj->text() == '/start':
              if ($this->action_data['session_isset']) {
                try {
                  $this->delPrevScreen();
                } catch (\Throwable $e) {
                  $this->actionErrorLogDev($e);
                }                
                $this->delSessionData();                
                $this->action_data['session_isset'] = false;
                $this->action_data['screens_data'] = [];
                $this->action_data['current_screen_name'] = '';                             
              }
              $this->actionStart();              
              try {
                $this->delCurrentUserMessage();
              } catch (\Throwable $e) {
                $this->actionErrorLogDev($e);
              }             
              break;
            case $this->update_obj->text() == '/exit':
              if ($this->action_data['session_isset']) {                
                try {
                  $this->delPrevScreen();
                } catch (\Throwable $e) {
                  $this->actionErrorLogDev($e);
                }
                $this->delSessionData();                
              } 
              try {
                $this->delCurrentUserMessage();
              } catch (\Throwable $e) {
                $this->actionErrorLogDev($e);
              }           
              die;
              break;
            case $this->action_data['current_screen_name'] == 'direct-search-start':
              $this->actionDirectSearchRes();              
              try {
                $this->delCurrentUserMessage();
              } catch (\Throwable $e) {
                $this->actionErrorLogDev($e);
              }
              break;
            case $this->action_data['current_screen_name'] == 'smart-search-match':
              $this->actionSmartSearchHandler();              
              try {
                $this->delCurrentUserMessage();
              } catch (\Throwable $e) {
                $this->actionErrorLogDev($e);
              }
              break;            
            default:              
              try {
                $this->delCurrentUserMessage();
              } catch (\Throwable $e) {
                $this->actionErrorLogDev($e);
              }
              die;
              break;
          }         
          break;         
        case $this->update_obj->callbackIsset():         
          switch (true) {
            case $this->update_obj->callbackData() == 'rollback':
              $this->actionRollback();
              break;
            case $this->update_obj->callbackData() == 'exit':
              if ($this->action_data['session_isset']) {                
                try {
                  $this->delPrevScreen();
                } catch (\Throwable $e) {
                  $this->actionErrorLogDev($e);
                }
                $this->delSessionData();                
              }
              die;
              break;
            case $this->update_obj->callbackData() == 'start':
              $this->actionStart();
              if ($this->action_data['session_isset']) {                
                try {
                  $this->delPrevScreen();
                } catch (\Throwable $e) {
                  $this->actionErrorLogDev($e);
                }
                $this->delSessionData();                              
                $this->action_data['session_isset'] = false;
                $this->action_data['screens_data'] = [];
                $this->action_data['current_screen_name'] = '';                             
              }
              break;
            case $this->update_obj->callbackData() == 'show-part-direct':
              $this->actionShowPartDirect();
              break;
            case $this->update_obj->callbackData() == 'direct-search-start':                 
              $this->actionDirectSearchStart();
              break;
            case $this->update_obj->callbackData() == 'smart-search-start':                 
              $this->actionSmartSearchStart();
              break;
            case $this->update_obj->callbackData() == 'smart-search-match':                 
              $this->actionSmartSearchMatch();
              break;
            case $this->update_obj->callbackData() == 'smart-search-handler':                 
              $this->actionSmartSearchHandler();
              break;           
            case $this->update_obj->callbackData() == 'dev-models':                 
              $this->actionDevModels();
              break;
            case $this->update_obj->callbackData() == 'show-part-smart':
              $this->actionShowPartSmart();
              break;
            case $this->update_obj->callbackData() == 'show-parts':                 
              $this->actionShowParts();
              break;           
            default:
              $text = 'Прилетел неизвестный каллбек';
              throw new UpdateException($text);              
              break;
          }         
          break;         
        default:          
          if ($this->update_obj->otherUpdateTypesIsset()) {
            die;
          } else {
            writeLogFile($this->data);
            throw new UpdateException('Неизвестный тип апдейта: не входит в other_update_types');
          }          
          break;
      }
    } catch (UpdateException $e) {
      writeLogFile($this->data);
      $this->actionErrorMessageDev($e);
      $this->actionErrorMessageUser();
      die;
    }
    if ($this->action_data['session_isset']) {       
      try {
        $this->delPrevScreen();
      } catch (ClientException $e) {                 
        writeLogFile($this->data);
        $this->actionErrorMessageDev($e);                
      }     
    }
    $this->setSessionData();   
  }

  private function actionStart()
  {    
    $screen_messages_id = [];    
    $user_id = $this->action_data['user_id'];
    $user_name = $this->action_data['user_name'];
    $keyboard = [[$this->callbackButton('Прямой поиск', 'direct-search-start')], [$this->callbackButton('Умный поиск', 'smart-search-start')]];
    $text = "Здравствуйте, {$user_name}!\nЕсли вы точно знаете модель мобильного устройства и имеете опыт пользования ботом, выбирайте \"Прямой поиск\"\nВ противном случае рекомендуется пользоваться \"Умным поиском\"";
    $query_data = [
      'chat_id' => $user_id,
      'text' => $text,
      "parse_mode" => "html", 
      "reply_markup" => json_encode(['inline_keyboard' => $keyboard,]),      
    ];    
    try {
      $res = $this->sendMessage($query_data);
    } catch (\Throwable $e) {
      $this->actionErrorMessageDev($e);
      $this->actionErrorMessageUser();
    }
    $out_message_id = $this->getOutMessageIdHelper($res);              
    array_push($screen_messages_id, $out_message_id);  
    $this->action_data['next_screen_name'] = 'start';    
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;    
  }
  private function actionDirectSearchStart()
  {
    $screen_messages_id = [];
    $user_id = $this->action_data['user_id'];
    $keyboard = [[$this->callbackButton('В начало', 'start')]];
    $text = "<b>Введите латиницей ключевое слово для поиска необходимой детали</b>";
    $query_data = [
      'chat_id' => $user_id,
      'text' => $text,
      "parse_mode" => "html",
      "reply_markup" => json_encode(['inline_keyboard' => $keyboard,])
    ];   
    try {
      $res = $this->sendMessage($query_data);
    } catch (\Throwable $e) {
      $this->actionErrorMessageDev($e);
      $this->actionErrorMessageUser();
      die;
    }
    $out_message_id = $this->getOutMessageIdHelper($res);              
    array_push($screen_messages_id, $out_message_id);
    $this->action_data['next_screen_name'] = 'direct-search-start';
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;    
  }
  private function actionDirectSearchRes()
  {
    $screen_messages_id = [];
    $rollback_data = [];
    $user_id = $this->update_obj->userId();
    $match = $this->update_obj->text();
    $search_res = $this->smart_search->getAllPartsForBot($match, 5);
    $arr_data = $search_res[0];
    $search_res_message = $search_res[1];
    $action_message = $search_res[2]; 
    $count = $search_res[3];
    if ($count != 0) {      
      foreach ($arr_data as $key => &$item) {
        $keyboard = [[$this->callbackButton('Просмотреть', "show-part-direct_{$item['part_id']}")]];
        $query_data = [
          "chat_id" => $user_id,
          "text" => $item['title_for_bot'],
          "parse_mode" => "html", 
          "reply_markup" => json_encode(['inline_keyboard' => $keyboard,])
        ];
        $rollback_data_item = [];
        $tg_method = 'sendMessage';
        try {
          $res = $this->$tg_method($query_data);
        } catch (\Throwable $e) {
          $this->actionErrorMessageDev($e);
          $this->actionErrorMessageUser();
          die;
        }
        $out_message_id = $this->getOutMessageIdHelper($res);
        array_push($screen_messages_id, $out_message_id);
        array_push($rollback_data_item, $tg_method, $query_data);
        array_push($rollback_data, $rollback_data_item); 
        
        if ($key == ($count - 1)) {
          $rollback_data_item = [];
          $keyboard = [[$this->callbackButton('Назад', 'direct-search-start'), $this->button_start,]];
          $query_data = [
            "chat_id" => $user_id,
            "text" => $search_res_message . PHP_EOL . $action_message, 
            "parse_mode" => "html",       
            "reply_markup" => json_encode(['inline_keyboard' => $keyboard,])
          ];          
          $tg_method = 'sendMessage';
          try {
            $res = $this->$tg_method($query_data);
          } catch (\Throwable $e) {
            $this->actionErrorMessageDev($e);
            $this->actionErrorMessageUser();
            die;
          }
          $out_message_id = $this->getOutMessageIdHelper($res);
          array_push($screen_messages_id, $out_message_id);
          array_push($rollback_data_item, $tg_method, $query_data);
          array_push($rollback_data, $rollback_data_item);
        } 
      }
      unset($item); 
          
    } else {
      $keyboard = [[$this->callbackButton('Назад', 'direct-search-start')], [$this->button_start],];
      $rollback_data_item = [];
        $query_data = [
          "chat_id" => $user_id,
          "text" => $search_res_message . PHP_EOL . $action_message,
          "parse_mode" => "html", 
          "reply_markup" => json_encode(['inline_keyboard' => $keyboard,])
        ];
        $tg_method = 'sendMessage';
        try {
          $res = $this->$tg_method($query_data);
        } catch (\Throwable $e) {
          $this->actionErrorMessageDev($e);
          $this->actionErrorMessageUser();
          die;
        }
        $out_message_id = $this->getOutMessageIdHelper($res);
        array_push($screen_messages_id, $out_message_id);
        array_push($rollback_data_item, $tg_method, $query_data);
        array_push($rollback_data, $rollback_data_item);
    }
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
    $this->action_data['next_screen_name'] = 'direct-search-res';
    $this->action_data['next_screen_source'] = $match;
    $this->action_data['next_screen_rollback_data'] = $rollback_data;
  }
  private function actionShowPartDirect()
  {
    $screen_messages_id = [];
    $current_screen_name = $this->action_data['current_screen_name'];
    $user_id = $this->action_data['user_id'];
    $part_id = $this->update_obj->marker();

    $arr_data_img = $this->smart_search->getImgForBot($part_id) ?? [];
    $first_img = $arr_data_img[0] ?? '';
    // $first_img_name = $arr_data_img[1] ?? '';
    $first_img_isset = !empty($first_img);

    $arr_data_part = $this->smart_search->getPartForBot($part_id);
    $title = $arr_data_part[0];
    $price = $arr_data_part[1];
    $desc = $arr_data_part[2];

    if (!empty($desc)) {
      $description = PHP_EOL . $desc . PHP_EOL;
    } else {
      $description = '';
    }

    $page_global_info = $arr_data_part[3];
    $keyboard_down = [[$this->rollbackButton($current_screen_name), $this->button_start, $this->button_exit]];

    if ($first_img_isset) {
      $message = $title . PHP_EOL . PHP_EOL . $price;
      $query_data = [      
        "chat_id" => $user_id,
        "text" => $message, 
        "parse_mode" => "html",                                 
      ];
      try {
        $res = $this->sendMessage($query_data);
      } catch (\Throwable $e) {
        $this->actionErrorMessageDev($e);
        $this->actionErrorMessageUser();
        die;
      }
      $out_message_id = $this->getOutMessageIdHelper($res);
      array_push($screen_messages_id, $out_message_id);
      $file_stream = Utils::tryFopen($first_img, 'r');
      $caption = $description . PHP_EOL . $page_global_info;
      $query_data = [
        "chat_id" => $user_id,
        'photo' => $file_stream,
        "parse_mode" => "html",
        'caption' => $caption,
        "reply_markup" => json_encode(['inline_keyboard' => $keyboard_down])
      ];    
      try {
        $res = $this->sendPhoto($query_data);
      } catch (\Throwable $e) {
        $this->actionErrorMessageDev($e);
        $this->actionErrorMessageUser();
        die;
      }
      $out_message_id = $this->getOutMessageIdHelper($res);
      array_push($screen_messages_id, $out_message_id);

    } else {
      $message = $title . PHP_EOL . PHP_EOL . $price . PHP_EOL .  $description . PHP_EOL . $page_global_info;
      $query_data = [      
        "chat_id" => $user_id,
        "text" => $message, 
        "parse_mode" => "html", 
        "reply_markup" => json_encode(['inline_keyboard' => $keyboard_down])                        
      ];
      try {
        $res = $this->sendMessage($query_data);
      } catch (\Throwable $e) {
        $this->actionErrorMessageDev($e);
        $this->actionErrorMessageUser();
        die;
      }
      $out_message_id = $this->getOutMessageIdHelper($res);
      array_push($screen_messages_id, $out_message_id);
    }
    $this->action_data['next_screen_name'] = 'show-part-direct';
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
  }
  private function actionSmartSearchStart()
  {
    $screen_messages_id = [];
    $user_id = $this->action_data['user_id'];
    $user_name = $this->action_data['user_name'];    
    $part_kinds = $this->get_parts->getPartKinds();
    $arr_data = [];
    foreach ($part_kinds as &$value) { 
        $item = [];                       
        array_push($item, $value['for_menu'], "smart-search-match_{$value['part_kind']}");
        array_push($arr_data, $item);
    }
    unset($value);
    $keyboard = $this->standartKeyboard(2, $arr_data);    
    $keyboard_row_down = [$this->button_start];
    array_push($keyboard, $keyboard_row_down);
    $query_data = [
      "chat_id" => $user_id,
      "text" => "Здравствуйте, {$user_name}!\nДля быстрого поиска необходимой вам детали выберите вид, к которому она относится.", 
      "parse_mode" => "html", 
      "reply_markup" => json_encode(['inline_keyboard' => $keyboard,])                        
    ];
    try {
      $res = $this->sendMessage($query_data);
    } catch (\Throwable $e) {
      $this->actionErrorMessageDev($e);
      $this->actionErrorMessageUser();
      die;
    }
    $out_message_id = $this->getOutMessageIdHelper($res);
    array_push($screen_messages_id, $out_message_id);
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
    $this->action_data['next_screen_name'] = 'smart-search-start';
  }
  private function actionSmartSearchMatch()
  {
    $screen_messages_id = [];
    $rollback_data = [];
    $user_id = $this->action_data['user_id'];
    $part_kind = $this->update_obj->marker();
    $aliases = aliases();
    $keyboard_down = [[$this->callbackButton('Назад', 'smart-search-start'), $this->callbackButton('Далее', 'smart-search-handler')]];
    $query_data = [
      "chat_id" => $user_id,
      "text" => "Вы выбрали категорию \"{$aliases['part_kinds'][$part_kind]['for_menu']}\". Отправьте в сообщении ключевое слово или фразу для более быстрого поиска или просто нажмите \"далее\"", 
      "parse_mode" => "html", 
      "reply_markup" => json_encode(['inline_keyboard' => $keyboard_down,])                        
    ];
    $tg_method = 'sendMessage';
    $rollback_data_item = [];
    array_push($rollback_data_item, $tg_method, $query_data);
    try {
      $res = $this->$tg_method($query_data);
    } catch (\Throwable $e) {
      $this->actionErrorMessageDev($e);
      $this->actionErrorMessageUser();
      die;
    }
    $out_message_id = $this->getOutMessageIdHelper($res);
    array_push($screen_messages_id, $out_message_id);
    array_push($rollback_data, $rollback_data_item);
    $this->action_data['next_screen_name'] = 'smart-search-match';
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
    $this->action_data['next_screen_rollback_data'] = $rollback_data;
    $this->action_data['next_screen_source'] = $part_kind;
  }
  private function actionSmartSearchHandler()
  {
    $screen_messages_id = [];
    $rollback_data = [];
    $user_id = $this->action_data['user_id'];
    $match = $this->update_obj->text() ?? '';
    $part_kind = $this->getCurrentScreenSourceHelper();    
    $limit = 10; // кол-во parts, выводимых напрямую
    $result = $this->smart_search->Stage1($part_kind, $match, $limit);
    $action_order = $result[0];
    $result_data = $result[1];   
    $search_res_message = $result[2];
    $action_message = $result[3];
    $arr_data = [];
    foreach ($result_data as $value) {
      $item = [];
      switch ($action_order) {
        case 1: // Выводим скрин с кнопками (dev_brands)
          $next_screen_name = 'dev-brands'; 
          array_push($item, $value['dev_brand'], "dev-models_{$value['dev_brand']}");          
          break;
        case 2: // Выводим скрин с кнопками (models напрямую)
          $next_screen_name = 'dev-models';
          array_push($item, $value['dev_model'], "show-part-smart_{$value['id']}");
          break;
        case 3: // Выводим скрин с нопками (part_sub_kinds)
          $next_screen_name = 'part-sub-kinds';
          array_push($item, $value['title'], "show-parts_{$value['part_sub_kind']}");
          break;
        case 4: // parts напрямую
          $next_screen_name = 'show-parts';
          array_push($item, $value['part_name'], "show-part-smart_{$value['id']}");
          break;       
      }
      array_push($arr_data, $item);
    }
    unset($value);
    $keyboard = $this->standartKeyboard(2, $arr_data);    
    $keyboard_row_down = [$this->rollbackButton('smart-search-match'), $this->button_start];
    array_push($keyboard, $keyboard_row_down);
    $query_data = [      
      "chat_id" => $user_id,
      "text" => "<i>$search_res_message</i>\n<i>$action_message</i>", 
      "parse_mode" => "html", 
      "reply_markup" => json_encode(['inline_keyboard' => $keyboard,])                        
    ]; 
    $tg_method = 'sendMessage';
    $rollback_data_item = [];
    array_push($rollback_data_item, $tg_method, $query_data);
    try {
      $res = $this->$tg_method($query_data);
    } catch (\Throwable $e) {
      $this->actionErrorMessageDev($e);
      $this->actionErrorMessageUser();
      die;
    }
    $out_message_id = $this->getOutMessageIdHelper($res);
    array_push($screen_messages_id, $out_message_id);
    array_push($rollback_data, $rollback_data_item);
    $this->action_data['next_screen_name'] = $next_screen_name;
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
    $this->action_data['next_screen_rollback_data'] = $rollback_data;
    $this->action_data['next_screen_source'] = $part_kind;
  }
  private function actionDevModels()
  {
    $screen_messages_id = [];
    $rollback_data = [];
    $next_screen_name = 'dev-models';
    $user_id = $this->action_data['user_id'];
    $dev_brand = $this->update_obj->marker();   
    $part_kind = $this->getCurrentScreenSourceHelper();   
    $limit = 88;
    $result = $this->smart_search->getBrandModel($part_kind, $dev_brand, $limit);   
    $result_data = $result[0][0];   
    $search_res_message = $result[1];
    $action_message = $result[2];
    $arr_data = [];
    foreach ($result_data as &$value) {
      $part_id = $value['id'];      
      $item = [];      
      array_push($item, $value['dev_model'], "show-part-smart_{$part_id}");
      array_push($arr_data, $item);      
    }
    unset($value);    
    $keyboard = $this->standartKeyboard(2, $arr_data);
    $keyboard_row_down = [$this->rollbackButton('dev-brands'), $this->button_start];
    array_push($keyboard, $keyboard_row_down);    
    $query_data = [      
      "chat_id" => $user_id,
      "text" => "<i>$search_res_message</i>\n<i>$action_message</i>",      
      "parse_mode" => "html", 
      "reply_markup" => json_encode(['inline_keyboard' => $keyboard])                        
    ];    
    $tg_method = 'sendMessage';
    $rollback_data_item = [];
    array_push($rollback_data_item, $tg_method, $query_data);
    try {
      $res = $this->$tg_method($query_data);
    } catch (\Throwable $e) {
      $this->actionErrorMessageDev($e);
      $this->actionErrorMessageUser();
      die;
    }
    $out_message_id = $this->getOutMessageIdHelper($res);
    array_push($screen_messages_id, $out_message_id);
    array_push($rollback_data, $rollback_data_item);
    $this->action_data['next_screen_name'] = $next_screen_name;
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
    $this->action_data['next_screen_rollback_data'] = $rollback_data;
  } 
  private function actionShowParts()
  {
    $screen_messages_id = [];
    $rollback_data = [];
    $current_screen_name = $this->action_data['current_screen_name'];    
    $user_id = $this->action_data['user_id'];
    $part_sub_kind = $this->update_obj->marker();
    $part_kind = $this->getCurrentScreenSourceHelper();
    $limit = 88;
    $result = $this->smart_search->getSubKindParts($part_kind, $part_sub_kind, $limit);
    $result_data = $result[0][0];
    $search_res_message = $result[1];
    $action_message = $result[2];
    $arr_data = [];
    foreach ($result_data as &$value) {
      $item = [];                       
      array_push($item, $value['part_name'], "show-part-smart_{$value['id']}");
      array_push($arr_data, $item);      
    }
    unset($value);
    $keyboard = $this->standartKeyboard(2, $arr_data);    
    $keyboard_row_down = [$this->rollbackButton($current_screen_name), $this->button_start];
    array_push($keyboard, $keyboard_row_down);    
    $query_data = [      
      "chat_id" => $user_id,
      "text" => "<i>$search_res_message</i>\n<i>$action_message</i>", 
      "parse_mode" => "html", 
      "reply_markup" => json_encode(['inline_keyboard' => $keyboard])                        
    ];    
    $rollback_data_item = [];
    $tg_method = 'sendMessage';
    array_push($rollback_data_item, $tg_method, $query_data);
    array_push($rollback_data, $rollback_data_item);
    try {
      $res = $this->$tg_method($query_data);
    } catch (\Throwable $e) {
      $this->actionErrorMessageDev($e);
      $this->actionErrorMessageUser();
      die;
    }
    $out_message_id = $this->getOutMessageIdHelper($res);
    array_push($screen_messages_id, $out_message_id);    
    $this->action_data['next_screen_name'] = 'show-parts';
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
    $this->action_data['next_screen_rollback_data'] = $rollback_data;
  }
  private function actionShowPartSmart()
  {
    $screen_messages_id = [];
    $current_screen_name = $this->action_data['current_screen_name'];
    $user_id = $this->action_data['user_id'];
    $part_id = $this->update_obj->marker();
    $arr_data_img = $this->smart_search->getImgForBot($part_id) ?? [];
    $first_img = $arr_data_img[0] ?? '';
    $first_img_name = $arr_data_img[1] ?? '';
    $first_img_isset = !empty($first_img);
    $arr_data_part = $this->smart_search->getPartForBot($part_id);
    $title = $arr_data_part[0];
    $price = $arr_data_part[1];
    $desc = $arr_data_part[2];
    if (!empty($desc)) {
      $description = PHP_EOL . $desc . PHP_EOL;
    } else {
      $description = '';
    }  
    $page_global_info = $arr_data_part[3];
    $keyboard_down = [[$this->rollbackButton($current_screen_name), $this->button_start, $this->button_exit]];
    if ($first_img_isset) {
      $message = $title . PHP_EOL . PHP_EOL . $price;
      $query_data = [      
        "chat_id" => $user_id,
        "text" => $message, 
        "parse_mode" => "html",                                 
      ];
      try {
        $res = $this->sendMessage($query_data);
      } catch (\Throwable $e) {
        $this->actionErrorMessageDev($e);
        $this->actionErrorMessageUser();
        die;
      }
      $out_message_id = $this->getOutMessageIdHelper($res);
      array_push($screen_messages_id, $out_message_id);      
      $file_stream = Utils::tryFopen($first_img, 'r');
      $caption = $description . PHP_EOL . $page_global_info;
      $query_data = [
        "chat_id" => $user_id,
        'photo' => $file_stream,
        "parse_mode" => "html",
        'caption' => $caption,
        "reply_markup" => json_encode(['inline_keyboard' => $keyboard_down])
      ];
      try {
        $res = $this->sendPhoto($query_data);
      } catch (\Throwable $e) {
        $this->actionErrorMessageDev($e);
        $this->actionErrorMessageUser();
        die;
      }
      $out_message_id = $this->getOutMessageIdHelper($res);
      array_push($screen_messages_id, $out_message_id);
    } else {
      $message = $title . PHP_EOL . PHP_EOL . $price . PHP_EOL .  $description . PHP_EOL . $page_global_info;
      $query_data = [      
        "chat_id" => $user_id,
        "text" => $message, 
        "parse_mode" => "html", 
        "reply_markup" => json_encode(['inline_keyboard' => $keyboard_down])                        
      ];
      try {
        $res = $this->sendMessage($query_data);
      } catch (\Throwable $e) {
        $this->actionErrorMessageDev($e);
        $this->actionErrorMessageUser();
        die;
      }
      $out_message_id = $this->getOutMessageIdHelper($res);
      array_push($screen_messages_id, $out_message_id);
    }
    $this->action_data['next_screen_name'] = 'show-part-smart';
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
  }
 
 
  
 
  
  

  public function delSessionsAction()
  {    
    $users_to_delete = [];
    $date_current_obj = new \DateTime('now', $this->date_time_zone_obj);           
    $users_sessions = $this->builder_bot->select('user_id, created_at, screens_data, current_screen_name')->get()->getResultArray();              
    foreach ($users_sessions as &$item) { 
      $user_id = $item['user_id'];         
      $date_created = $item['created_at']; 
      $current_screen_name = $item['current_screen_name'];
      $screens_data_json = $item['screens_data'];      
      $screens_data = json_decode($screens_data_json, true);      
      $screen_data = $screens_data[$current_screen_name];     
      $screen_messages_id = $screen_data[0];                
      $date_created_obj = new \DateTime($date_created, $this->date_time_zone_obj);
      $interval = $date_current_obj->diff($date_created_obj);     
      $days = $interval->days;
      $hours = $interval->h;
      $time_hours = $days*24 + $hours;              
      try {
        if ($time_hours > 2) {           
          foreach ($screen_messages_id as &$item) {            
          $res = $this->delAnyMessage($user_id, $item);            
          }
          array_push($users_to_delete, $user_id);         
        }        
      } catch (\Throwable $e) {        
        $this->actionErrorLogDev($e);
      }                       
    }
    unset($item);   
    if (!empty($users_to_delete)) {
      $this->builder_bot->whereIn('user_id', $users_to_delete)->delete();    
    }
    return $users_to_delete;    
  }

 
 


}