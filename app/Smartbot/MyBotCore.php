<?php
namespace App\Smartbot;
use App\Bots\BotApi;
use App\Bots\UpdateException;

abstract class MyBotCore extends MyBotApi
{  
  protected $data;   
  protected $action_data;  
  protected $builder_bot;

 
  public function __construct($token, $host_bot, $data = [], $builder_bot)
  {    
    $this->builder_bot = $builder_bot;           
    parent::__construct($token, $host_bot, $data);
    $this->data = $data;     
  }
  protected function preSelector()
  {    
    $this->action_data['user_id'] = $this->update_obj->userId() ?? '';
    $this->action_data['user_name'] = $this->update_obj->userName() ?? '';                
    return true;
  }
  protected function getSessionData()
  {    
    $session_isset = !empty($this->builder_bot->where(['user_id' => $this->action_data['user_id']])->countAllResults()); 
      
    if ($session_isset) {
      $res = $this->builder_bot->select('current_screen_name, screens_data')->where(['user_id' => $this->action_data['user_id']])->get()->getResultArray(); 
    
      $current_screen_name = $res[0]['current_screen_name'];
      $screens_data = json_decode($res[0]['screens_data'], true);           
    } else {
      $screens_data = [];
      $current_screen_name = '';      
    }
    $this->action_data['session_isset'] = $session_isset;    
    $this->action_data['screens_data'] = $screens_data;    
    $this->action_data['current_screen_name'] = $current_screen_name;       
  }
  protected function setSessionData()
  {
    $next_screen_data = [];
    $screens_data = $this->action_data['screens_data'];
    $user_id = $this->action_data['user_id'];
    $user_name = $this->action_data['user_name'];
    $next_screen_name = $this->action_data['next_screen_name'];
    $next_screen_messages_id = $this->action_data['next_screen_messages_id'];
    $next_screen_source = $this->action_data['next_screen_source'] ?? '';
    $next_screen_rollback_data = $this->action_data['next_screen_rollback_data'] ?? [];
    array_push($next_screen_data, $next_screen_messages_id, $next_screen_rollback_data, $next_screen_source);
    $screens_data[$next_screen_name] = $next_screen_data;    

    if ($this->action_data['session_isset']) {
      $res = $this->builder_bot->where(['user_id' => $user_id])->set(['screens_data' => json_encode($screens_data), 'current_screen_name' => $next_screen_name,])->update();
    } else {
      $res = $this->builder_bot->insert(['user_id' => $user_id, 'user_name' => $user_name, 'current_screen_name' => $next_screen_name, 'screens_data' => json_encode($screens_data),]);
    }   
    return $res;
  }
 
  protected function delSessionData()
  {
    $user_id = $this->action_data['user_id'];    
    $session_isset = $this->action_data['session_isset'];
    if ($session_isset) {
      $res = $this->builder_bot->where(['user_id' => $user_id])->delete();
    } else {
      $res = true;
    }
    return $res;
  }
  protected function delPrevScreen()
  { 
    $current_screen_name = $this->action_data['current_screen_name'];
    $screens_data = $this->action_data['screens_data'];    
    $screen_messages_id = $screens_data[$current_screen_name][0];   
    $user_id = $this->action_data['user_id'];     
    foreach ($screen_messages_id as $key => $item) {
      $item_message_id = $item;      
      $res = $this->delAnyMessage($user_id, $item_message_id);       
    }
    $res = true;
    return $res;
  }
  protected function delCurrentUserMessage()
  { 
    $res = $this->delAnyMessage($this->update_obj->chatId(), $this->update_obj->messageId());            
    return $res;    
  }
  protected function getOutMessageIdHelper($res)
  {
    $out_message_id = $res['result']['message_id'];
    return $out_message_id;
  }
  protected function actionRollback()
  {
    $screen_messages_id = []; 
    $screen_name = $this->update_obj->marker();   
    $user_id = $this->action_data['user_id'];
    $screens_data_json = $this->builder_bot->select('screens_data')->where(['user_id' => $user_id])->get()->getResultArray()[0]['screens_data'];
    $screens_data = json_decode($screens_data_json, true);
    $screen_data = $screens_data[$screen_name];
    $rollback_data = $screen_data[1];
    foreach ($rollback_data as &$item) {
      $tg_method = $item[0];
      $query_data = $item[1];     
      try {
        if (isset($item[2])) {
          $file_key = $item[2];
          $res = $this->$tg_method($query_data, $file_key);        
        } else {
          $res = $this->$tg_method($query_data);
        }
      } catch (\Throwable $e) {
        $this->actionErrorMessageDev($e);
        $this->actionErrorMessageUser();
        die;
      }
      $out_message_id = $this->getOutMessageIdHelper($res);
      array_push($screen_messages_id, $out_message_id);
    }
    unset($item);
    $this->action_data['next_screen_name'] = $screen_name;
    $this->action_data['next_screen_messages_id'] = $screen_messages_id;
    $this->action_data['next_screen_rollback_data'] = $rollback_data;
    if (isset($screen_data[2])) {
      $this->action_data['next_screen_source'] = $screen_data[2];
    } 
  }
  protected function getCurrentScreenSourceHelper()
  {
    $user_id = $this->action_data['user_id'];
    $screen_name = $this->action_data['current_screen_name'];
    $screens_data_json = $this->builder_bot->select('screens_data')->where(['user_id' => $user_id])->get()->getResultArray()[0]['screens_data'];
    $screens_data = json_decode($screens_data_json, true);
    $screen_data = $screens_data[$screen_name];
    $screen_source = $screen_data[2];
    return $screen_source;
  }
  protected function saveUserFileHelper()
  {
    $user_name = $this->action_data['user_name'];
    $user_id = $this->action_data['user_id'];
    $document_file_id = $this->update_obj->documentFileID();       
    $document_file_name = $this->update_obj->documentFileName();
    $res = $this->getFile($document_file_id);    
    $file_path = $res['result']['file_path'];            
    $file_link = self::API_FILE_URL . $this->token . '/' . $file_path;
    $dest = ROOTPATH . "/public/userFiles";          
      if (!file_exists($dest)) {
    mkdir($dest);
    }       
    $dest = $dest . "/$user_id - $user_name";
    if (!file_exists($dest)) {
    mkdir($dest);
    }
    $date = date("Y-m-d H-i-s");
    $dest = $dest . "/$date";
    if (!file_exists($dest)) {
    mkdir($dest);
    }    
    $dest = $dest . "/$document_file_name"; 
    $ch = curl_init($file_link); 
    $fp = fopen($dest, 'wb');    
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $dest;      
  }
  public function actionErrorMessageUser()
  {     
    $user_id = $this->action_data['user_id'];
    $user_name = $this->action_data['user_name'];
    $text = "<b>{$user_name}</b>, что-то пошло не так и произошла непредвиденная ошибка, очистите историю сообщений и запустите бота заново, выполнив команду  \"Старт\" в меню";     
    $query_data = [
      'text' => $text,
      'chat_id' => $user_id,
      'parse_mode' => 'html',      
    ];
    $this->delSessionData(); 
    $res = $this->sendMessage($query_data);   
  }
  
 
  
  
 
 
  
 
  
  
  

 
}