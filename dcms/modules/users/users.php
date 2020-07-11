<?php 
// dcms/modules/users/users.php

Class users {

	function constructor()
	{
		if ($users = $this->db->select("users", "id>0 ORDER BY id ASC"))
		{
			$out = '';
			foreach($users as $ml)
			{ 
				$u_ruls = "hacker";
				if ($ml->rights == 1) $u_ruls = "Админ";
				if ($ml->rights == 2) $u_ruls = "Редактор";
				if ($ml->rights == 3) $u_ruls = "Пользователь";
				if ((isset($_GET['id'])) && ($ml->id == $_GET['id'])) {
				$out .= '<li class="active"><a class="_name" href="'.$this->url.'dcms/users/edit?id='.$ml->id.'">'.$ml->name.' ('.$u_ruls.')</a></li>';
				}
				else {
				$out .= '<li><a class="_name" href="'.$this->url.'dcms/users/edit?id='.$ml->id.'">'.$ml->name.' ('.$u_ruls.')</a></li>';
				}
			}
			echo '
					<div class="tree ucol"><ul>'.$out.'</ul>
					<span><a href="'.$this->url.'dcms/users/c_add_new" class="_new"><b></b>Добавить пользователя</a></span>

					
					</div>
					
					<div class="content ucol">
						<div class="block">
			';
		}
	}
	
	function destructor() 
	{
		echo '</div></div>';
	}
	
	function index()
	{
		echo '<h1>Пользователи</h1>
			<p><b>Администраторы</b> — Имеют полный доступ<p>
			<p><b>Редакторы</b> — Только в раздел управления страницами и файлами, с ограниченной функциональностью<p>
			<p><b>Внешние пользователи</b>  —  Не имеют доступа к системе управления<p>
			<p><i><b>Внимание!</b> Для удаления Администратороа надо вывести его из статуса Администратора</i><p>
		';
	}
	
	function edit()
	{
		
		if (isset($_POST['login'])) {
			$save  = new stdClass();
			$save->login = $_POST['login'];
			if (isset($_POST['name']))  $save->name = $_POST['name'];
			if ((isset($_POST['password'])) && ($_POST['password'] != ''))  $save->password = $this->func->cript_f1($_POST['password']);
			if (isset($_POST['mail'])) $save->mail = $_POST['mail'];
			if (isset($_POST['rights'])) $save->rights = $_POST['rights'];
			$this->db->update("users","id='".$_GET['id']."'",$save); 	
		}
		
		if ($user = $this->db->select("users", "id='".$_GET['id']."'"))
		{
			echo '<h2>'.$user[0]->name .'</h2>';
			if ($user[0]->rights != 1) echo '<div class="tools"><a href="javascript:go(\''.$this->url.'dcms/users/c_dell?id='.$_GET['id'].'\')">Удалить пользователя</a></div>';
			
			//if ($user[0]->rights != 1)  echo '<div class="tools"><a href="javascript:go(\''. $this->url .'dcms/users/c_udell?id='.$_GET['id'].'\')">Удалить</a></div>';
				echo '<div class="pageitem">
					<div class="_head">
						<h3></h3>
					</div>
					
						<form class="myForm" action="'. $this->url .'dcms/users/edit?id='.$_GET['id'].'" method="POST">
						<div class="_body"><input type="text" name="login" value="'.$user[0]->login.'" /> <p> Логин</p></div>
						<div class="_body"><input type="text" name="name"  value="'.$user[0]->name.'" /> <p> Имя пользователя</p></div>
						<div class="_body"><input type="text" name="password" value="" /> <p> Пароль</p></div>
						<div class="_body"><input type="text" name="mail" value="'.$user[0]->mail.'" /> <p> Почта</p></div>
						<div class="_body"><select style=" width: 100px; " name="rights">';
							if ($user[0]->rights == 1) echo '<option value="1" selected>Администраторы </option>'; else echo '<option value="1">Администраторы </option>';
							if ($user[0]->rights == 2) echo '<option value="2" selected>Редакторы </option>'; else echo '<option value="2">Редакторы </option>';
							if ($user[0]->rights == 3) echo '<option value="3" selected>Внешние пользователи</option>'; else  echo '<option value="3">Внешние пользователи</option>';
						echo '</select> <p> Права доступа</p></div>
						<div class="_body"><input type="submit" value="Сохранить" /></div>
						</form>
					</div>
				</div>
			
			';
			
		}
	}
}

