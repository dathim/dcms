<?php

//продление сессий
if (isset($_COOKIE[ session_name() ])){
 setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60, '/');
}

// dcms/index.php
//Подключение модулей админки
require_once('config.php');
require_once('system/function.php');
require_once('system/output.php');
require_once('system/db.php');
$version = 'DCMS 4.62';

$username_1 = $username;
$password_1 = $password;



if((isset($dcms_debug_mod)) && ($dcms_debug_mod == "true")){
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

//создание объектов
$dcms  = new stdClass();
$dcms->out = new out;
$dcms->func = new func;
$dcms->db = new db;
$dcms->url = $url;
$dcms->use_rf_domain = false;
if (isset($use_rf_domain)) $dcms->use_rf_domain = $use_rf_domain;

if (!isset($max_uplads_size)) {
	$max_uplads_size = 500*1048576;
}
$dcms->max_uplads_size = $max_uplads_size;








//выводимый_модуль(По умолчанию, путь сейчас, права пользователя, данные модулей, классы осн. модудей)
if (isset($_SESSION['login'])){
	if (isset($_SESSION['rights'])) {
		$user = array($_SESSION['login'], $_SESSION['rights']); 
		if ($fake_admin) $user = array('c3VwZXJ1c2Vy','Андрей','dathin@gmail.com','1'); //!#
		$dcms->user = $user;
	}
}
else
{
	$user = array(99999, "test","","",4); //array('1',$_SESSION['login'],'Андрей','dathin@gmail.com','1'); //данные пользователя ИЗ СЕССИЙ
	if ($fake_admin) $user = array('c3VwZXJ1c2Vy','Андрей','dathin@gmail.com','1'); //!#
	$dcms->user = $user;
}

//подготовка модулей
//echo $hostname." ".$username_1." ".$password_1." ".$database;
if (!$dcms->db->connect($hostname,$username_1,$password_1,$database)) { echo 'dcms:'; include 'system/informers/db_not_conect.php'; exit(); } //Подключение к бд
$modules = $dcms->func->scan_modules();
$m_vars = $dcms->func->resort_mod_var($modules);
$true_parh = $dcms->func->segment_url($url); //Путь в админке
$dcms->path = $true_parh;
//$mname массив (модуль, класс, функция)

$p1=''; $p2='';
if ((isset($dcms->path[1])) && (isset($dcms->path[2]))) { $p1=$dcms->path[1]; $p2=$dcms->path[2]; }
if (($dcms->func->is_login($p1,$p2)) || ($fake_admin == true)) 
{
	//LOGS
	if (isset($_SERVER['REDIRECT_URL'])) $at = $_SERVER['REDIRECT_URL']; else $at = '/index.php/xz_path';
	$num_index = strripos($at,'/index.php/');
	$part_2 = substr($at,$num_index+11);
	$part_2 = trim($part_2,'/'); 
	$parts = explode('/',$part_2);
	
	if (isset($parts[0])){
		switch ($parts[0]) {
			case "page":
				$res1 = 'Страницы';
			break;
			case "maket":
				$res1 = 'Макеты';
			break;
			case "com":
				$res1 = 'Компоненты';
			break;
			case "files":
				$res1 = 'Файлы';
			break;
			case "users":
				$res1 = 'Пользователи';
			break;
		   default:
				$res1 = $parts[0];
		}
	}
	if (isset($parts[1])){
		$res1  .= '>';
		switch ($parts[1]) {
			case "c_edit":
				$res1 .= 'Редактор текста';
			break;
			case "edit":
				$res1 .= 'Редактирование';
			break;
			case "c_dell_com":
				$res1 .= 'Удаление';
			break;
			case "c_add":
				$res1 .= 'Добавление эл.';
			break;
			case "c_add_new":
				$res1 .= 'Новый макет';
			break;
			case "c_dell":
				$res1 .= 'Удаление макета';
			break;
			case "c_add_page":
				$res1 .= 'Новая страница';
			break;
			case "c_del_page":
				$res1 .= 'Удаление страницы';
			break;
			case "c_save_set":
				$res1 .= 'Обновление параметров';
			break;
			case "show_com":
				$res1 .= 'Просмотр компонента';
			break;
			case "edit_item":
				$res1 .= 'Редактирование в комп.';
			break;
			case "add_form":
				$res1 .= 'Добавление в комп.';
			break;
			case "c_del_items":
				$res1 .= 'Удаление в комп.';
			break;
			case "c_save_item":
				$res1 .= 'Сохранение в комп.';
			break;
			
			default:
				$res1 .= $parts[1];
		}
	}
	$at = $res1;
	$log = new stdClass();
 	if (isset($_SERVER['REDIRECT_QUERY_STRING'])) $ao = $_SERVER['REDIRECT_QUERY_STRING']; else $ao = '';
	if ($adnlogs = $dcms->db->select('admin_logs' ,"action_type <> '{$at}' ORDER BY date DESC LIMIT 0, 1")){	
		
		$log->date = date('Y-m-d G:i:s');
		$log->action_type = $res1;
		
		
		$log->action_object = $ao;
		$log->ip = $_SERVER['REMOTE_ADDR']; 
		if (isset($_GET['id'])) { $log->action_object  .= '?id= '.$_GET['id']; }		
		$log->user = base64_decode($dcms->user[0]);
		$dcms->db->insert("admin_logs",$log);
	} else {$log->date = date('Y-m-d G:i:s'); $dcms->db->insert("admin_logs",$log); }

	// END LOGS
	
	//чистый класс
	if (isset($true_parh[2]))
	{
		$tp = $true_parh[2];
		if (($tp[0] == 'c') && ($tp[1] == '_'))
		{
			$dcms->func->run_modul($mod_def,$true_parh,$user,$m_vars, $dcms,  $database); 
			exit();
		}
	}
	$uname1 = base64_decode($user[0]);
	$uip1 = $_SERVER['REMOTE_ADDR'];
	$ua1 = $_SERVER['HTTP_USER_AGENT'];
	$s_data = '';
	//Вывод страницы
	$dcms->out->print_ap_header($version,$dcms,$s_data,$true_parh);
	$dcms->out->print_ap_menu($m_vars,$dcms);
	$dcms->out->print_ap_end_header($dcms);
	$dcms->func->run_modul($mod_def,$true_parh,$user,$m_vars, $dcms, $database  ); //выводимый_модуль(По умолчанию, путь сейчас, права пользователя, классы осн. модудей)
	$dcms->out->print_ap_footer();

}
else
{
	$dcms->out->print_login($dcms);
}




