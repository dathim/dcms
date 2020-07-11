<?php 
// dcms/system/function.php
Class func {



	function rus_date($format="j F Y", $timestamp) {
		$translate = array(
		"am" => "дп",
		"pm" => "пп",
		"AM" => "ДП",
		"PM" => "ПП",
		"Monday" => "Понедельник",
		"Mon" => "Пн",
		"Tuesday" => "Вторник",
		"Tue" => "Вт",
		"Wednesday" => "Среда",
		"Wed" => "Ср",
		"Thursday" => "Четверг",
		"Thu" => "Чт",
		"Friday" => "Пятница",
		"Fri" => "Пт",
		"Saturday" => "Суббота",
		"Sat" => "Сб",
		"Sunday" => "Воскресенье",
		"Sun" => "Вс",
		"January" => "Января",
		"Jan" => "Янв",
		"February" => "Февраля",
		"Feb" => "Фев",
		"March" => "Марта",
		"Mar" => "Мар",
		"April" => "Апреля",
		"Apr" => "Апр",
		"May" => "Мая",
		"May" => "Мая",
		"June" => "Июня",
		"Jun" => "Июн",
		"July" => "Июля",
		"Jul" => "Июл",
		"August" => "Августа",
		"Aug" => "Авг",
		"September" => "Сентября",
		"Sep" => "Сен",
		"October" => "Октября",
		"Oct" => "Окт",
		"November" => "Ноября",
		"Nov" => "Ноя",
		"December" => "Декабря",
		"Dec" => "Дек",
		"st" => "ое",
		"nd" => "ое",
		"rd" => "е",
		"th" => "ое"
		);
		return strtr(date($format, $timestamp), $translate);
	}





	function sub_com($tab, $id, $order=''){
		if ($order == '') $order = "d_sort ASC"; 
		$sql = "SELECT * FROM `{$tab}` WHERE `parent`=".$id." ORDER BY $order";
		$res = db::$mysqli->query($sql);
		return $res;
	// нужна сборка ка надо 
	
	}

	function foldersize($path) {

		$total_size = 0;
		$files = scandir($path);


		foreach($files as $t) {

			if (is_dir(rtrim($path, '/') . '/' . $t)) {

				if ($t<>"." && $t<>"..") {

					$size = $this->foldersize(rtrim($path, '/') . '/' . $t);
 
					$total_size += $size;
				}
			} else {

				$size = @filesize(rtrim($path, '/') . '/' . $t);

				$total_size += $size;
			}   
		}

		return $total_size;
	}


	//время генеации страницы
	function generation_time($path,$count=100){
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$start = $time;
		for($i=0;$i<$count;$i++){
			$homepage = file_get_contents($path);
			unset($homepage);
		}
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $start), 4);
		$total_time = $total_time/$count;
		return $total_time;
	}
	

	function check_filename($file_name,$dir='../uploads/',$prefix=''){
		$new_file_name = $this->ru_en_translite($file_name);
		$t = explode(".", $new_file_name);
		$file_type = end($t);
		$new_file_name = substr($new_file_name, 0, strrpos($new_file_name, '.')); 
		$new_file_name = $prefix.substr($new_file_name, 0, 50); 
		$new_file_name = str_replace(array(' ','%20'),'_',$new_file_name);
		$new_file_name = str_replace(array("'",'"','#','%','@'),'',$new_file_name);

		$new_file_name = strtolower($new_file_name); //preg_replace('%[^A-Za-zА-Яа-я0-9]%', '', $new_file_name);
		for($i=0;$i<1000;$i++){
			if ($i==0) $caut_val=''; else  $caut_val='('.$i.')';
			if (!is_file($dir.$new_file_name.$caut_val.'.'.$file_type)) {
				return $new_file_name.$caut_val.'.'.$file_type;
			}
		}
		
	}
	
	function check_num($val){
		$val = trim($val);
		$val = htmlspecialchars($val);
		$int_val = intval($val);
		return $int_val;
	}
	
	function check_string($val){
		$val = trim($val);
		$val = htmlspecialchars($val);
		$bad_chars = array("'","\"","--",);
		$val = str_replace($bad_chars, "", $val);
		$val_c = $val;
		  if ( 
		  !strpos($val_c, "select") && //  
		  !strpos($val_c, "union") && // 
		  !strpos($val_c, "select") && // 
		  !strpos($val_c, "order") && // Ищем вхождение слов в параметре 
		  !strpos($val_c, "where") && //  
		  !strpos($val_c, "char") && // 
		  !strpos($val_c, "from") // 
		  ) {
				return $val_c;
			}
			else return 'bad_data';
		
	}
		
	function cript_f1($pwd,$url='http://dcms4.ru/'){
		$spre =  'M65g308E';
		$spost =  'm3nv1rTe';
		$eurl = preg_replace("/[^a-z]/i", "", $url);
		$res = crypt(md5($pwd,$eurl),'$5$rounds=5000$usesomesillystringforsalt$');
		$res = md5($res);
		$res = $spre.$res.$spost;
		return $res;
	}
	
	//email_func
	
	function email_t_get($fname="No name",$get){
		if ($get != ''){
			if (isset($_GET[$get])){
				$val = $this->check_string($_GET[$get]);
				return  "<p>{$fname} - {$val}</p>";
			}
		}
		else return "<p>bad post</p>";
	}
	
	function email_t_post($fname="No name",$post){
		if ($post != ''){
			if (isset($_POST[$post])){
				$val = $this->check_string($_POST[$post]);
				return  "<p>{$fname} - {$val}</p>";
			}
		}
		else return "<p>bad post</p>";
	}
	
	function email_send_html($to="dathim@gmail.com",$text="no_text",$subject="SITE",$from="info@dathim.ru"){
	
		$header = "Content-type: text/html; charset='utf-8' \r\n";
		//$header='Content-type: text/plain; charset=utf-8';
		$header.="From: Evgen <".$from.">";
		$header.="Subject: Site";
		$header.='Content-type: text/plain; charset=utf-8';
		//$header .= 'Content-type: text/plain; charset=utf-8';
		 
		if(@mail($to, $_SERVER['HTTP_REFERER'], $text, $header))
		{
			return true;
		}else{
			return false;
		}
	}
	
	function scan_modules() //Поиск модулей
	{
		$moddir = scandir('modules');
		foreach($moddir as $m)
		{
			if ($m != '.'){ 
				if ($m != '..'){ 
					if (is_dir('modules/'.$m)){
						if (file_exists('modules/'.$m.'/index.php')){
							$moduls[] = $m;
						}
					}
				}
			}
		}
		foreach($moduls as $m) //сбор данных по всем модулям
		{
			include 'modules/'.$m.'/index.php';
			if($modul['enabled']) $m_vars[] = $modul;
			unset($modul);
		}
		return $m_vars;
	}
	
	function resort_mod_var($vars) // Сортировка модулей
	{
		for($i=0; $i<count($vars); $i++)
		{
			for($j=0; $j<count($vars); $j++)
			{
				if ($vars[$i]['sort'] < $vars[$j]['sort'])
				{
					$tmp = $vars[$i];
					$vars[$i] = $vars[$j];
					$vars[$j] = $tmp;
				}
			}
		}
		return $vars;
	}
	
	
	function run_modul($mod_def,$path,$user,$m_vars,$dcms,$database ) //выводимый_модуль(По умолчанию, путь сейчас, права пользователя, данные модулей, классы осн. модудей)
	{	
		if (isset($path[1])) $modul = $path[1]; else $modul = $mod_def;
		if (isset($path[2])) $page = $path[2];	else $page = 'index';
		
		
		foreach($m_vars as $mv)
		{
			if ($mv['enname'] == $modul) //нужный модуль
			{

				if ((isset($mv['rights'])) && ($mv['rights'] >= $dcms->user[1])) //Проверка прав на модуль
				{
					$mpath = 'modules/'.$modul.'/'.$modul.'.php';
					$fmpath = 'modules/'.$modul.'/function.php';
					if (is_file($mpath))
					{	
						include $mpath;
						if (($page[0] == 'c') && ($page[1] == '_'))
						{
							$start = new clear;
						}
						else
						{
							$start = new $modul;
						}
						
						if (is_file($fmpath))
						{
							include $fmpath;
							$fm = 'f'.$modul;
							//$start->mfunc = new $fm;
							//print_r(get_class_methods($fm));
							$start->func = new $fm;
							//print_r(get_class_methods($start->func));
						}
						else
						{
							$start->func = $dcms->func;
						}
						$start->db = $dcms->db;
						$start->url = $dcms->url;
						if (isset($dcms->use_rf_domain)) $start->use_rf_domain = $dcms->use_rf_domain;
						$start->max_uplads_size = $dcms->max_uplads_size;
						$start->user = $dcms->user;
						$start->out = $dcms->out;
						$start->database = $database; 
						if (($page[0] == 'c') && ($page[1] == '_'))
						{
							$start->$page();
						}
						else
						{
							$start->constructor();
							$start->$page();
							$start->destructor();
						}
					}
					else
					{
						include 'informers/no_modul_workspace.php';
					}
				}
				else
				{
					include 'informers/no_raights.php';
				}
			}
		}		
	}

	function segment_url($url)
	{
		$base = trim($url,'/');
		$base = explode("/",$base);
		$request = trim($_SERVER["REQUEST_URI"],'/');
		$request .='?';
		$request = substr($request, 0, strpos($request,'?')-strlen($request));
		$request = explode("/",$request);
		$res = '';
		$flag = true;
		foreach($request as $r)
		{
			foreach($base as $b)
			{
				if ($r == $b) 	$flag = false;
			}
			$r = urldecode($r);
			if ($flag == true)
			{
				//защита 
				$srting = array("query","select","from","delete","insert","update",";","'",'"',"^","|","\n","\r","\p","<",">");
				$r = trim(htmlspecialchars(strip_tags(str_replace($srting,"",$r)))); 
				$r = preg_replace("/[^a-zA-Zа-яA-Z-,_ 0-9]/", "", $r);
				$res[] = $r; 
			}
			$flag = true;
		}
		return $res;
	}
	
	
		
	function ru_en_translite($string) 
	  { 
		$table = array( 
					'А' => 'a', 
					'Б' => 'b', 
					'В' => 'v', 
					'Г' => 'g', 
					'Д' => 'd', 
					'Е' => 'e', 
					'Ё' => 'yo',
					'Ж' => 'zh',
					'З' => 'z', 
					'И' => 'i', 
					'Й' => 'j', 
					'К' => 'k', 
					'Л' => 'l', 
					'М' => 'm', 
					'Н' => 'n', 
					'О' => 'o', 
					'П' => 'p', 
					'Р' => 'r', 
					'С' => 's', 
					'Т' => 't', 
					'У' => 'u', 
					'Ф' => 'f', 
					'Х' => 'h', 
					'Ц' => 'c', 
					'Ч' => 'ch',
					'Ш' => 'sh',
					'Щ' => 'csh', 
					'Ь' => '', 
					'Ы' => 'y', 
					'Ъ' => '', 
					'Э' => 'e', 
					'Ю' => 'yu',
					'Я' => 'ya',
					'-' => '-',
					'а' => 'a', 
					'б' => 'b', 
					'в' => 'v', 
					'г' => 'g', 
					'д' => 'd', 
					'е' => 'e', 
					'ё' => 'yo', 
					'ж' => 'zh', 
					'з' => 'z', 
					'и' => 'i', 
					'й' => 'j', 
					'к' => 'k', 
					'л' => 'l', 
					'м' => 'm', 
					'н' => 'n', 
					'о' => 'o', 
					'п' => 'p', 
					'р' => 'r', 
					'с' => 's', 
					'т' => 't', 
					'у' => 'u', 
					'ф' => 'f', 
					'х' => 'h', 
					'ц' => 'c', 
					'ч' => 'ch', 
					'ш' => 'sh', 
					'щ' => 'csh', 
					'ь' => '', 
					'ы' => 'y', 
					'ъ' => '', 
					'э' => 'e', 
					'ю' => 'yu', 
					'я' => 'ya', 
					' ' => '_' 
		); 

		$output = str_replace( 
			array_keys($table), 
			array_values($table),$string  ); 

		return $output; 
	} 
	
	function is_login($urla="",$urlb="") {
		if (($urla == 'users') && ($urlb == 'c_login')) { return true;} // получение формы входа
		if (($urla == 'users') && ($urlb == 'c_send_new_pas')) { return true;} // получение паролья
		if (($urla == 'users') && ($urlb == 'c_send_new_pas2')) { return true;} // получение паролья
		if (isset($_SESSION['login'])) {
			if (isset($_SESSION['password'])){ 
				if (isset($_SESSION['rights'])) {				
					$login = base64_decode($_SESSION['login']); 
					$rights = $_SESSION['rights']; 
					$password = $_SESSION['password']; 
					if ($query = "SELECT * FROM users WHERE login = '" .$login."' AND  password='".$password."'"){
						if ($nme = db::$mysqli->query($query)){
							$user = $nme->fetch_object();
							if ((isset($user->rights)) && ($user->rights>0)) {
								return true;
							} else return false;
						} else return false;
					} else return false;
				} else return false;
			} else return false;
		} else return false;
		return false;
	}
	
	// модификация 20.6.2017
	function serch_tru_path($p, $data,$branch, $bc,$id=0,$dcms){			
		$flag=false;
		foreach($data as $d){
			if (($p == $d->parent) && ($branch[$bc] == $d->path)){			
				$bc++;
				$next_p = $d->id; 
				$flag=true;
				$true_id = $d->id;
				$id = $d->id;
			}
		}
		
		// страниуа соответствует пути и она крайняя
		if (($flag==true) && (count($branch)==$bc)) {
			return $next_p;
		}
		
		// страница не в пути и она крайная 
		if (($flag==false) && (count($branch)==$bc)) {
			$dcms->func->p404($dcms);
			exit();
		}
		
		if ($flag)  {
			//переходим к поиску следующей страницы
			$true_id = $this->serch_tru_path($next_p, $data, $branch, $bc, $id,$dcms); 
			return $true_id;
		} else {
			// Не удалось восстановить цепочку
			$dcms->func->p404($dcms);
			exit();	
		}
	}
	
	
	function show_bar($name,$url,$page_id,$dcms,$p_design){
		if ($bar_pi = $dcms->db->select("pages_items","parent=".$page_id." AND komp=0 AND design = ".$p_design."")){
			
			if (count($bar_pi) == 1) {
				$edit_button ='';
				if  ((strpos($bar_pi[0]->text, "<?")) || (strpos($bar_pi[0]->text, "?>"))) { 
						$edit_button .= '<a rel="tooltip" data-placement="bottom" data-original-title="Подсветка кода" href="javascript:window.open(\''. $url .'dcms/page/c_edit?editor=cm&id='.$bar_pi[0]->id.'\',\'\',\'width=900,height=500\'); void(0)" >Редактировать '.$bar_pi[0]->name.'</a>';
					}	 else { 
						$edit_button .= '<a href="javascript:window.open(\''. $url .'dcms/page/c_edit?editor=fck&id='.$bar_pi[0]->id.'\',\'\',\'width=900,height=500\'); void(0)" >Редактировать '.$bar_pi[0]->name.'</a>';
					}
			
			}
			
			if (count($bar_pi) > 1) {
				$edit_button='<a href="javascript:void(0)">Редактировать ('.count($bar_pi).')</a>';
				$edit_button.='	<div class="dropdown">';
				foreach($bar_pi as  $pis){
					if  ((strpos($pis->text, "<?")) || (strpos($pis->text, "?>"))) { 
						$edit_button .= '<a rel="tooltip" data-placement="bottom" data-original-title="Подсветка кода" href="javascript:window.open(\''. $url .'dcms/page/c_edit?editor=cm&id='.$pis->id.'\',\'\',\'width=900,height=500\'); void(0)" >'.$pis->name.'</a>';
					}	 else { 
						$edit_button .= '<a href="javascript:window.open(\''. $url .'dcms/page/c_edit?editor=fck&id='.$pis->id.'\',\'\',\'width=900,height=500\'); void(0)" >'.$pis->name.'</a>';
					}
				}
				$edit_button.='</div>';
			}
		} else {
			$edit_button='<a>На странице нечего редактировать</a>';
		}
		
		
		echo '
				<link rel="stylesheet" type="text/css" href="'.$url.'dcms/style/dcms_panel.css">
				<div class="dcms_panel"><div class="wrapper">
				<div class="right">
					<div class="group">
						<a href="'.$url.'dcms/page?page='.$page_id.'">Назад в DCMS</a>
						<a href="'.$url.'dcms/page/c_exit">Разлогиниться</a>
					</div>
				</div>
				<div class="left">
					<div class="group">
						'.$edit_button.'
						
					</div>
				</div>
			</div></div>
		';
	}
	
	function print_page($id,$dcms)
	{
		if ($page = $dcms->db->select("pages","id='".$id."'"))
		{
			$d_sql_q = "SELECT id, name, sort, text, komp , ful_copy_id, `design`  FROM `designes_items` WHERE parent='".$page[0]->design."'  AND `for_all`=1 AND `design`=0 UNION SELECT  `id`, `name`, `sort`, `text`, `komp` , `ful_copy_id`, `design`  FROM `pages_items` WHERE parent='".$id."'  AND `design`=".$page[0]->design." ORDER BY sort ASC";
			$d_mq_res = db::$mysqli->query($d_sql_q);
			if(!$d_mq_res) exit('bad_query '  . $d_sql_q);
			while ($obj = $d_mq_res->fetch_object()) {	$d_all_page_items[] = $obj; }
			
			if (isset($d_all_page_items)){
				//переменные функции
				$url  =  $dcms->url; 
				$keywords = $page[0]->keyw;
				$title = $page[0]->title;
				$parent = $page[0]->parent;
				$description = $page[0]->descr;
				$name = $page[0]->name;
				$path = $page[0]->path;
				$brunch = $dcms->func->segment_url($url);
				
				//bar
				//if ($this->is_login() == true) 	echo $this->show_bar($name,$url,$id,$dcms,$page[0]->design);
				
				//Перебор всех элементов страницы
				foreach($d_all_page_items as $d_iid)
				{
					if ((isset($d_iid->ful_copy_id)) && ($d_iid->ful_copy_id != 0 )){ //элементы ссылающиеся на другие
						$copy_text = $dcms->db->select("designes_items","id='".$d_iid->ful_copy_id."'");
						eval('?>'.$copy_text[0]->text);
					} else {
						if ($d_iid->komp == 0)	{ eval('?>'.$d_iid->text);	} // элементы текст / php
						else { // элементы  компоненты
							$use_com = $d_iid->komp;
							$com = $dcms->db->select("coms","id='".$use_com."'"); 
							$com_filds = $dcms->db->select("coms_fields","parent='".$com[0]->id."' AND type=14"); 
							//print_r($com_filds); //!
							if ($com[0]->query == '') $query = 'ORDER BY d_sort ASC'; else $query = $com[0]->query;
							{
								$tab_name = $com[0]->table_name;
								eval('?>'.$com[0]->code);
								$pagination='';
								$addp_sql='';
									/* pagination*/
									
									$sqlcount = db::$mysqli->query("SELECT id FROM ".$tab_name." WHERE page_item=".$d_iid->id); 
									$rowcount = $sqlcount->num_rows;
									
									$total = $rowcount[0]; // всего записей
									//echo $total;	
									if ((isset($page_size)) && ($total > $page_size )){ //Пагинация доступна под $pagination;
										$pagination = '<ul class="pagination">';
										$pahe_count = ceil($total/$page_size); 
										$page_num = 0;
										$p_min = 0;
										$p_count = $page_size;
										if ((isset($_GET['page'])) && ($_GET['page'] <> 1)) {
											$p_min = $_GET['page']*$page_size-$page_size;
											$p_count = $page_size;
										}
										//echo 'min'.$p_min;
										//echo 'max'.$p_count; 
										$addp_sql = 'LIMIT '.$p_min.', '.$p_count;
										//echo $addp_sql;
										for($pgi=0; $pgi<$pahe_count; $pgi++){
											$page_num++;
											if ((isset($_GET['page'])) && ($_GET['page'] == $page_num)) {
												$pagination  .= ' <li class="active"><a href="?page='.$page_num.'">'.$page_num.'</a></li>';
											}
											else
											{
												if ((!isset($_GET['page'])) && (1 == $page_num)) {
													$pagination  .= ' <li class="active" ><a href="?page='.$page_num.'">'.$page_num.'</a></li>';
												}else{
													$pagination  .= ' <li><a href="?page='.$page_num.'">'.$page_num.'</a></li>';
												}
											}
										}
										$pagination .= '</ul>';
									}
									
									/* ens pagination*/
								if ($comp_query_all = $dcms->db->select($tab_name,"page_item='".$d_iid->id."' " . $query.' '.$addp_sql))
								{
									
									eval('?>'.$com[0]->list_prefix);
									foreach( $comp_query_all as $ci)
									{
										
										/*if((isset($com_filds[0]->param)) &&($com_filds[0]->param != 0)){ //com2
											$com_var = $com_filds[0]->enname;
											$ci->$com_var = "fake data"; 
											$com2 = $dcms->db->select("coms","id='".$com_filds[0]->param."'"); 
											$c2out .= $com2[0]->list_prefix;
											$tab_name2 = $com2[0]->table_name;
											if ($com[0]->query == '') $query2 = 'ORDER BY d_sort ASC'; else $query2 = $com2[0]->query;
											
											if ($comp_query_all2 = $dcms->db->select($tab_name2,"page_item='".$d_iid->id."' AND parent=".$ci->id." " . $query2.' '))
											{
												//echo '<pre>'; print_r($comp_query_all2 ); echo '</pre>';
												foreach( $comp_query_all2 as $ci2)
												{
													$c2out .= $com2[0]->item;
												}
											}
											$c2out .= $com2[0]->list_sufix;
											
											$ci->$com_var = $c2out; 
											$c2out ='';
										}
										*/
										eval('?>'.$com[0]->item);
				
									}
									eval('?>'.$com[0]->list_sufix);
								}
								else {
									if ($show_empty) {
										eval('?>'.$com[0]->list_prefix);
										eval('?>'.$com[0]->list_sufix);
									}
								}
								
							}
						}
					}
				}
			} else {
				echo "Нет содержимого";
			}
		}
		else
		echo 'no_index_page [ошибка отсутствия 404 страницы]';
	}
	
	
	
	function p404($dcms)
	{
		if ($page = $dcms->db->select("pages","path=404"))
		{
			header( 'HTTP/1.1 404 Not Found' );
			$this->print_page($page[0]->id,$dcms);
		}
		else
		{
			header( 'HTTP/1.1 404 Not Found' );
			echo 'Нет Страницы 404';
		}
	}
	

}

?>