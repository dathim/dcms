<?
/*
Привет разработчик! 
Меня Зовут Андрей Таран(dathim@gmail.com) и я разработал эту системму управления!
Дизайн Creatoff

30.8.2013  Доработан режим пользователя редактор
 
*/
$url = 'http://dcms.test/'; //must end "/"
$path_name_parser = "";   // обработка url путей
//ru,com.. - 
//rf  -

$hostname = 'localhost';
$username = 'mysql';
$password = 'mysql';
$database = 'dcms4'; 
$version = '4.72 GAMMA'; 
$mod_def = 'page';

//new 4.32
$dcms_debug_mod = true; // default false;
$url_save_mod = true;  // default true;

//new 4.50131
$fake_admin = true; // default false;


$use_rf_domain = false; 
$max_uplads_size = 11*1048576 ; // default unlim;

ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 3600);
session_set_cookie_params(3600);

ini_set('session.use_cookies', 1);
ini_set('session.use_trans_sid', 1);
ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'] .'/session');
session_start();
?>