<?php
// Получение случайного имени для загружаемого файла
function getRandomFileName($path, $extension = '')
{
    $extension = $extension ? '.' . $extension : '';
    $path = $path ? $path . '/' : '';

    do {
        $name = md5(microtime() . rand(0, 9999));
        $file = $path . $name . $extension;
    } while (file_exists($file));

    return $name;
}
//   Изменяем размер изображения jpg
function imageresize($outfile,$infile,$neww,$newh,$quality) {
    $im=imagecreatefromjpeg($infile);
    $im1=imagecreatetruecolor($neww,$newh);
    imagecopyresampled($im1,$im,0,0,0,0,$neww,$newh,imagesx($im),imagesy($im));
    imagejpeg($im1,$outfile,$quality);
    imagedestroy($im);
    imagedestroy($im1);
    return true;
}
// Функция для раздельного вывода ошибок валидации для кажлого поля
function display_error($validation, $field)
{
    if ($validation->hasError($field)) {
        return $validation->getError($field);
    } else {
        return false;
    }
}
// Красивый вывод массивов и объектов
function outArray($arr)
{
    echo '<pre>' . print_r($arr, true) . '</pre>';
    echo '<br>';
}
// Перехватываем информацию var_dump
function getVarDump ($val) {
  ob_start();
  var_dump($val);
  $output = ob_get_clean();
  return $output;
}

// Записываем данные в лог-файл
function writeLogFile ($string, $clear = false)
{    
  $log_file_name = 'bot_logs.txt';    
  $now = date("Y-m-d H:i:s");        
  if ($clear == false) {
    $res =  file_put_contents($log_file_name, "\n" . $now . "\n" . print_r($string, true), FILE_APPEND);
  } else {
      file_put_contents($log_file_name, '');
      file_put_contents($log_file_name, $now . "\n" . print_r($string, true), FILE_APPEND);
  }    
}
