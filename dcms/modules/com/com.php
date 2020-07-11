<?php 
// dcms/modules/com/com.php

Class com {
function constructor()
	{
		$out = '';
		if ($maketlist = $this->db->select("coms", "id>0 ORDER BY id ASC"))
		{
			$out = '<ul>';
			foreach($maketlist as $ml)
			{
				$count_records = $this->db->select($ml->table_name, "", "COUNT(1)");
				$cvar="COUNT(1)";
				if ($ml->name == 'page_extension') {
					$out .= '<li><a class="_name" href="'.$this->url.'dcms/com/edit?id='.$ml->id.'"><b>'.$ml->name.' ('.$count_records[0]->$cvar.')</b></a></li>';
				}  else {
					$out .= '<li><a class="_name" href="'.$this->url.'dcms/com/edit?id='.$ml->id.'">'.$ml->name.' ('.$count_records[0]->$cvar.')</a></li>';
				}
			}
			$out .= '</ul>';
		}
		else
		{
			$out = '<h3>Компонентов нет</h3>';
		}
		echo '
				<div class="tree ucol">'.$out.'
				<span><a href="'. $this->url.'dcms/com/add" class="_new"><b></b>Добавить компонент</a></span></div>
			
				<div class="content ucol">
					<div class="block">
		';
		
	}

	function destructor() 
	{
		echo '</div></div></div>';
	}
	
	function index(){
		echo '<h2>Компоненты</h2><p>Данный раздел предназначен для опытных пользователей</p>';
	}
	
	
	function add(){
			$err = '';
			if ((isset($_GET['err'])) && ($_GET['err'] == 'name')) $err = "<b>Данное имя недопустимо</b>";
			echo '
			<h2>Добавление компонента</h2>
			'. $err.'
			<form action="'. $this->url .'dcms/com/c_create_new" method="POST">
			<table><tr><td>Название компонента (Новости)</td>
			<td><input type="text" name="com_name" value=""></td>
			<td>Шаблон: 
			<select>
				<option value="0">нет</option>
			</select>
			
			
			</td></tr>
			</table><input type="submit"  value="Создать"/></form>'; 
	}
	
	function edit()
	{
		if ($com = $this->db->select("coms","id='".$_GET['id']."'"))
		{
			if ($com_fields = $this->db->select("coms_fields","parent='".$_GET['id']."' ORDER BY sort ASC"))
			{
				//name
				if ($com[0]->name == 'page_extension') {
					echo "<h2>{$com[0]->name}</h2>";
				} else {
					echo '<form  action="'. $this->url .'dcms/com/c_rename?id='.$_GET['id'].'" method="POST">';
					echo "<a href='javascript:void(0)' rel='tooltip' data-original-title='Сохранить' data-placement='left' class='_mastername'></a>
					<h2 class='_wide'><input onBlur='javascript:save_ajax(this)' type='text' value='{$com[0]->name}' name='name' placeholder='Название' /></h2>";
					echo '</form>';	
				}
				//tools
				echo '<div class="tools">';	
				if ($com[0]->name != 'page_extension') { echo '<a href="javascript:go(\''. $this->url .'dcms/com/c_dell_com?id='.$_GET['id'].'\')">Удалить компонент</a>'; }
				echo '<a href="'. $this->url .'dcms/com/show_all?id='.$_GET['id'].'">Просмотреть</a>';
				if (file_exists("../uploads/".$com[0]->table_name)){
					echo '<a href="javascript:void(0)">Хранение файлов работает</a>';
				}
				else{
					echo '<a href="'. $this->url .'dcms/com/c_add_dir?id='.$_GET['id'].'">Создать директорию</a>';
				}
				//echo '<a href="javascript:window.open(\''. $this->url .'dcms/com/c_show_data?id='.$_GET['id'].'\',\'\',\'width=900,height=500\'); void(0)"">Посмотреть данные</a>';
				echo '</div>';
				
				//tamles
					echo "<table class='com_table'>
					<tr>
						<td>id</td>
						<td>Название</td>
						<td>Параметеры ?</td>
						<td>Таб.</td>
						<td>Ред.</td>
						<td>Сорт.</td>
						<td>Обозначения</td>
						
						<td>Тип данных</td>
						<td>Действие</td>
					</tr>";
					$max_sort = 200;
					foreach($com_fields as $p)
					{
						echo '<tr>'; 
							echo '<td>'.$p->id.'</td>';
							echo '<td>
							<form class="myForm" action="'. $this->url .'dcms/com/c_save_field?id='.$_GET['id'].'&eid='.$p->id.'" method="POST">
							<input type="text" name="comname" value="'.$p->name.'" /></td>';
							echo '<td>	<input type="text" name="param" value="'.$p->param	.'" /></td>';
							if ($p->show_table == 1)
							{
							echo '<td>	<input type="checkbox" style=" width:20px;"  name="show_table" value="1" checked/></td>';
							}
							else
							{
							echo '<td>	<input type="checkbox" style=" width:20px;" name="show_table" value="1" /></td>';
							}
							
							if ($p->show_edit == 1)
							{
							echo '<td>	<input type="checkbox" style=" width:20px;"  name="show_edit" value="1" checked/></td>';
							}
							else
							{
							echo '<td>	<input type="checkbox" style=" width:20px;" name="show_edit" value="1" /></td>';
							}
							echo '<td>	<input type="text" style=" width: 30px; " name="sort" value="'.$p->sort	.'" /></td>';
							echo '<td>'.$p->enname.'<br />&lt?=$ci->'.$p->enname.'?&gt</td>';
							
							if ($p->type == 1) echo '<td>VARCHAR - 120</td>';
							if ($p->type == 2) echo '<td>VARCHAR - 500</td>';
							if ($p->type == 3) echo '<td>TEXT - 65535</td>';
							if ($p->type == 4) echo '<td>LONGTEXT - 4294967295</td>';
							if ($p->type == 5) echo '<td>DATE - 1000-01-01</td>';
							if ($p->type == 6) echo '<td>DATETIME - 1000-01-01 00:00:00</td>';
							if ($p->type == 7) echo '<td>TINYINT - -128 до 127</td>';
							if ($p->type == 8) echo '<td>INT</td>';
							if ($p->type == 9) echo '<td>DOUBLE </td>';
							if ($p->type == 10) echo '<td>FILE IMG (resize 1_, 2_)</td>';
							if ($p->type == 11) echo '<td>FILE (VARCHAR - 120 /uploads)</td>';
							if ($p->type == 12) echo '<td>Off/On</td>';
							if ($p->type == 13) echo '<td>COM select</td>';
							if ($p->type == 14) echo '<td>COM sub COM</td>';
							
							if (($p->enname == 'name') || ($p->enname == 'sys_date') || ($p->enname == 'd_sort'))
							{
								echo '<td><input  style=" margin: 0;" type="submit"  value="Save"></form></td>';
							}
							else
							{
								echo '<td><input style=" margin: 0;"  type="submit"  value="Save"> <a href="javascript:go(\''. $this->url .'dcms/com/c_del_field?id='.$p->id.'&cid='.$_GET['id'].'&tab='.$com[0]->table_name.'&enname='.$p->enname.'\')">Удалить</a></form></td>';
							}
						echo '</tr>'; 
						$max_sort = $p->sort+100;
					}
				//add
					$datatype = '
					<select style=" width: 100px; " name="datatype">
						<option value="1">VARCHAR - 120</option>
						<option value="2">VARCHAR - 500</option>
						<option value="3">TEXT - 65535</option>
						<option value="4">LONGTEXT - 4294967295</option>
						<option value="5">DATE - 1000-01-01</option>
						<option value="6">DATETIME - 1000-01-01 00:00:00</option>
						<option value="7">TINYINT - -128 до 127</option>
						<option value="8">INT - -2147483648 до 2147483647</option>
						<option value="9">DOUBLE - -1,7976931348623157E+308 до -2,2250738585072014E-308</option>
						<option value="10">FILE IMG(VARCHAR - 120 /uploads)</option>
						<option value="11">FILE (VARCHAR - 120 /uploads)</option>
						<option value="12">Off/On (Галочка)</option>
						<option value="13">COM select (Из списка)</option>
						<option value="14">COM sub COM (подкомпонент)</option>
					</select>
					
					
					';  // чтсло, строка 150,  текст, Файл, подстановка с компонентов поля по id (int)
					
					echo '<form class="myForm" action="'. $this->url .'dcms/com/c_create_field?id='.$_GET['id'].'&eid='.$p->id.'" method="POST">';
						echo '<td>&nbsp;</td>';
						echo '<td><input type="text" name="name"/></td>';
						echo '<td><input type="text" name="param"/></td>';
						echo '<td><input type="checkbox" style=" width:20px;" name="show_table" value="1" /></td>';
						echo '<td><input type="checkbox" style=" width:20px;" name="show_edit"  checked value="1" /></td>';
						echo '<td><input style=" width: 40px; "  type="text" value="'.$max_sort.'" name="sort"/></td>';
						echo '<td><input type="text" name="enname"/></td>';
						
						echo '<td>'.$datatype.'</td>';
						echo '<td><input style=" margin: 0;"  type="submit"  value="Add" > </td>';
					echo '</form>';
				
				echo "</table>";
				
				
				
		
				
				//fields
				echo '<form class="rcform myForm"  action="'. $this->url .'dcms/com/c_edir_aditional_parameters?id='.$_GET['id'].'" method="POST">';
				
				echo '<h3>Подзапрос: </h3>';
				echo 'SELECT * FROM '.$com[0]->table_name .' WHERE id>0 <input style="width:200px; " type="text" name="query" value="'.$com[0]->query.'"/><br />';
				echo '<h3>Путь к файлам: &lt;?=$url?&gt;uploads/'.$com[0]->table_name.'/</h3><br />';
				
				echo '<h4>Код элемента</h4>'; 
				echo '<textarea id="teatarea_item" name="item" style="display:none;">'.htmlspecialchars($com[0]->item).'</textarea>';
				echo '<div class="ace_item" id="ace_com_item">'.htmlspecialchars($com[0]->item).'</div>';
			
				echo '<h4>Шапка списка</h4>';
				echo '<textarea id="com_prefix" name="list_prefix" style="display:none;" >'.htmlspecialchars($com[0]->list_prefix).'</textarea>';	
				echo '<div class="ace_item" id="ace_com_prefix" >'.htmlspecialchars($com[0]->list_prefix).'</div>'; 
				
				echo '<h4>Подвал списка</h4>';
				echo '<textarea  id="list_sufix" name="list_sufix" style="display:none;"  >'.htmlspecialchars($com[0]->list_sufix).'</textarea>';
				echo '<div class="ace_item" id="ace_com_sufix" >'.htmlspecialchars($com[0]->list_sufix).'</div>'; 
				
				echo '<h4>Доп. код </h4>';
				echo '<textarea  id="com_code" name="code"  style="display:none;" >'.htmlspecialchars($com[0]->code).'</textarea>';
				echo '<div class="ace_item" id="ace_com_code" style="  margin-bottom: 20px;">'.htmlspecialchars($com[0]->code).'</div>'; 
					
				echo '<input type="submit"  onClick="ready_data();" value="Сохранить">';
				echo '</form>
				<script src="'. $this->url.'dcms/plugins/ace/ace.js" type="text/javascript" charset="utf-8"></script>
						<script>
							 var ace_com_item = ace.edit("ace_com_item");
							 ace_com_item.setTheme("ace/theme/eclipse");
							 ace_com_item.getSession().setMode("ace/mode/php");
							 ace_com_item.setOptions({maxLines: Infinity});
							
							 var ace_com_prefix = ace.edit("ace_com_prefix");
							 ace_com_prefix.setTheme("ace/theme/eclipse");
							 ace_com_prefix.getSession().setMode("ace/mode/php");
							 ace_com_prefix.setOptions({maxLines: Infinity});
							
							 var ace_com_sufix = ace.edit("ace_com_sufix");
							 ace_com_sufix.setTheme("ace/theme/eclipse");
							 ace_com_sufix.getSession().setMode("ace/mode/php");
							 ace_com_sufix.setOptions({maxLines: Infinity});
							
							 var ace_com_code = ace.edit("ace_com_code");
							 ace_com_code.setTheme("ace/theme/eclipse");
							 ace_com_code.getSession().setMode("ace/mode/php");
							 ace_com_code.setOptions({maxLines: Infinity});
							
							function ready_data(){
								$("#teatarea_item").html(escapeHtml(ace_com_item.getSession().getValue()));
								$("#com_prefix").html(escapeHtml(ace_com_prefix.getSession().getValue()));
								$("#list_sufix").html(escapeHtml(ace_com_sufix.getSession().getValue()));
								$("#com_code").html(escapeHtml(ace_com_code.getSession().getValue()));
								return true;
							}
						</script>	
						
						<style type="text/css" media="screen">
							 .ace_item{ 
								width: 100%;
								//height:400px;
							 }
						</style>
				
				
				';

			}
		}
	}
	
	function show_all()
	{
		if ($com = $this->db->select("coms","id='".$_GET['id']."'")){
			echo '<h2>'.$com[0]->name.'</h2>'; 
			echo '<div class="tools">';	
				echo '<a href="'. $this->url .'dcms/com/edit?id='.$_GET['id'].'">Назад</a>';
			echo '</div>';
			//if ($all = $this->db->select($com[0]->table_name,"id>0","id, name, page_item")){
			 if ($all = $this->db->select($com[0]->table_name .' LEFT JOIN pages_items ON '.$com[0]->table_name.'.page_item = pages_items.id
			  LEFT JOIN pages  ON pages_items.parent = pages.id',  ' pages_items.id>0 ',
			 $com[0]->table_name.'.id, @a:=pages.name,'.$com[0]->table_name.'.name')){
				//print_r($all);
			 
				echo "<table class='com_table'>";
				echo '<tr><th>id</th><th>name</th><th>Страница</th><th></th></th>';
				$n2 ="@a:=pages.name";
				foreach($all as $im){
					echo '<tr>
					<td>'.$im->id.'</td>
					<td>'.$im->$n2.'</td>
					<td>'.$im->name.'</td>
					<td><a href="'. $this->url .'dcms/com/del_item?id='.$_GET['id'].'&delid='.$im->id.'">Удалить</a></td>
					</tr>';
				}
				echo "</table>";
			}
		}
	}
	
	function del_item()
	{
		if ($com = $this->db->select("coms","id='".$_GET['id']."'")){
			if ($this->db->delete($com[0]->table_name, 'id="'.$_GET['delid'].'"'))
			{
				header('Location: '.$this->url.'dcms/com/show_all?id='.$_GET['id'] );
			}
		}
	}
	
}

