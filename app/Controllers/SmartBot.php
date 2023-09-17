<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Smartbot\MySmartBot;
use App\Smartbot\UpdateClass;
use GuzzleHttp\Exception\ClientException;
use App\Libraries\SmartSearch;
use App\Libraries\GetParts;

class SmartBot extends BaseController
{
    private $token;
    private $host_bot; 

    public function __construct()
    {           
        $this->token = botIni()['token_bot'];
        $this->host_bot = botIni()['host_bot'];                
    } 

    public function index()
    {
        $token = $this->token;
        $host_bot = $this->host_bot;
        $my_bot = new MySmartBot($token, $host_bot);       

        // $res = $my_bot->getMe();
        // $res = $my_bot->getWebhookInfo();
        // $res = $my_bot->deleteWebHook();
        // $res = $my_bot->setWebHook();
        //  $res = $my_bot->deleteMyCommands();
        // $res = $my_bot->getMyCommands();
        // $bot_commands = [['command' => '/start', 'description' => 'Старт'], ['command' => '/exit', 'description' => 'Выход']];
        // $res = $my_bot->setMyCommands($bot_commands);
       /*  $query_data = [
            'chat_id' => 902636138,
            'document' => ROOTPATH . 'sftp.txt',
            'caption' => 'Hello Mikola'
        ]; */
        // $file_key = 'document';
        // $res = $my_bot->sendDocument($query_data, $file_key);
        // outArray($res);
        // die;
            //  $data = tgTestData()['Mikalai-start'];
            // $data = tgTestData()['Mikalai-exit'];

            //  $my_bot = new MySmartBot($token, $host_bot, $data);
            //  $my_bot->inputData();






        if ($this->request->is('post')) {
            $data = json_decode(file_get_contents('php://input'), true);                       
            if (isset($data)) {                         
              $my_bot = new MySmartBot($token, $host_bot, $data);                                                                                                 
                $my_bot->inputData();                                               
            }           
        }
        
    }
    public function delSessions()
    {     
      $token = botIni()['token_bot'];
      $host_bot = botIni()['host_bot'];                      
      $my_bot = new MySmartBot($token, $host_bot);      
      $my_bot->delSessionsAction();         
    }
}
