<?php

namespace App\Libraries;

class Webp
{
  // $src - исходный файл
  public static function create(string $src, int $quality = 100): string
  {
    $dir = pathinfo($src, PATHINFO_DIRNAME); //путь к директории исходного файла
    $dir_thumbnail = $dir . '/thimbnails';
    $name = pathinfo($src, PATHINFO_FILENAME); //назание исходного файла
    $ext = pathinfo($src, PATHINFO_EXTENSION); //расширение исходного файла

    $dest = "{$dir}/{$name}.webp"; // итоговый файл
    $dest_thumbnail = "{$dir_thumbnail}/{$name}.webp"; // итоговый файл

    $is_alpha = false; // флаг для работы с png и другими форматами с возможным прозрачным фоном
    if (mime_content_type($src) == 'image/png') {
      $is_alpha = true;
      $img = imagecreatefrompng($src); // получаем идентификатор картинки
    } elseif (mime_content_type($src) == 'image/jpeg') {
      $img = imagecreatefromjpeg($src);
    } else {
      return $src; // просто возвращается переданный в функцию файл
    }
    // Сохраняем прозрачность
    if ($is_alpha) {
      imagepalettetotruecolor($img);
      imagealphablending($img, true);
      imagesavealpha($img, true);
    }
    //imagedestroy($img); освобождает память, начиная с php8 не имеет смысла
    

    imagewebp($img, $dest, $quality);
    return $dest; // возвращаем путь к изображению
  }
}