Class clear{

	function c_dell_com()
	{
		$tab = $this->db->select("coms","id='".$_GET['id']."'");
		if ($this->db->delete('coms', 'id="'.$_GET['id'].'"',1))
		{
			if ($this->db->delete('coms_fields', 'parent="'.$_GET['id'].'"',99999))
			{
				$sql = 'DROP TABLE ' .$tab[0]->table_name ;	
				$query = mysql_query($sql);
				if ($query) 	
				{
					header('Location: '.$this->url.'dcms/com');
				}	else echo 'Ошибка удаления ' . $tab[0]->com_table_name;
			}	else echo 'Ошибка удаления coms_fields';
		}
	}

	function c_edir_aditional_parameters()
	{
		$des->list_prefix  = $_POST['list_prefix'];
		$des->list_sufix = $_POST['list_sufix'];
		$des->query = $_POST['query'];	
		$des->code = $_POST['code'];	
		$des->item = $_POST['item'];
		$this->db->update("coms","id='".$_GET['id']."'",$des); 	
		echo '<script type="text/javascript">location.replace("'. $this->url .'dcms/com/edit?id='.$_GET['id'].'");</script>';	
	}	

	function c_rename() //Переименование компонента
	{
		$name = $_POST['name'];
		$name = substr(strip_tags($name),0,51);
		if ($name == '')
		{
			$des->name = "No name";
			if ($this->db->update("coms","id='".$_GET['id']."'",$des)) 	
			header('Location: '.$this->url.'dcms/com/edit?id='.$_GET['id'] );
		}
		else
		{
			$des->name = $name;
			if ($this->db->update("coms","id='".$_GET['id']."'",$des)) 	
			header('Location: '.$this->url.'dcms/com/edit?id='.$_GET['id'] );
		}
	}
	
	function c_create_new() //Создание компонента
	{
		$name = $_POST['com_name'];
		$name = substr(strip_tags($name),0,51);
	
		
		if (($com = $this->db->select("coms","name='".$name."'")) || ($name == ''))
		{
			echo '<script type="text/javascript">location.replace("'.  $this->url .'dcms/com/add?err=name");</script>';		
		}
		else
		{
			//Создание записи в coms
			$enname = 'com_'.$this->func->ru_en_translite($name);
			$addi->name = $name;
			$addi->table_name = $enname;
			$addi->item = ' ';
			$addi->code = '<?
$show_empty = true;    // Показать шапку и подвал пустого компонента
$page_size=1000;          // Количество элементов на странице  ($pagination)

?>

			';
			$this->db->insert("coms",$addi);
			$this_id = $this->db->select("coms","id>0 ORDER BY id DESC LIMIT 1");
			
			//Добавление таблицы
			$sql = "CREATE TABLE IF NOT EXISTS `".$enname."` (`id` INT NOT NULL AUTO_INCREMENT , `page_item` INT, `d_sort` INT, `sys_date` DATE, `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci, PRIMARY KEY (`id`))"; //код запроса
			$query = mysql_query($sql); 
			if ($query)  //если табоица создано то запишем что создана
			{ 	
				//вставка данных о созданой таблице
				$com->table_name = $enname;
				$com->query = 'ORDER BY d_sort ASC';
				if ($this->db->update("coms","id='".$this_id[0]->id."'", $com))
				{
					//вставка name
					$add->name = 'Заголовок'; 
					$add->enname = 'name';
					$add->show_table = 1;
					$add->show_edit = 1;
					$add->sort = 100;
					$add->parent = $this_id[0]->id;
					$add->type = 2;
					$this->db->insert("coms_fields",$add);
					
					//d_sort
					$add->name = 'Сортировка';
					$add->enname = 'd_sort';
					$add->show_table = 0;
					$add->show_edit = 0;
					$add->sort = 200;
					$add->parent = $this_id[0]->id;
					$add->type = 8;
					$this->db->insert("coms_fields",$add);
					
					//date
					$add->name = 'Дата создания';
					$add->enname = 'sys_date';
					$add->show_table = 0;
					$add->show_edit = 0;
					$add->sort = 300;
					$add->parent = $this_id[0]->id;
					$add->type = 5;
					$this->db->insert("coms_fields",$add);
					
					
					//folder
					$newdir = '../uploads/'.$enname;
					if (!mkdir($newdir , 0777, true)) {
						echo 'Не удалось создать директории...';
					}
					//chown($newdir,'profi'); // указываем пользователя по имени
					//chgrp($newdir,'profi'); // указываем группу по имени
					
					//создано
					echo '<script type="text/javascript">location.replace("'. $this->url .'dcms/com/edit?id='. $this_id[0]->id.'");</script>';
				}
				else { 	echo '<h2>Ошибка</h2>Создание записи в coms_fields<br>';}
			}
			else 
			{	
				echo '<h2>Ошибка</h2><br>[CREATE TABLE] '.$sql.'<br>';
			}		
		}
	}

	function c_save_field() { //Сохранение параметров полей
		$com_id = $_GET['id'];
		$com_field_id = $_GET['eid'];
		if (isset($_POST['comname']))    $save->name = $_POST['comname'];
		if (isset($_POST['param']))      $save->param = $_POST['param'];
		if (isset($_POST['show_table'])) $save->show_table = 1;  else $save->show_table = 0;
		if (isset($_POST['show_edit']))  $save->show_edit = 1;  else $save->show_edit = 0;
		if (isset($_POST['sort']))       $save->sort = $_POST['sort'];
		if ($this->db->update("coms_fields","id='". $com_field_id ."'",$save)) 	header('Location: '.$this->url.'dcms/com/edit?id='.$com_id );
	}
	
	function c_create_field() //Создание нового поля в компоненте
	{
		if ($_POST['name'] != ''){
			if ($_POST['enname'] != ''){ 
				$enname = trim($_POST['enname']); 
			} else {
				$enname  = $this->func->ru_en_translite($_POST['name']);
			}
			if ($enname == 'alter') $enname = 'f_alter';
			if ($enname == 'create') $enname = 'f_create';
			if ($enname == 'select') $enname = 'f_select';
			if ($enname == 'drop') $enname = 'f_drop';
			if ($enname == 'where') $enname = 'f_where';
			if ($enname == 'order') $enname = 'f_order';
			if ($enname == 'limit') $enname = 'f_limit';
			if ($enname == 'date') $enname = 'f_date';
			if ($enname == 'id') $enname = 'f_id';
		
			if ($povtor = $this->db->select("coms_fields","id>'{$_GET['id']}' AND enname = '{$enname}'")){
				echo "Конфликт имён <a href='{$this->url}dcms/com/edit?id={$_GET['id']}'>Назад</a>";
			}  
			else { 
				$add->name = $_POST['name'];
				$add->param = $_POST['param'];
				$add->enname = $enname;
				$add->type = $_POST['datatype'];
				$add->sort = $_POST['sort'];
				$add->parent = $_GET['id'];
				
				if (isset($_POST['show_table'])) $add->show_table = 1;  else $add->show_table = 0;
				if (isset($_POST['show_edit']))  $add->show_edit = 1;  else $add->show_edit = 0;
				$this->db->insert("coms_fields",$add);
				if ($_POST['datatype'] == 1) 	$type = 'VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci'; 
				if ($_POST['datatype'] == 2) 	$type = 'VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci';
				if ($_POST['datatype'] == 3) 	$type = 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
				if ($_POST['datatype'] == 4) 	$type = 'LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
				if ($_POST['datatype'] == 5) 	$type = 'DATE';
				if ($_POST['datatype'] == 6) 	$type = 'DATETIME';
				if ($_POST['datatype'] == 7) 	$type = 'TINYINT';
				if ($_POST['datatype'] == 8) 	$type = 'INT';
				if ($_POST['datatype'] == 9) 	$type = 'DOUBLE';
				if ($_POST['datatype'] == 10) 	$type = 'VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci'; 
				if ($_POST['datatype'] == 11) 	$type = 'VARCHAR( 120 ) CHARACTER SET utf8 COLLATE utf8_general_ci'; 
				if ($_POST['datatype'] == 12) 	$type = 'INT';
				if ($_POST['datatype'] == 13) 	$type = 'INT';
				if ($_POST['datatype'] == 14) 	$type = 'INT';
				$des = $this->db->select("coms","id='".$_GET['id']."'");
				$sql = 'ALTER TABLE `'.$des[0]->table_name.'` ADD `'.$enname.'` '.$type.';';
				$query = mysql_query($sql);
				if ($query) 
				{ 
					echo '<h2>Создано</h2>';
					echo '<script type="text/javascript">location.replace("'. $config->url .'dcms/com/edit?id='.$_GET['id'].'");</script>';
					header('Location: '.$this->url.'dcms/com/edit?id='.$_GET['id']);
				}
				else 
				{	
					echo '<br>[bad INSERT] '.$sql.'<br>';
				}
			}
		}
		else echo "Не введены данные <a href='{$this->url}dcms/com/edit?id={$_GET['id']}'>Назад</a>";
	}
	
	function c_add_dir(){ 
		if ($com = $this->db->select("coms","id='".$_GET['id']."'")){ 
			$newdir = '../uploads/'.$com[0]->table_name;
			if (!mkdir($newdir , 0777, true)) {
				echo 'Не удалось создать директории...';
			} 
			header('Location: '.$this->url.'dcms/com/edit?id='.$_GET['id'] );
		} else {echo "Компонент не найден";}
	}

	function c_del_field(){ //Удаление поя из таблицы
		echo 'c_del_field';
		$sql = 'ALTER TABLE `'.$_GET['tab'].'` DROP `'.$_GET['enname'].'`;';
			$query = mysql_query($sql);
			if ($query) {
				if ($this->db->delete('coms_fields', 'id="'.$_GET['id'].'"'))
				{
					header('Location: '.$this->url.'dcms/com/edit?id='.$_GET['cid'] );
				}
				else echo 'ERROR coms_fields del field';
			}
			else echo 'ERROR - ' .$sql;
	}
}