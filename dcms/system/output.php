<?php
// dcms/system/output.php
Class out {
	function print_ap_menu($m_vars,$dcms) // Печать меню
	{
		echo '<div class="left">
';
		foreach($m_vars as $mv)
		{
			if ((isset($mv['rights'])) && ($mv['rights'] >= $dcms->user[1])) //Проверка прав на модуль
			{
				if ((isset($dcms->path[1])) && ($dcms->path[1] == $mv['enname']))
				{
					echo "<a href='{$dcms->url}dcms/{$mv['enname']}' rel='tooltip' data-original-title='{$mv['runame']}' data-placement='bottom' class='{$mv['class']} active'></a>";
				}
				else
				{
					echo "<a href='{$dcms->url}dcms/{$mv['enname']}' rel='tooltip' data-original-title='{$mv['runame']}' data-placement='bottom' class='{$mv['class']}'></a>";
				}
				/*printf('					<a href="%s">%s</a>
', $mv['enname'], $mv['runame']);*/
			}
		}
		
		echo "<a   href='{$dcms->url}' target='_blank'  rel='tooltip' data-original-title='К сайту' data-placement='bottom' class='_to_site'></a>";
			echo "<a   href='http://dathim.ru/help_v4' target='_blank'  rel='tooltip' data-original-title='Справка' data-placement='bottom' class='_help'></a>";
		echo '	</div>';
	}
	
	function print_ap_header($version=4,$dcms,$refer,$true_parh) {
		$mod_name = '';
		function stringToColorCode($str) {
		  $code = dechex(crc32($str));
		  $code = substr($code, 0, 6);
		  return $code;
		}
		 
		
		 
		 
		$acent_color = stringToColorCode($_SERVER['HTTP_HOST']);		
		
		if (isset($true_parh[1])) $mod_name = $true_parh[1];
		$username = $dcms->user;	
		echo 
		'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>'.$version.' '.$mod_name.'</title>

			<link rel="stylesheet" type="text/css" href="'.$dcms->url.'dcms/style/rama.css" />
			<link rel="stylesheet" type="text/css" href="'.$dcms->url.'dcms/style/style.css" />
			<link rel="stylesheet" type="text/css" href="'.$dcms->url.'dcms/style/bootstrap.min.css" />
			<link href="//fonts.googleapis.com/css?family=Open+Sans:400,300&subset=latin,cyrillic" rel="stylesheet" type="text/css" />
			<link rel="shortcut icon" href="'.$dcms->url.'dcms/favicon.png" type="image/x-icon" />

			
			

			<script type="text/javascript" src="'.$dcms->url.'dcms/js/jquery.js"></script>
			<script type="text/javascript" src="'.$dcms->url.'dcms/js/jquery.form.js"></script>
			<script type="text/javascript" src="'.$dcms->url.'dcms/js/jquery_ui.js"></script>
			<script type="text/javascript" src="'.$dcms->url.'dcms/js/bootstrap-tooltip.js"></script>
			<script type="text/javascript" src="'.$dcms->url.'dcms/js/bootstrap.min.js"></script>
			<script type="text/javascript">var surl = \''.$dcms->url.'\';</script>
			<script type="text/javascript" src="'.$dcms->url.'dcms/js/dcms.js"></script>
			<script type="text/javascript" src="'.$refer.'"></script>
			
			<script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
			<script src="'.$dcms->url.'dcms/js/bootstrap-datetimepicker.js"></script>
			<style type=text/css>
			div.block:first-child h2{
				    border-top: 2px #'.$acent_color.' solid;
			}
			.block:first-child {
				border-top: 2px #'.$acent_color.' solid;
			}
			
			</style>
			
			
		</head>
		<body>
			<div class="layout">
				<div class="header">
					<div class="x960">
						<div class="right">
							<a href="'.$dcms->url.'dcms/page/c_exit" class="_logout" rel="tooltip" data-original-title="Выход '.base64_decode($username[0]).'" data-placement="bottom"></a>
						</div>
						';	
						
		
						
	}
	
	function print_ap_end_header($dcms) 
	{
		// 
		echo '
		
	
		
		
		<div class="left">
		<div class="notifications">
			<span style="display: none !important;"></span>
		</div>
	</div>
			</div>
		</div>
		<div class="main">
			<div class="x960">';	
	}
	
	function print_ap_footer() 
	{
		echo '
		</div>
		</div>
				
		</body>
		</html>';	
	}
	
	function print_login($dcms) 
	{
		$save_path = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$err = '';
		if (isset($_SESSION['try'])) {
			$ost = 30-$_SESSION['try'];
			if ($ost==0) exit('limit login');
			$err = "<div class='note'><p>Вы неправильно ввели пароль!
			<a href='{$dcms->url}dcms/users/c_send_new_pas'>Восстановить</a></p>
			</div>";
		
		} else  {
		 	$err = "<div class='note'><p><a href='{$dcms->url}dcms/users/c_send_new_pas'>Восстановить пароль</a></p></div>";
		} 
		echo "
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
			<html>
			<head>
				<title>DCMS 4</title>
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
				<link rel='stylesheet' type='text/css' href='{$dcms->url}dcms/style/style.css' />
				<link rel='stylesheet' type='text/css' href='{$dcms->url}dcms/style/bootstrap.min.css' />
				<script type='text/javascript' src='{$dcms->url}dcms/js/jquery.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/jquery.form.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/bootstrap-tooltip.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/bootstrap.min.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/dcms.js'></script>
				<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300&subset=latin,cyrillic' rel='stylesheet' type='text/css' />
			</head>
			<body><div class='layout'>
				<div class='login'>	
					<div class='block'>
						<h2>Авторизация</h2>
						<form action='{$dcms->url}dcms/users/c_login' method='POST'>
						
							<p>
								<input type='text' value='' name='dcms_userlogin' style='width:259px;' placeholder='Пользователь' />
							</p>
							<p>
								<input type='hidden'  name='save_path' value='{$save_path}' />
								<input type='password'  name='dcms_password' style='width:202px;' value='' placeholder='Пароль' />
								<input type='submit' value='Войти'>
							
							
							</p>
						</form>
						{$err}	
					</div>
				</div>
			</div></body>
			</html>
		
		";	
	}
	
	function print_repair($dcms) 
	{
		$err = '';
		if (isset($_SESSION['try'])) {
			$ost = 30-$_SESSION['try'];
			if ($ost==0) exit('limit login');
			$err = "<div class='note'><p>Попыток: (". $ost."),  <a href='{$dcms->url}dcms/'>назад</a></p></div>";
		
		}
		echo "
		
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
			<html>
			<head>
				<title>DCMS 4</title>
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
				<link rel='stylesheet' type='text/css' href='{$dcms->url}dcms/style/style.css' />
				<link rel='stylesheet' type='text/css' href='{$dcms->url}dcms/style/bootstrap.min.css' />
				<script type='text/javascript' src='{$dcms->url}dcms/js/jquery.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/jquery.form.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/bootstrap-tooltip.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/bootstrap.min.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/dcms.js'></script>
				<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300&subset=latin,cyrillic' rel='stylesheet' type='text/css' />
			</head>
			<body><div class='layout'>
				<div class='login'>	
					<div class='block'>
						<h2>Восстановление пароля</h2>
						<form action='{$dcms->url}dcms/users/c_send_new_pas2' method='POST'>
							<p>
								<input type='text' value='' name='dcms_email' style='width:259px;' placeholder='Email' /><input type='submit' value='Выслать'>
							</p>
							<p>
								Обратится к разработчикам: <a href='http://dathim.ru/contacts'>dathim.ru</a> 
							</p>
						</form>{$err}
					</div>
				</div>
			</div></body>
			</html>	";	
	}
	
	function pas_send($dcms, $text_out) 
	{
		echo "	
			<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
			<html>
			<head>
				<title>DCMS 4</title>
				<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
				<link rel='stylesheet' type='text/css' href='{$dcms->url}dcms/style/style.css' />
				<link rel='stylesheet' type='text/css' href='{$dcms->url}dcms/style/bootstrap.min.css' />
				<script type='text/javascript' src='{$dcms->url}dcms/js/jquery.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/jquery.form.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/bootstrap-tooltip.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/bootstrap.min.js'></script>
				<script type='text/javascript' src='{$dcms->url}dcms/js/dcms.js'></script>
				<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300&subset=latin,cyrillic' rel='stylesheet' type='text/css' />
			</head>
			<body><div class='layout'>
				<div class='login'>	
					<div class='block'>
						<h2>{$text_out}</h2>
						<div class='note'><p><a href='{$dcms->url}dcms/'>назад</a></p></div>
	
					</div>
				</div>
			</div></body>
			</html>
		
		";	
	}


}