<?php
//dcms4 Taran Andrey

//запуск сессий



//Подключение модулей админки
require_once("dcms/config.php");
require_once('dcms/system/function.php');
require_once('dcms/system/output.php');
require_once('dcms/system/db.php');

//debug_mod
if((isset($dcms_debug_mod)) && ($dcms_debug_mod == "true")){
	//echo "<script>console.log('Вывод ошибок и предупреждений php')</script>";
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

//создание объектов
$dcms  = new stdClass();
$dcms->out = new out;
$dcms->func = new func;
$dcms->db = new db;
$dcms->url = $url;

if (!isset($url_save_mod)) $url_save_mod = false;
if ($url_save_mod) { 
	//print_r($_GET);
	foreach($_GET as $get_item){
		$_GET[key($_GET)] = preg_replace("/[^a-zA-Zа-яA-Z-,_ 0-9]/", "", $get_item);
	}
}

if (!$dcms->db->connect($hostname,$username,$password,$database)) { include 'dcms/system/informers/db_not_conect.php'; exit(); } //Подключение к бд

//Получаем путь
$branch = $dcms->func->segment_url($url);
	
if (count($branch) == 1)
{
	if (!isset($branch[0])) 
	{
		$dcms->func->print_page(1,$dcms); //вывод главной о министерстве 
	}	
	else
	{
		if ($res = $dcms->db->select("pages","path='".$branch[0]."' AND parent='1'"))
		{
		$dcms->func->print_page($res[0]->id,$dcms); //вывод страниц уровня 1 
		}
		else
		{
		$dcms->func->p404($dcms);
		}
	}
}
else
{
	
	$Q = "path IN (";
	foreach($branch as $b)
		{
			$Q .= "'".$b. "', ";
		}
	$Q=substr_replace($Q, '', -2);
	$Q .= ')';
	if ($res = $dcms->db->select("pages",$Q,'path, parent, id'))
	{
		$select_p=1;
		$dcms->func->print_page($dcms->func->serch_tru_path($select_p, $res, $branch,0,0,$dcms),$dcms); // вывод страниц уровня 2 и выше
	}
	else
	{
		$dcms->func->p404($dcms);//$core->p404();
	}
}






?>