<?php 
// dcms/modules/mcom/mcom.php
Class seo {
function constructor()
	{
		echo '<div class="content ucol"><div class="block">';
	}

	function destructor() 
	{
		echo '</div></div></div>';
	}
	
	function index() {
	
		if ($this_page = $this->db->select("pages","id='".$_GET['pid']."'")){
			$endpath = $this_page[0]->path;
			$parent = $this_page[0]->parent;
			if ($this_page[0]->id!=1) {
				for(;;){
					$q = $this->db->select("pages","id='".$parent."'");
					if ($parent==1) break;
					$endpath = $q[0]->path.'/'.$endpath;
					$parent = $q[0]->parent;
				}
				$path_tp = $this->url.$endpath;
				
			} 
			else { $path_tp = $this->url; }
			echo "<h1>{$this_page[0]->name} ({$path_tp})</h1>";
		}
		//парсер стр.
		$in_parser = "[".date("H:i d-m-Y")."] ";
		$start_time = microtime();
		$start_array = explode(" ",$start_time);
		$start_time = $start_array[1] + $start_array[0];
		
		$content_mp = file_get_contents($path_tp);
		
		$end_time = microtime();
		$end_array = explode(" ",$end_time);
		$end_time = $end_array[1] + $end_array[0];
		$time = $end_time - $start_time;
		
		//Wordsstat;
		$content_mp_wordstat = str_replace('</', ' </', $content_mp); 
		$content_mp_wordstat = str_replace("\n", "", $content_mp_wordstat); 
		$content_mp_wordstat = str_replace("\r", "", $content_mp_wordstat); 
		$content_mp_wordstat = preg_replace("/[\s\.\,\!]+/", " ", $content_mp_wordstat);
		$all_text = strip_tags($content_mp_wordstat);
		$all_text = preg_replace('| +|', ' ', $all_text); 
		$text_words = explode(' ',$all_text);
		$count_words = count($text_words);
		echo "<p>Слов на странице: <b>".$count_words."</b> (Оптимально более 100)</p>"; 	
	
		foreach (array_count_values($text_words) as $key => $value){
			 if (($value > 1) && ($value != "")) echo '<br>'.$key.'-->'.$value;
		}
				
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		if (strripos($content_mp,'rel="icon"')) {
			echo "<p>Иконка сайта: <b>Да</b></p>";
		} else {
			echo "<p>Иконка сайта: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[ico] ";
		}
		
		if (strripos($content_mp,"charset=")) {
			echo "<p>Кодовая  страница сайта определена: <b>Да</b></p>";
		} else {
			echo "<p>Кодовая  страница сайта определена: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[char] ";
		}
		
		if (strripos($content_mp,'name="viewport"')) {
			echo "<p>Адаптация под моб. устройства: <b>Да</b></p>";
		} else {
			echo "<p>Адаптация под моб. устройства: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[mob] ";
		}
		
		if (strripos($content_mp,"yandex.ru/metrika")) {
			echo "<p>Статистика Yandex metrika: <b>Да</b></p>";
		} else {
			echo "<p>Статистика Yandex metrika: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[Y] ";
		}
		
		if (strripos($content_mp,"google-analytics.com")) {
			echo "<p>Статистика Google-analytics: <b>Да</b></p>";
		} else {
			echo "<p>Статистика Google-analytics: <b>Нет</b></p>";
			$in_parser .= "[G] ";
		}
		
		if (strripos($content_mp,"<title>")) {
			echo "<p>Title: <b>Да</b></p>";
		} else {
			echo "<p>Title: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[T] ";
		}
		
		if (strripos($content_mp,"escription")) {
			echo "<p>Description: <b>Да</b></p>";
		} else {
			echo "<p>Description: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[D] ";
		}
		
		if (strripos($content_mp,"eywords")) {
			echo "<p>Keywords: <b>Да</b></p>";
		} else {
			echo "<p>Keywords: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[K] ";
		}
	
		if (strripos($content_mp,"<h1>")) {
			echo "<p>h1: <b>Да</b></p>";
		} else {
			echo "<p>h1: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[h1] ";
		}
		
		if (strripos($content_mp,"<h2>")) {
			echo "<p>h2: <b>Да</b></p>";
		} else {
			echo "<p>h2: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[h2] ";
		}
		
		if (strripos($content_mp,"<h3>")) {
			echo "<p>h3: <b>Да</b></p>";
		} else {
			echo "<p>h3: <b>Нет</b></p>";
		}
		
		if (strripos($content_mp,"<h4>")) {
			echo "<p>h4: <b>Да</b></p>";
		} else {
			echo "<p>h4: <b>Нет</b></p>";
		}
		
		if (strripos($content_mp,"<h5>")) {
			echo "<p>h5: <b>Да</b></p>";
		} else {
			echo "<p>h5: <b>Нет</b></p>";
		}
		
		if (strripos($content_mp,"<h6>")) {
			echo "<p>h6: <b>Да</b></p>";
		} else {
			echo "<p>h6: <b>Нет</b></p>";
		}
		
		if (strripos($content_mp,"xml:lang")) {
			echo "<p>Язык сайта: <b>Да</b></p>";
		} else {
			echo "<p>Язык сайта: <b  style='background-color: #f88;'>Нет</b></p>";
			$in_parser .= "[lang] ";
		}
		
		
		//$this_page[0]->seo_warning = $in_parser; 
		//$this->db->update("pages","id='".$_GET['pid']."'",$this_page[0]); 	
		
		
		if (strripos($content_mp,"dathim.ru")) {
			echo "<p>Ссылка на сайт студии: <b>Да</b></p>";
		} else {
			echo "<p>Ссылка на сайт студии: <b>Нет</b></p>";
		}
		
		// www.
		$url_www = str_replace("://","://www.",$path_tp);
		$content_mp_www = file_get_contents($url_www);
		if ($content_mp_www == $content_mp){
			echo "<p>Сайт с www: <b>Работает </b></p>";
		} else {
			echo "<p>Сайт с www: <b  style='background-color: #f88;'>Выдаёт что то дургое</b></p>";
		}
		
		if ($This_header = @get_headers($path_tp)){
    		$response = explode(' ', $This_header[0]);
    		if ($response[1] == '200') {
				echo "<p>Сайт выдаёт код: <b>200 - Работает  нормально</b></p>";
			} else {
				echo "<p>Сайт выдаёт код: <b  style='background-color: #f88;'>".$response[1]." - Что то не то</b></p>";
			}
		}
		
		if ($This_header = @get_headers($url_www)){
    		$response = explode(' ', $This_header[0]);
    		if ($response[1] == '301') {
				echo "<p>Перенаправление с www (301 Redirect): <b>Да</b></p>";
			} else {
				echo "<p>Перенаправление с www (301 Redirect): <b style='background-color: #f88;'>Нет</b></p>";
			}
		}
		
		
		//Время загрузки 
		echo "<p>Страница сгенерирована: <b>$time</b> с</p>";
		
		//кол-во внешних файлов на стр
		
		
		
		
		
		
		
		
		
		
		
		
		
		echo "<h3 style='display: block;'>Страницы сайта</h3>";
		$all_page = $this->db->select("pages", "id>0 ORDER BY sort ASC");
		//[id] => 461 [name] => Сайт-визитка [parent] => 458 [path] => simple [title] => Сайт-визитка 
		//[keyw] => Сайт-визитка [descr] => [design] => 79 [sort] => 1 [off] => 0 [hide_child] => 0 [sost] => 0 [sub_design] => 79 ) 
		//print_r($all_page);
		$count_on_page="0";
		$has_404 = "Нет";
		$page_fail_title_kwd_descr = 0;
		foreach($all_page as $p){
			if ($p->off == 0) $count_on_page++;
			if ($p->name == "404") {
				$has_404="Да";
			} else {
				// не проверять на 404
				if (($p->keyw == $p->title) && ($p->keyw == $p->descr) && ($p->keyw != "")) { 
					$page_fail_title_kwd_descr++;
				} else {
					if (($p->title == "") || ($p->keyw == "") || ($p->descr == "")) $page_fail_title_kwd_descr++;
				}
			}
		}
		
		echo "<p>На сайте основных страниц: <b>".count($all_page)."</b>";
		echo ", из них включено:<b> {$count_on_page}</b></p>";
		echo "<p>Наличие 404 страницы: <b>{$has_404}</b></p>";
		echo "<p>Заполнены неправильно Title, Keywords, Description на страницах:<b>{$page_fail_title_kwd_descr}</b></p>";
		
		
		
		echo "<h3 style='display: block;'>Север</h3>";
		if (is_file("../robots.txt")) { 
			echo "<p>Файл robots.txt : <b>Да</b>";
		} else {
			echo "<p>Файл robots.txt : <b  style='background-color: #f88;'>Нет</b>";
		}
		
		if (is_file("../sitemap.xml")) { 
			echo ". Файл sitemap.xml: <b>Да</b></p>";
		} else {
			echo ". Файл sitemap.xml: <b  style='background-color: #f88;'>Нет</b></p>";
		}
		
		$big_file = 0;
		if (is_dir("../uploads")) { 
			if ($handle = opendir("../uploads")) {
				while (false !== ($file = readdir($handle))) { 
					if ($file == '..') continue;
					if ($file == '.') continue;
					if (is_dir("../uploads/".$file)) {
						continue;
					} else {
						if (@filesize("../uploads/".$file) > 200000) {
							$big_file++;
						}
					}
				}
			}
		}
		if ($big_file >0){
			echo "<p>В папке uploads имеются файлы большого размера (более 200Кб): <b  style='background-color: #f88;'>{$big_file}</b> шт.</p>";
		} else {
			echo "<p>В папке uploads файлов большого размера (более 200Кб): <b>Нет</b></p>";
		}
		
		
	}
	
	
	function get_map() {
	
		// Поможет при длительном выполнении скрипта
		set_time_limit(0);
		$host='dathim.ru'; // Хост сайта
		$scheme='https://'; // http или https?
		$urls=array(); // Здесь будут храниться собранные ссылки
		$content=NULL; // Рабочая переменная
		// Здесь ссылки, которые не должны попасть в sitemap.xml
		$nofollow=array('/go.php','/search/','/404/','javascript:void(0)','tel:+78182629906','javascript:void(0);');
		// Первой ссылкой будет главная страница сайта, ставим ей 0, т.к. она ещё не проверена
		$urls[$scheme.$host]='0';
		// Разрешённые расширения файлов, чтобы не вносить в карту сайта ссылки на медиа файлы. Также разрешены страницы без разрешения, у меня таких страниц подавляющее большинство.
		$extensions[]='php';$extensions[]='aspx';$extensions[]='htm';$extensions[]='html';$extensions[]='asp';$extensions[]='cgi';$extensions[]='pl';
		// Корневая директория сайта, значение можно взять из $_SERVER['DOCUMENT_ROOT'].'/';
		

		// Функция для сбора ссылок
		function sitemap_geturls($page,&$host,&$scheme,&$nofollow,&$extensions,&$urls)
		{
			//Возможно уже проверяли эту страницу
			if($urls[$page]==1){continue;}
			//Получаем содержимое ссылки. если недоступна, то заканчиваем работу функции и удаляем эту страницу из списка
			$content=file_get_contents($page);if(!$content){unset($urls[$page]);return false;}
			//Отмечаем ссылку как проверенную (мы на ней побывали)
			$urls[$page]=1;
			//Проверяем не стоит ли запрещающий индексировать ссылки на этой странице мета-тег с nofollow|noindex|none
			if(preg_match('/<[Mm][Ee][Tt][Aa].*[Nn][Aa][Mm][Ee]=.?("|\'|).*[Rr][Oo][Bb][Oo][Tt][Ss].*?("|\'|).*?[Cc][Oo][Nn][Tt][Ee][Nn][Tt]=.*?("|\'|).*([Nn][Oo][Ff][Oo][Ll][Ll][Oo][Ww]|[Nn][Oo][Ii][Nn][Dd][Ee][Xx]|[Nn][Oo][Nn][Ee]).*?("|\'|).*>/',$content)){$content=NULL;}
			//Собираем все ссылки со страницы во временный массив, с помощью регулярного выражения.
			preg_match_all("/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/",$content,$tmp);$content=NULL;
			//Добавляем в массив links все ссылки не имеющие аттрибут nofollow
			foreach($tmp[0] as $k => $v){if(!preg_match('/<.*[Rr][Ee][Ll]=.?("|\'|).*[Nn][Oo][Ff][Oo][Ll][Ll][Oo][Ww].*?("|\'|).*/',$v)){$links[$k]=$tmp[1][$k];}}
			unset($tmp);
			//Обрабатываем полученные ссылки, отбрасываем "плохие", а потом и с них собираем...
			for ($i = 0; $i < count($links); $i++)
			{
				if (isset($links[$i])){
					//Если слишком много ссылок в массиве, то пора прекращать нашу деятельность (читай спецификацию)
					if(count($urls)>49900){return false;}
					//Если не установлена схема и хост ссылки, то подставляем наш хост
				
					if(!strstr($links[$i],$scheme.$host)){
						$links[$i]=$scheme.$host.$links[$i];
					}
					
					//Убираем якори у ссылок
					$links[$i]=preg_replace("/#.*/X", "",$links[$i]);
					//Узнаём информацию о ссылке
					$urlinfo=@parse_url($links[$i]);if(!isset($urlinfo['path'])){$urlinfo['path']=NULL;}
					//Если хост совсем не наш, ссылка на главную, на почту или мы её уже обрабатывали - то заканчиваем работу с этой ссылкой
					if((isset($urlinfo['host']) AND $urlinfo['host']!=$host) OR $urlinfo['path']=='/' OR isset($urls[$links[$i]]) OR strstr($links[$i],'@')){continue;}
					//Если ссылка в нашем запрещающем списке, то также прекращаем с ней работать
					$nofoll=0;if($nofollow!=NULL){foreach($nofollow as $of){if(strstr($links[$i],$of)){$nofoll=1;break;}}}if($nofoll==1){continue;}
					//Если задано расширение ссылки и оно не разрешёно, то ссылка не проходит
					@$ext=end(explode('.',$urlinfo['path']));
					$noext=0;if($ext!='' AND strstr($urlinfo['path'],'.') AND count($extensions)!=0){$noext=1;foreach($extensions as $of){if($ext==$of){$noext=0;continue;}}}if($noext==1){continue;}
					//Заносим ссылку в массив и отмечаем непроверенной (с неё мы ещё не забирали другие ссылки)
					$urls[$links[$i]]=0;
					//Проверяем ссылки с этой страницы
					sitemap_geturls($links[$i],$host,$scheme,$nofollow,$extensions,$urls);
				}
			} 
			return true;
		}
		 
		// (START!) Первоначальный старт функции для проверки главной страницы и последующих
		sitemap_geturls($scheme.$host,$host,$scheme,$nofollow,$extensions,$urls);

		// Когда все ссылки собраны, то обрабатываем их и записываем в файлы sitemap.xml и sitemap.txt (должны быть права на запись)
		$sitemapXML='<?xml version="1.0" encoding="UTF-8"?>
		<urlset xmlns="http://www.google.com/schemas/sitemap/0.84"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd">
		<!-- Last update of sitemap '.date("Y-m-d H:i:s+06:00").' -->';
		$sitemapTXT=NULL;
		 
		// Добавляем каждую ссылку
		foreach($urls as $k => $v){$sitemapXML.="\r\n<url><loc>{$k}</loc><changefreq>weekly</changefreq><priority>0.5</priority></url>";$sitemapTXT.="\r\n".$k;}
		 
		//Окончание для файла sitemap.xml
		$sitemapXML.="\r\n</urlset>";

		//Некоторые символы, а также кириллица - должны быть в правильной кодировке/виде (по спецификации)
		$sitemapXML=trim(strtr($sitemapXML,array('%2F'=>'/','%3A'=>':','%3F'=>'?','%3D'=>'=','%26'=>'&','%27'=>"'",'%22'=>'"','%3E'=>'>','%3C'=>'<','%23'=>'#','&'=>'&')));
		$sitemapTXT=trim(strtr($sitemapTXT,array('%2F'=>'/','%3A'=>':','%3F'=>'?','%3D'=>'=','%26'=>'&','%27'=>"'",'%22'=>'"','%3E'=>'>','%3C'=>'<','%23'=>'#','&'=>'&')));

		//Запись в файл
		//echo $sitemapXML; 
		//$fp=fopen($engine_root.'../sitemap.txt','w+');if(!fwrite($fp,$sitemapTXT)){echo 'Ошибка записи!';}fclose($fp);
		$fp=fopen('../sitemap.xml','w+');
		if(!fwrite($fp,$sitemapXML)){
			echo 'Ошибка записи!';
		} else {
		
			echo "<p>Данные обновлены, найдено страниц: ".substr_count($sitemapXML,"</loc>")."</p>";
		}
		fclose($fp);
	
	} 
	
	
}
	