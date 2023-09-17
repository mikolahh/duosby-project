<?php

namespace App\Libraries;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Xls
{	
	public static function upload($file, $cells)
	{
		$db = \Config\Database::connect();			
		$builder = $db->table('parts');							
		helper('my');		
		$aliases = aliases();			
		 // используем класс IOFactory БИБЛИОТЕКИ PHPSpreadsheet и его метод load для загрузки файла, передаем полученный объект в переменную $document для дальнейшей работы			 
			$document = IOFactory::load($file);	
					 
		//$count_sheets = $document->getSheetCount(); // можем узнать кол-во листов в документе, но мы знаем, что он у нас один		
		$document->setActiveSheetIndex(0); // Устанавливаем активный лист в документе под индексом 0, первый по счету и единственный
				
		$sheet = $document->getActiveSheet(); // получаем данные активного листа и присваиваем их переменной $sheet
		//$row_count = $sheet->getHighestDataRow();//можем получить максимальное кол-во строк, но по моему мнеию оно нам не нужно, библиотека сама справится и не будет перебирать пустые строки, которых бесконечное кол-во
		//$column_count = $sheet->getHighestColumn();// АНАЛОГИЧНО СО СТОЛБЦАМИ
		$rate = $sheet->getCell('H1'); // получаем курс из ячейки D1		
		$rate = str_replace(',', '.', $rate);		
		$rowIterator = $sheet->getRowIterator(); //получаем данные о всех строках и присваиваем их переменной  $rowIterator	
		$arr_parts = array(); // создаем массив, куда впоследствии будем заносить данные о строках таблицы, то есть данные о наших комплектующих
		foreach ($rowIterator as $row) { // проходимся в цикле по всем строкам
			if ($row->getRowIndex() > 1) { // вызываем метод, который отдает индекс текущей ячейки, и пишем условие, в котором указываем, что нас интересуют только строки с индексом, большим 1, так как шапка таблицы  и все, что находится выше, нас не интересует. $row - это объект с информацией по одной строке
				$cellIterator = $row->getCellIterator(); // получаем данные о всех ячейках в текущей строке в виде объекта и присваиваем его в переменную
				foreach ($cellIterator as $cell) { // проходимся в цикле по всем ячейкам в текущей строке 
					$cellPath = $cell->getColumn(); // получаем значение колонки для каждой ячейки (иначе говоря, путь к ячейке)					
					if (isset($cells[$cellPath])) { //  Далее обращаемся к конфигурационному св-ву cells с полями нашей  таблицы в бд. И если в нашем конфигурационном массиве cells существует элемент с ключем, совпадающим с путем к данной ячейке, то само значение этого элемента мы будем использовать в качестве ключа для формирования массива с данными для конкретной запчасти
						$arr_part[$cells[$cellPath]] = $cell->getCalculatedValue();	//формируем массив с данными о конкретной запчасти 							
					}
				}
				array_push($arr_parts, $arr_part);	// формируем массив с данными о всех моделях					
			}
		} 
				
			// Удаляем parts, где нет цены уже не актуально		
		// $arr_parts = array_filter($arr_parts, fn($part) => $part['part_price'] != 0);	
							
		// Производим необходимые нам преобразования данных
		foreach ($arr_parts as $key=> &$part) {
			$part['part_price'] = round($part['part_price'] * $rate); // преобразуем цены с учетом курса	
			// Если существует поле brand_model, разделяем его
			if ($part['dev_brand_model']) {
				$part['dev_brand_model'] = trim($part['dev_brand_model']);
				$part['dev_brand'] = trim(explode(' ', $part['dev_brand_model'], 2)[0]); // выделяем поле brand из brand_model
				$part['dev_model'] = trim(explode(' ', $part['dev_brand_model'], 2)[1]); // выделяем поле model из brand_model			
			} else {
				$part['dev_brand'] = '';
				$part['dev_model'] = '';
			}
			//unset($part['dev_brand_model']); // удаляем поле brand_model			
			// Преобразуем служебные строковые данные (varchar) нужным нам образом
			$part['dev_kind'] = str_replace([' ', '/', ','], '_', mb_strtolower(trim($part['dev_kind'])));
			$part['part_kind'] = str_replace([' ', '/', ','], '_', mb_strtolower(trim($part['part_kind'])));
			$part['part_sub_kind'] = str_replace([' ', '/', ','], '_', mb_strtolower(trim($part['part_sub_kind'])));
			$part['part_param'] = str_replace([' ', '/', ','], '_', mb_strtolower(trim($part['part_param'])));
			$part['part_param'] = str_replace(['__', '___'], '_', $part['part_param']);			
			// Приводим оставшиеся необработанные поля к нужному типу данных
			$part['part_desc'] = (string)$part['part_desc'];
			$part['part_name'] = (string)$part['part_name'];
			$part['dev_kind'] = (string)$part['dev_kind'];
			$part['part_en_slider'] = (int)$part['part_en_slider'];
			// Начали формировать slug
			// Приводим поля, используемые для формирования slug, к нужному виду
			$brand_for_slug = str_replace([' ', '/', ',', '(', ')'], '-', mb_strtolower(trim($part['dev_brand'])));
			$model_for_slug = str_replace([' ', '/', ',', '(', ')'], '-', mb_strtolower(trim($part['dev_model'])));
			$part_name_for_slug = str_replace([' ', '/', ',', '(', ')'], '-', mb_strtolower(trim($part['part_name'])));
			$part_kind_for_slug = str_replace([' ', '/', ',', '(', ')', '_'], '-', mb_strtolower(trim($part['part_kind'])));
			// Добавляем разделители к нужным существующим полям
			$brand_for_slug = $brand_for_slug ? "{$brand_for_slug}-" : '';
			$model_for_slug = $model_for_slug ? "{$model_for_slug}-" : '';
			$part_name_for_slug = $part_name_for_slug ? "{$part_name_for_slug}-" : '';

			// Собираем slug
			$slug_for_slug = $brand_for_slug . $model_for_slug . $part_name_for_slug . $part_kind_for_slug;
			// убираем возможные повторы разделителей
			$part['part_slug'] = str_replace(['--', '---'], '-', $slug_for_slug);
			// Закончили формировать slug			
			// Формируем part_kind_group, part_h, part_url, part_seo_title, part_seo_desc
			if (!empty($part['part_param'])) {
				$part_param_h = ' ('. $aliases['part_params']["{$part['part_param']}"]['for_h'] . ')';
			}
			else $part_param_h = '';			
			if (!empty($part['dev_brand'])) {
				$part['part_kind_group'] = 1;								
				$part['part_h'] = '<span>' . $aliases['part_kinds']["{$part['part_kind']}"]['for_h'] . ' ' . $aliases['dev_kinds']["{$part['dev_kind']}"]['for_dev'] . ' ' . $part_param_h . '</span>' . '<span>' . $part['dev_brand'] . '</span>' . ' ' . '<span>' . $part['dev_model'] . '</span>';				
				$part['part_seo_title'] = $aliases['part_kinds']["{$part['part_kind']}"]['for_title'] . ' ' . $aliases['dev_kinds']["{$part['dev_kind']}"]['for_dev'] . ' ' . $part['dev_brand'] . ' ' . $part['dev_model'] . $part_param_h;
				$part['part_seo_desc'] = $aliases['part_kinds']["{$part['part_kind']}"]['for_desc'] . ' ' . $aliases['dev_kinds']["{$part['dev_kind']}"]['for_dev'] . ' ' . $part['dev_brand']  . ' ' . $part['dev_model'] . $part_param_h;
			} else if(!empty($part['part_sub_kind'])) {
					$part['part_kind_group'] = 2;
					$part['part_h'] = '<span>' . $aliases['part_kinds']["{$part['part_kind']}"]['part_sub_kinds']["{$part['part_sub_kind']}"]['for_h'] . '</span>' . ' ' . '<span>' . $part['part_name'] . $part_param_h . '</span>';					
					$part['part_seo_title'] = $aliases['part_kinds']["{$part['part_kind']}"]['part_sub_kinds']["{$part['part_sub_kind']}"]['for_title'] . ' ' . $part['part_name'] . $part_param_h;
					$part['part_seo_desc'] = $aliases['part_kinds']["{$part['part_kind']}"]['part_sub_kinds']["{$part['part_sub_kind']}"]['for_desc'] . ' ' . $part['part_name'] . $part_param_h;
			} else {
				$part['part_kind_group'] = 3;
				$part['part_h'] = '<span>' . $aliases['part_kinds']["{$part['part_kind']}"]['for_h'] . '</span>' . ' ' . '<span>' . $part['part_name'] . $part_param_h . '</span>';
				$part['part_seo_title'] = $aliases['part_kinds']["{$part['part_kind']}"]['for_title'] . ' ' . $part['part_name'] . $part_param_h;
				$part['part_seo_desc'] = $aliases['part_kinds']["{$part['part_kind']}"]['for_desc'] . ' ' . $part['part_name'] . $part_param_h;
			}
			// Закончили формировать part_kind_group, part_h, part_url, part_seo_title, part_seo_desc	
			// Записываем время
			$part['created_at'] = date('Y-m-d\\TH:i:sP');
					
		}			
		unset($part); // удаляем переменную, созданную по ссылке в цикле foreach
		
				
		//$query1 = $builder->truncate();	// очищаем таблицу от старых данных
		//$query2 = $builder->insertBatch($arr_parts); // старый вариант, когда просто перезаписывали бд		
		$query1 = $builder->updateBatch($arr_parts, ['part_slug']); // обновляем все строки  в таблице данными $arr_parts  по ключу slug		
		$query2 = $builder->upsertBatch($arr_parts); // добавляем в таблицу новые строки (которые отсутствуют в таблице, но присутствуют в $arr_parts )
					// Создаем sitemap.xml	
		$arr_parts_db =	$builder->select('part_slug, created_at')->where(['part_first_img !=' => ''])->get()->getResultArray();		
		$query3 = self::sitemap($arr_parts_db);
		if (isset($query1) && isset($query2) && isset($query3)) {
			return true;
		} else {
			return false;
		}
	}
	protected static function sitemap($arr_parts)
	{						  
		$sitemap_data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . PHP_EOL;			
		$sitemap_data .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">" . PHP_EOL;			
		$sitemap_data .= ' ' . "<url>" . PHP_EOL;
		$sitemap_data .= '  ' . "<loc>" . site_url() . "</loc>" . PHP_EOL;
		$sitemap_data .= '  ' .'<lastmod>' . date('Y-m-d\\TH:i:sP') . '</lastmod>' . PHP_EOL; 
		$sitemap_data .= ' ' . "</url>" . PHP_EOL;
		foreach ($arr_parts as &$item) {
				$sitemap_data .= ' ' . "<url>" . PHP_EOL;
				$sitemap_data .= '  ' . "<loc>" . site_url("parts/{$item['part_slug']}") . "</loc>" . PHP_EOL;
				$sitemap_data .= '  ' .'<lastmod>' . date('Y-m-d\\TH:i:sP') . '</lastmod>' . PHP_EOL;
				$sitemap_data .= ' ' . "</url>" . PHP_EOL;
		}			
		unset($item);
		$sitemap_data .= "</urlset>";					
		$file = 'sitemap.xml';
		file_put_contents($file, '');
		$res = file_put_contents($file, $sitemap_data, FILE_APPEND);		
		return	$res;
	}
}
