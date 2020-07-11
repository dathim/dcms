<?php 
// dcms/modules/maket/maket.php
Class maket {
function constructor()
	{
		$out = '';
		if ($maketlist = $this->db->select("designes", "id>0 ORDER BY id ASC"))
		{
			
			foreach($maketlist as $ml)
			{
				if ((isset($_GET['id'])) && ($ml->id == $_GET['id'])) {
				$out .= '<li class="active"><a class="_name" href="'.$this->url.'dcms/maket/edit?id='.$ml->id.'">'.$ml->name.'</a></li>';
				}
				else {
				$out .= '<li><a class="_name" href="'.$this->url.'dcms/maket/edit?id='.$ml->id.'">'.$ml->name.'</a></li>';
				}
			}
			
		}
		echo '
					<div class="tree ucol"><ul>'.$out.'</ul>
					<span><a href="'.$this->url.'dcms/maket/c_add_new" class="_new"><b></b>Добавить макет</a></span>
					</div>
					
					<div class="content ucol">
						<div class="block">
			';
	}

	function destructor() 
	{
		echo '
					</div>
				</div>
			</div>';
	}
	
	function index(){
		echo '<h2>Макеты</h2>
		<p><b>Внимание!</b> Данный раздел предназначен для опытных пользователей<p>
		';
	}
	
	function edit()
	{
		$page = $this->db->select("designes","id='".$_GET['id']."'");
		
		
		
		echo '<form class="rcform" action="'. $this->url .'dcms/maket/c_rename?id='.$_GET['id'].'" method="POST">';
		echo "<a href='javascript:void(0)' rel='tooltip' data-original-title='Сохранить' data-placement='left' class='_mastername'></a>
			<h2 class='_wide'><input onBlur='javascript:save_ajax(this)' type='text' value='{$page[0]->name}' name='name' placeholder='Название' /></h2>";
		echo '</form>';	
			
			
		echo '<div class="tools">';
			echo '<a href="'. $this->url .'dcms/maket/c_add?id='.$_GET['id'].'">Добавить элемент дизайна</a>';
			echo '<a href="'. $this->url .'dcms/maket/c_copy?id='.$_GET['id'].'">Копировать</a>';
			if ($use_des = $this->db->select("pages","design='".$_GET['id']."'",'id'))
			{
				echo '<a href="javascript:go(\''. $this->url .'dcms/maket/c_dell?id='.$_GET['id'].'\')">Удалить (используется '.count($use_des).' раз)</a>';
			}
			else
			{
				echo '<a href="javascript:go(\''. $this->url .'dcms/maket/c_dell?id='.$_GET['id'].'\')">Удалить</a>';
			}
			echo '<a href="javascript:window.open(\''. $this->url .'dcms/maket/c_wizard?id='.$_GET['id'].'\',\'name_'.$page[0]->name.'\',\'width=900,height=500\'); void(0)">Wizard</a>';
		echo '</div>';
		// список компонентов 
		if ($item = $this->db->select("designes_items","parent='".$_GET['id']."' ORDER BY sort ASC")){
			echo "	<div class='table-header'>
		<div class='col w150px'>
			Название
		</div>
		<div class='col w50px'>
			Общ.
		</div>
		<!--<div class='col w150px'>
			Редактор
		</div>-->
		<div class='col w100px'>
			Замена
		</div>
		<div class='col w100px'>
			Компонент
		</div>
		
		
		<div class='col w50px'>
			Порядок
		</div>
	</div>";
			echo "<div class='table' id='sortable' class='table ui-sortable'>";
			foreach($item as $p)
			{
				
				if ($p->for_all == 0 ) echo '<div class="row fon_red" id="'.$p->id.'">'; 
				else echo '<div class="row fon_gren" id="'.$p->id.'">'; 
					echo '<form class="rcform" action="'. $this->url .'dcms/maket/c_save_item?cid='.$_GET['id'].'&iid='.$p->id.'" method="POST">';
					
					echo '<div class="col w150px">';					
						echo '<input type="text" name="name" onBlur="javascript:save_ajax(this)" value="'.$p->name.'"/>';
					echo '</div>';
					
					echo '<div class="col w50px">';
						if ($p->for_all == 0 )	 echo '<input type="checkbox" name="for_all" onClick="javascript:save_ajax(this)" value="1"/>'; else echo '<input type="checkbox" name="for_all" value="1"   checked />';
					echo '</div>';
					
					
					echo '<div class="col w100px">';
						echo '<select name="ful_copy_id" onBlur="javascript:save_ajax(this)">
						<option name="ful_copy_id" value="0" >Нет</option>';
							if ($all_des = $this->db->select("designes","", "id, name"))
							foreach($all_des as $j)
							{
								if ($sd = $this->db->select("designes_items","parent='".$j->id."' ORDER BY sort ASC", "id, name"))
								echo '<option value="0" disabled >+ '.$j->name.'</option>';
								foreach($sd as $i)
								{
									if ($p->ful_copy_id	== $i->id)	echo '<option value="'.$i->id.'" selected>&nbsp;&nbsp;'.$i->name.'</option>';
										else	echo '<option value="'.$i->id.'" >&nbsp;&nbsp;'.$i->name.'</option>';
								}
								
							}
						echo '</select>';
					echo '</div>';
					
					echo '<div class="col w100px">';
						echo	'<select name="component" onBlur="javascript:save_ajax(this)">				
								<option value="0" >Нет</option>';
						if ($com = $this->db->select("coms","", "id, name"))
						foreach($com as $i)
						{
							if ($p->komp	== $i->id) echo '<option value="'.$i->id.'" selected>'.$i->name.'</option>';	
									else echo '<option value="'.$i->id.'" >'.$i->name.'</option>';
						}
						echo '</select>';
					echo '</div>';
					
				
					
					echo '<div class="col w50px">';					
						echo '<input type="text" name="sort" onBlur="javascript:save_ajax(this)" value="'.$p->sort.'"/>';
					echo '</div>';
					
					
					echo '<div class="col w100px">';
					echo '<input style=" margin: 0;" type="submit" name="" value="Сохранить">';
					echo '</div>';				
					echo '<a class="delete" href="javascript:go(\''. $this->url .'dcms/maket/c_del_item?id='.$p->id.'&des='.$_GET['id'].'\')"></a>
							<a class="edit" href="javascript:window.open(\''. $this->url .'dcms/maket/c_edit?id='.$p->id.'\',\'name_'.$p->id.'\',\'width=900,height=500\'); void(0)" ></a>';
				echo '</form>';
				echo '</div>'; 
			}
			echo '</div>
			
			
			<script type="text/javascript">
			$(function() {

				$( "#sortable" ).sortable({

					placeholder: "ui-state-highlight",
						stop: function(event, ui) {
							var result = $("#sortable").sortable("toArray");
							console.log(result);
							$.post("'.$this->url.'dcms/maket/c_resort?id='. $_GET['id'].'",{arr:result},function(data){ // с двумя параметрами и вызовом функции
						
							if (data != 1) 
							{	
								alert("Порядок сортировки не сохранен");  
								
							}
							else
							{
								nsave();
								window.location.replace(window.location);
							}
						});
					}

						});
					});
			</script>
		';
			
		}
	}



}