Class clear{
	function c_add_new(){
		$add->name = "Новый пользователь";	
		if ($this->db->insert("users",$add)) 
		{
			header('Location: '.$this->url.'dcms/users');	
		}

	}
	
	function c_udell(){
		if ($this->db->delete('users', 'id="'.$_GET['id'].'"'))
		{
			header('Location: '.$this->url.'dcms/users');	
		}
	}
	
	function c_send_new_pas()
	{ 
		$this->out->print_repair($this);
	}
	
	function c_send_new_pas2()
	{ 
		if (isset($_SESSION['try'])) $_SESSION['try'] = $_SESSION['try'] +1; else $_SESSION['try'] = 1; 
		$email = $this->func->check_string($_POST['dcms_email']);	
		if ($users = $this->db->select("users", "mail = '{$email}'  ORDER BY rights ASC LIMIT 0, 1 ")){
			//print_r($users);
			$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; 
			$max=8; 
			$size=StrLen($chars)-1; 
			$real_pas=null; 
			while($max--)  $real_pas.=$chars[rand(0,$size)]; 
			$cipt_pas = $this->func->cript_f1($real_pas);
			$users[0]->password = $cipt_pas;
			$this->db->update("users","id='".$users[0]->id."'",$users[0]); 
			$text = 'Для пользователя '.$users[0]->login.' новый пароль: '.$real_pas;
			if ($this->func->email_send_html($users[0]->mail,$text,"Password")){
				$this->out->pas_send($this,"Сообщение отправлено");
			}
			else {
				$this->out->pas_send($this,"Ошибка сообщение не отправлено!");
			}
		}
		else{
			$this->out->pas_send($this,"Ошибка почта не верна!");
		}
	}	
	
	function c_login()
	{ 
		sleep(1);
		
		function strip($text) {	//проверка вход. данных
			$srting = array("query","select","from","delete","insert","update",";","'",'"',"^","|","\n","\r","\p","<",">");
			$result = trim(htmlspecialchars(strip_tags(str_replace($srting,"",$text)))); 
			$result = htmlspecialchars(substr($result,0,50));
			return $result;
		}
		
		if ((isset($_POST['dcms_userlogin'])) && (isset($_POST['dcms_password']))) 
		{
			$login = $this->func->check_string($_POST['dcms_userlogin']);	
			$password = $this->func->check_string($_POST['dcms_password']);
			$query = "SELECT * FROM users WHERE password='".$this->func->cript_f1($password)."' AND login = '" .$login."'";
			//echo $query;
			$nme = db::$mysqli->query($query);
			if (isset($_SESSION['try'])) $_SESSION['try'] = $_SESSION['try'] +1; else $_SESSION['try'] = 1; 
			if ($nme)
			{
				$user = $nme->fetch_object();
				if (count($user->id)>0)
				{
					$_SESSION['login'] = base64_encode($login);
					$_SESSION['password'] = $this->func->cript_f1($password);
					$_SESSION['rights'] = $user->rights;
					if (isset($_SESSION['try'])) $_SESSION['try'] = 0; 
					if ($_SESSION['rights'] < 4){ 
						if ((isset($_POST['save_path'])) && ($_POST['save_path'] != '')){
							header('Location: '.$_POST['save_path']  ); 
						} else {
							header('Location: '.$this->url.'dcms/' ); 
						}
					} 	else { 	
					echo 2;
					header('Location: '.$this->url );
					}
				}
				else
				{
					header('Location: '.$this->url.'dcms/' );		//bad
				}
			}
			else
			{
				header('Location: '.$this->url.'dcms/' );		//bad
			}
		}
		else 
		{
			header('Location: '.$this->url.'dcms/' );	//bad 
		}
	}
	
	function c_exit()
	{	
		session_unset();
		header('Location: '.$this->url );
	}	
	
	function c_dell()
	{	
		$this->db->delete('users', 'id="'.$_GET['id'].'"');
		header('Location: '.$this->url.'dcms/users'  );
	}
	
}