Class clear {  //чистый класс

	function c_wizard(){	
		$page = $this->db->select("designes","id='".$_GET['id']."'");	
		if ($items = $this->db->select("designes_items","parent='".$_GET['id']."' ORDER BY sort ASC")){
			foreach($items as $i){
				if ($i->ful_copy_id != 0) {
					if ($item_replace = $this->db->select("designes_items","id=".$i->ful_copy_id)){
						$true_items[] = $item_replace[0];
						$i= $item_replace[0];
					} else {
						$true_items[] = $i;
					}
				} else {
					$true_items[] = $i;
				}
			}
			
			$outpoot_text='';
			foreach($true_items as $i){
				if ($i->name == '') $i->name="Без названия";
				if ($i->sort == '') $i->sort=0;
				if ($i->for_all == '') $i->for_all=0;
				$outpoot_text .= "\r\n{item \"name\":{$i->name},\"id\":{$i->id},\"sort\":{$i->sort},\"global\":{$i->for_all}}\r\n";
				$outpoot_text .= $i->text;
			}
			$outpoot_text = trim($outpoot_text,"\n\r\0");
			echo '
			<html>
				<head>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.form.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery-ui-1.8.14.custom.min.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/minibox.js"></script>
					<link rel="stylesheet" type="text/css" href="'.$this->url.'dcms/style/editors.css" />
	

					<script>
							 
							function is_ready_data(){
								if (typeof ready_data == "function") { 
									if (ready_data()) $(".editform").submit();
								}
							}
						</script>	
					 <style type="text/css">
						.CodeMirror-scroll{
						display:block;
						width: 100%;
						height:95%;
						background-color: #fff;
						}
					 </style>
				</head>
				<body>
					<form class="editform myForm"  action="'. $this->url .'dcms/maket/c_wizard_save?id='.$_GET['id'].'" method="POST">
						<div class="editors_head">
							<h2 class="title">'.$page[0]->name .'<a href="javascript:window.open(\'http://dathim.ru/help_v4\',\'width=900,height=500\'); void(0)">Справка</a></h2><b class="save_alert">Сохранено</b>
							<a href="javascript:insert_ace_text(\'<?=$url?>\')">url </a> 
							<a href="javascript:insert_ace_text(\'<?=$title?>\')">title </a>
							<a href="javascript:insert_ace_text(\'<?=$name?>\')">name</a>
							<div class="notifications"><span style="display: none !important;"></span></div>
							<input style=" margin: 0;" class="right_submit" type="submit" name=""  onClick="is_ready_data();" value="Сохранить">
						</div>
						<div style=" padding-top: 33px; ">';
			
			
			echo '	<textarea id="ace_code"   name="wizard_text"  style="display:none;"  >'.htmlspecialchars($outpoot_text).'</textarea>
							<div class="ace_item" id="ace_page_code" style="   margin-top: 3px;">'.htmlspecialchars($outpoot_text).'</div> 
								
							<script src="'. $this->url.'dcms/plugins/ace/ace.js" type="text/javascript" charset="utf-8"></script>
							<script>
							 var ace_page_code = ace.edit("ace_page_code");
							 ace_page_code.setTheme("ace/theme/eclipse");
							 ace_page_code.getSession().setMode("ace/mode/php");
							 //ace_page_code.setOption("maxLines", 90);
							function ready_data(){
								$("#ace_code").html(escapeHtml(ace_page_code.getSession().getValue()));
								return true;
							}
							
							
						</script>	
						';
			echo '<style type="text/css" media="screen">
							 .ace_item{ 
								width: 100%;
								
							 }
						</style>
						</div>
					</form>
				</body>
				</html>';
		}
	}

	function c_wizard_save() 
	{	
		function json2array($json){
			if(get_magic_quotes_gpc()){
				$json = stripslashes($json);
			}
			$json = substr($json, 1, -1);
			$json = str_replace(array(":", "{", "[", "}", "]"), array("=>", "array(", "array(", ")", ")"), $json);
			@eval("\$json_array = array({$json});");
			return $json_array;

		}
		$all_text = $_POST['wizard_text'];
		$items = explode('{item ', $all_text);
		if (strlen($items[0])<10) unset($items[0]); 
		//print_r($items);
		foreach($items as $i){
			$json = "'{".substr($i, 0, strpos($i, '}')+1) . ";"; // только до }
			$code = substr($i, strpos($i, '}')+1); // от{
			$code = trim($code,"\n\r\0");
			$json = str_replace(" ", "_", $json);
			//echo  $json;
			$arr = json2array($json);
			//echo "<h1>".$arr[0]['name']."</h1>"; 
			// print_r($arr);
			//echo $code;
			//UPDATE
			if ($item = $this->db->select("designes_items","id='".$arr[0]['id']."'")){
				$item[0]->name =  $arr[0]['name'];
				$item[0]->sort =  $arr[0]['sort'];
				$item[0]->for_all =  $arr[0]['global'];
				$item[0]->text =  $code;
				$this->db->update("designes_items","id=".$arr[0]['id'], $item[0]);
			}	
		}
		echo "ok";
	}
	
	function c_copy() //копирование макета 
	{
		if ($des = $this->db->select("designes","id='".$_GET['id']."'"))
		{
			$new->name = 'Копия '.$des[0]->name;
			$this->db->insert("designes",$new);
			if ($new_id = $this->db->select("designes","id>0  ORDER BY id DESC LIMIT 1 ","id"))
			{
				$max_id = $new_id[0]->id;
			
				if ($ides = $this->db->select("designes_items","parent='".$_GET['id']."'"))
				{
					foreach($ides as $i)
					{
						$i->id = '';
						$i->parent = $max_id;
						$i->text = $i->text;
						
						$i->sort =$i->sort;
						$this->db->insert("designes_items",$i);
					}
					header('Location: '.$this->url.'dcms/maket');	
				}
				else header('Location: '.$this->url.'dcms/maket');	
			}
		}	
	}

	function c_dell() 	//удаление макета дизайна
	{
		if ($this->db->delete('designes', 'id="'.$_GET['id'].'"'))
		{
			if ($this->db->delete('designes_items', 'parent="'.$_GET['id'].'"','999'))
			{
				header('Location: '.$this->url.'dcms/maket');		
			}
			else echo 'Ошибка при: удаление элементов шаблона дизайна';
		}
		else echo 'Ошибка при: удаление шаблона дизайна';
	}

	function c_add_new() // создание нового макета
	{		
		$add->name = "Новый макет";	
		if ($this->db->insert("designes",$add)) 
		{
			header('Location: '.$this->url.'dcms/maket');				
		}	
	}


	function c_rename() // Переиминование макета
	{
		$des->name = $_POST['name'];
		if ($this->db->update("designes","id='".$_GET['id']."'",$des)) 	header('Location: '.$this->url.'dcms/maket/edit?id='.$_GET['id']);	
	}
	
	function c_save_item() // сохранение элемента макета
	{
		if ($item = $this->db->select("designes_items","id='".$_GET['iid']."'"))
		{
			if (isset($_POST['name'])) $item[0]->name =  $_POST['name'];
			if (isset($_POST['component']))  $item[0]->komp=  $_POST['component'];
			if (isset($_POST['ful_copy_id']))  $item[0]->ful_copy_id =  $_POST['ful_copy_id'];
			if (isset($_POST['for_all']))  $item[0]->for_all  = '1'; else $item[0]->for_all ='0';
			if ($this->db->update("designes_items","id='". $_GET['iid'] ."'",$item[0]))
			{
				header('Location: '.$this->url.'dcms/maket/edit?id='.$_GET['cid']);
			}		
		}
	}
	
	function c_add() // создание элемента макета
	{
		$add->name = "Новый элемент";	//добавление page_id
		$add->parent = $_GET['id'];	//добавление page_id
		if ($this->db->insert('designes_items',$add)) 
		{
			header('Location: '.$this->url.'dcms/maket/edit?id='.$_GET['id']);
		}
		else
		{
			echo "Ошибка создания элемента макета c_add()";
		}
	}
	
	

	function c_edit()
	{
		if ($page = $this->db->select("designes_items","id='".$_GET['id']."'"))
		{   	
			echo '
			<html>
				<head>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.form.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery-ui-1.8.14.custom.min.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/minibox.js"></script>
					<link rel="stylesheet" type="text/css" href="'.$this->url.'dcms/style/editors.css" />
	

					<script>
							 
							function is_ready_data(){
								if (typeof ready_data == "function") { 
									if (ready_data()) $(".rcform").submit();
								}
							}
						</script>	
					 <style type="text/css">
						.CodeMirror-scroll{
						display:block;
						width: 100%;
						height:95%;
						background-color: #fff;
						}
					 </style>
				</head>
				<body>
					<form  class="myForm editform"  action="'. $this->url .'dcms/maket/c_save?id='.$_GET['id'].'" method="POST">
						<div class="editors_head">
							<h2 class="title">'.$page[0]->name .'<a href="javascript:window.open(\'http://dathim.ru/help_v4\',\'width=900,height=500\'); void(0)">Справка</a></h2><b class="save_alert">Сохранено</b>
							<div class="notifications"><span style="display: none !important;"></span></div>
							<input style=" margin: 0;" class="right_submit" type="submit" name=""  onClick="is_ready_data();" value="Сохранить">
						</div>
						<div style=" padding-top: 33px; ">
						
							
							
							<textarea id="ace_code"   name="text"  style="display:none;"  >'.htmlspecialchars($page[0]->text).'</textarea>
							<div class="ace_item" id="ace_page_code" style="   margin-top: 3px;">'.htmlspecialchars($page[0]->text).'</div> 
								
							<script src="'. $this->url.'dcms/plugins/ace/ace.js" type="text/javascript" charset="utf-8"></script>
							<script>
							 var ace_page_code = ace.edit("ace_page_code");
							 ace_page_code.setTheme("ace/theme/eclipse");
							 ace_page_code.getSession().setMode("ace/mode/php");
							 //ace_page_code.setOption("maxLines", 90);
							function ready_data(){
								$("#ace_code").html(escapeHtml(ace_page_code.getSession().getValue()));
								return true;
							}
							
						
						</script>	
						
						<style type="text/css" media="screen">
							 .ace_item{ 
								width: 100%;
								
							 }
						</style>
						</div>
					</form>
				</body>
				</html>';
		}
		
	}
	
	function c_func()
	{
		 
		include 'help_func.php';
		
			echo '
				<html>
					<head>
						<script type="text/javascript" src="'.$this->url.'dcms/js/jquery-1.5.1.min.js"></script>
						<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.form.js"></script>
						<script type="text/javascript" src="'.$this->url.'dcms/js/jquery-ui-1.8.14.custom.min.js"></script>
						<script type="text/javascript" src="'.$this->url.'dcms/js/minibox.js"></script>
						<link rel="stylesheet" type="text/css" href="'.$this->url.'dcms/style/mini.css" />
						<link rel="stylesheet" href="'. $this->url .'dcms/cm/lib/codemirror.css">
						 <script src="'. $this->url .'dcms/cm/lib/codemirror.js"></script>
						 <script src="'. $this->url .'dcms/cm/lib/util/overlay.js"></script>
						 <link rel="stylesheet" href="'. $this->url .'dcms/cm/theme/default.css">
						 <script src="'. $this->url .'dcms/cm/mode/xml/xml.js"></script>


						 <style type="text/css">
							.CodeMirror-scroll{
							display:block;
							width: 100%;
							height:95%;
							background-color: #fff;
							}
						 </style>
					</head>
					<body>
							<textarea id="code"  style="width:100%; height:95%;" name="text"  onkeydown="insertTab(this, event);">'.$data_func.'</textarea><br/>
							 <script>
							CodeMirror.defineMode("mustache", function(config, parserConfig) {
							  var mustacheOverlay = {
								 token: function(stream, state) {
									if (stream.match("{{")) {
									  while ((ch = stream.next()) != null)
										 if (ch == "}" && stream.next() == "}") break;
									  return "mustache";
									}
									while (stream.next() != null && !stream.match("{{", false)) {}
									return null;
								 }
							  };
							  return CodeMirror.overlayParser(CodeMirror.getMode(config, parserConfig.backdrop || "text/html"), mustacheOverlay);
							});
							var editor = CodeMirror.fromTextArea(document.getElementById("code"), {mode: "mustache"});
							</script>

					</body>
					</html>';
		
	}
	
	function c_save()
	{
		$page = $this->db->select("designes_items","id='".$_GET['id']."'");
		$page[0]->text = $_POST['text'];
		if ($this->db->update("designes_items","id='".$_GET['id']."'",$page[0])){
			echo "ok";
		} else {
			echo "Ошибка сохоанения текста c_save()";
		}
	}
	
	function c_resort()
	{
		$count=0;
		foreach($_POST['arr'] as $a)
		{
			$count+=1000;
			$i->sort = ++$count;
			$this->db->update("designes_items","id='". $a ."'",$i);
		}
		echo 1;
	}	
	
	function c_del_item() //удаление элемента
	{	
		if ($this->db->delete('designes_items', 'id="'.$_GET['id'].'"'))
		{
			header('Location: '.$this->url.'dcms/maket/edit?id='.$_GET['des']);			
		}
		else echo 'Ошибка при: удаление элементоа макета c_del_item()';
	}
	
}