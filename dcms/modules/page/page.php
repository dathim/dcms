<?php 
// dcms/modules/page/page.php

Class page {
	function constructor() //Самописный конструктор
	{
		
	
	
		function ierarh_option($p,$par,$curent,$step){
			$t='';
			foreach($p as $i)	{
				if ($i->parent != $par) continue;
				if ($i->id == $curent) { 	$t .= '<option value='.$i->id.' selected >+'.$step.$i->name.'</option>';		}
				else { $t .= '<option value='.$i->id.'>&nbsp;'.$step.$i->name.'</option>';	} 
				$t .= ierarh_option($p,$i->id, $curent,$step.'&nbsp;&nbsp;&nbsp;&nbsp;');
			}
			return $t;
		}
		$tree = $this->db->select("pages", "sost = '0' ORDER BY sort ASC");
		if (!isset($_GET['table'])){
			echo '<div class="settings ucol">';
			if (isset($_GET['page'])) 
			{
				if ($page = $this->db->select("pages", "id = '".$_GET['page']."'"))
				{
					
					$all_page = '';
					if ($all_pager = $this->db->select("pages","id>0 ORDER BY sort ASC","id, name, path, parent"))
					{
						$all_page = ierarh_option($all_pager,0,$page[0]->parent,"");
						/*$coun_pg = count($item); 
						foreach($item as $i)
						{
							if ($i->id == $page[0]->parent) { 
								$all_page .= '<option value='.$i->id.' selected >'.$i->name.' [/'.$i->path.']</option>';
							}
							else { 
								$all_page .= '<option value='.$i->id.'>'.$i->name.' [/'.$i->path.']</option>';
							} 
						}*/
					}
					
					if (!isset($page[0]->sub_design)){
						//установка подмакетов
						$sql_q_ins = "ALTER TABLE  `pages` ADD  `sub_design` INT NOT NULL;";
						db::$mysqli->query($sql_q_ins);	
						$page = $this->db->select("pages", "id = '".$_GET['page']."'");	
					}
					if ($ms = $this->db->select("designes"))
					{
						$makets='';
						$sub_makets='';
						if ($page[0]->sub_design == 0) $sub_makets= '<option value="0">Наследовать</option>';
						foreach($ms as $m)
						{
							if ($m->id == $page[0]->design) { 
								$makets .= '<option value='.$m->id.' selected >'.$m->name.'</option>';
							}
							else { 
								$makets .= '<option value='.$m->id.'>'.$m->name.' </option>';
							} 
							
							if ($m->id == $page[0]->sub_design) { 
								$sub_makets .= '<option value='.$m->id.' selected >'.$m->name.'</option>';
							}
							else { 
								$sub_makets .= '<option value='.$m->id.'>'.$m->name.' </option>';
							} 
						}
					}
				
					echo $this->func->page_settings($this, $page[0], $this->url,$all_page,$makets,$sub_makets);
				}
				else
				{
					echo $this->func->page_not_found();
				}
			} else {
			

			
				$username_ap = base64_decode($_SESSION['login']); 
				echo '<div class="block">
						<h2>Информация о сайте</h2>
					</div>
					<div class="block">
						<b>Домен: </b> '.$this->url.'<br />
						<b>База данных: </b> '.$this->database  .'<br />
						<b>Размер сайта:</b> '.
						$this->func->format_size(4545728+$this->func->foldersize('../uploads'))
						.' <br /><b>Разработка: </b> <a target="_blank" href="http://dathim.ru">Студия Dathim</a><br />
						<a href="'.$this->url.'dcms/page/sitemap">Обновить sitemap.xml</a>
					</div>
					<div class="block">
						<h2>Информация о разработчике</h2>
						<b>Архангельск: </b> <br />
						Таран Андрей<br />  +7 902 507-37-48,<br />  dathim@gmail.com <br /><br />
						<b>Астрахань: </b> <br />
						Костров Пётр <br />+7 988 177-87-08,<br />  creatoff@gmail.com<br />
					</div>
					<div class="block">
						<br />Вы вошли под именем <b>'.$username_ap.'</b>,<br /> <a href="users">сменить пароль</a>
					</div>
					
					';	
			}			
			echo '		</div>';
		}
		echo '	<div class="tree ucol"><ul>'. $this->func->recurs($tree,0,$this->url).'</ul></div>
					
					<div class="content ucol">
						<div class="block">';
	}

	function destructor() 
	{
		echo '</div></div></div>';
	}
		
		
	function return_line($items, $l, $path) {
		$text='';
		foreach($items as $i){
			if ($i->parent == $l) {
				if ($path != ''){
					$text .=  "<url>\n<loc>".$this->url.$path.'/'.$i->path."</loc>\n</url>\n";
					$text .= $this->return_line($items,$i->id,$path.'/'.$i->path );	
				} else {
					$text .=  "<url>\n<loc>".$this->url.$path.$i->path."</loc>\n</url>\n";
					$text .= $this->return_line($items,$i->id,$path.$i->path );	
				}
				
			}
			
		}
		return $text;
	}	
		
	function sitemap(){
		echo "<h2>Sitemap.xml</h2>";

		if ($this_page = $this->db->select("pages","off=0 ORDER BY parent ASC")){
			$sitemap_body='<?xml version="1.0" encoding="UTF-8"?>'."\n".
'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			$line = 1;
			
			$sitemap_body .= $this->return_line($this_page, $line, '');
			
			$sitemap_body .= '</urlset> ';
			
		}
		if(file_put_contents("../sitemap.xml", $sitemap_body)){
			echo "<p>Данные обновлены, найдено страниц: ".count($this_page)."</p>";
		} else {
			echo "<p>Возникли проблемы, обратитесь к администратору</p>";
		}
	}
		
	function index(){
		if (isset($_GET['page'])) {
			if ($this_page = $this->db->select("pages","id='".$_GET['page']."'")){
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
				echo '<h2 rel="tooltip" data-original-title="Перейти на страницу" data-placement="bottom"><a target="_blank" href="'.$path_tp.'">'.$this_page[0]->name.'</a></h2>';
				if (isset($_GET['edit'])){
					echo '<div style=" float: right; margin: 23px 0px 0px 0px;"><a href="'. $this->url .'dcms/page?page='.$_GET["page"].'"><img src="'. $this->url .'dcms/style/icons2/logout.png" /></a></div>';
				} else {
					echo '<div style=" float: right; margin: 23px 0px 0px 0px;"><a href="'. $this->url .'dcms/page?page='.$_GET["page"].'&edit=1"><img src="'. $this->url .'dcms/style/icons2/settings.png" /></a></div>';
				}
				
				
			if (!isset($_GET['edit'])){ // обычный режим
					if ($page_item = $this->db->select("pages_items","parent='".$_GET['page']."' AND design='".$this_page[0]->design."' ORDER BY sort ASC"))
					{
						foreach($page_item as $pi)
						{
							if($pi->komp == 0) //одинчка
							{
								echo $this->func->page_edit_text($pi,$this->url);
							}
							else
							{
								echo $this->func->page_edit_com($pi,$this);
							}
						}
					}
					
				}
			else { //режим "править элементы"
						echo '<div class="tools">
								<a href="'. $this->url .'dcms/page?page='.$_GET["page"].'">Страница</a>
								<a href="'. $this->url .'dcms/page/c_add_new_pi?id='.$_GET['page'].'">Добавить</a>
								<a href="javascript:go(\''. $this->url .'dcms/page/c_del_p_i?id='.$_GET['page'].'\')">Удалить всё</a>
							  </div>';
						$page = $this->db->select("pages","id='".$_GET['page']."'");
							  
						echo '<div class="table" id="sortable_pages" class="table ui-sortable">';
						$sql = "SELECT id, name, sort, text, komp , ful_copy_id, `design`, `parent`  FROM `designes_items` WHERE  `for_all`=1 AND `parent`=".$page[0]->design." 
								UNION SELECT  `id`, `name`, `sort`, `text`, `komp` , `ful_copy_id`, `design`, `parent`  FROM `pages_items` WHERE parent='".$_GET['page']."'   ORDER BY sort ASC";
						$res = db::$mysqli->query($sql);
						while ($obj = $res->fetch_object()) {	$data[] = $obj; }	
							if (isset($data)){ 
								foreach($data as $c)
								{
									if ($komps  = $this->db->select("coms","name <>''")){								
										$kom_select='<select name="komp" onchange="javascript:save_ajax(this)">';
										$kom_select.= '<option selected value="0">Редакторы текста</option>';
										foreach($komps as $km)
										{
											if ($c->komp == $km->id) $kom_select.= '<option selected value="'.$km->id.'">Комп. '.$km->name.'</option>';
											else $kom_select.= '<option  value="'.$km->id.'">Комп. '.$km->name.'</option>';
										}
										$kom_select.= '</select>';
									}
									
									if ($mak  = $this->db->select("designes","name <>''")) {	
										$mak_select='<select name="design" onchange="javascript:save_ajax(this)">';
										
										foreach($mak as $ms)
										{
											if ($c->design == $ms->id) $mak_select.= '<option selected value="'.$ms->id.'">'.$ms->name.'</option>';
											else $mak_select.= '<option  value="'.$ms->id.'">'.$ms->name.'</option>';

										}
										$mak_select.= '</select>';
									}
								
									
									if ($c->design == 0 ) 
									{
										echo '<div class="pageitem ui-state-disabled" style=" background: #FFE4CB; ">'; 
										echo '<div class="_head"><h3><a href="#1">'.$c->name .' ( id:'.$c->id .')</a></h3></div>';	
										echo '<div class="_body"><span>Общий элемент. Порядок: '.$c->sort.'</span></div>';
										echo '</div>';
										continue;
									}				
									if ($page[0]->design == $c->design) echo '<div class="pageitem" style=" background: #C9FCBF; " id="'.$c->id.'">'; 
									if (($c->design != 0) && ($c->design != $page[0]->design)) echo '<div class="pageitem" style=" background: #EEE; " id="'.$c->id.'">'; 
									
									echo '<div class="_head"><h3><a href="javascript:window.open(\''. $this->url .'dcms/page/c_edit?editor=cm&id='.$c->id.'\',\'\',\'width=900,height=500\'); void(0)">'.$c->name .' ( id:'.$c->id .')	</a></h3>
											<div class="editors"><a class="_dell" href="'. $this->url .'dcms/page/c_del_p_i1?id='.$_GET['page'].'&piid='.$c->id.'"></a></div>
										  </div>';	
										  
										echo '<div class="_body">';
											echo '<form class="rcform" action="'. $this->url .'dcms/page/c_items_edit?id='.$_GET['page'].'" method="POST">';
												echo '
												<div class="_pif"><label>Имя: </label><input type="text" name="name" value="'.$c->name .'" onchange="javascript:save_ajax(this)"/></div>
													 <input type="hidden" name="pid" value="'.$c->id.'"/>
												<div class="_pif"><label>Порядок: </label><input type="text" name="resort" value="'.$c->sort.'" style=" width: 51px; " onchange="javascript:save_ajax(this)"/></div>
												<div class="_pif"><label>Тип материала: </label>'.$kom_select.'</div>
												<div class="_pif"><label>Макет: </label>'.$mak_select.'</div>';
												
											echo '</form>';
										echo '</div>';
									echo '</div>';
								}
							}
						echo '</div>
						
						
						<script type="text/javascript">
						$(function() 
						{
							$( "#sortable_pages" ).sortable({
							
								
								stop: function(event, ui) {
										var result = $("#sortable_pages").sortable("toArray");
										//console.log(result);
										$.post("'.$this->url.'dcms/page/c_page_iten_resort_script?id='. $_GET['page'].'",{arr:result},function(data){ // с двумя параметрами и вызовом функции
											if (data != 1) 
											{	
												alert("Порядок сортировки не сохранен"+data); 
											}
											else
											{
												//Сохоанено 
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
			
			
			//Расширения параметров страницы
			$x = db::$mysqli->query('SELECT * FROM `com_page_extension`');
			if($x) {
				echo "<h3><a href='javascript:void(0)' class='togle_ext'>Дополнительная информация к странице</a></h3>";
				echo "<div class='blockd hide' >";
				
				if ($ext = $this->db->select("com_page_extension","page_id = {$_GET['page']}")){
					
							//определить список полей
					$ext_com = $this->db->select("coms","table_name = 'com_page_extension'");
					if ($cf = $this->db->select('coms_fields' ,"parent='{$ext_com[0]->id}' AND show_edit = 1 ORDER BY sort ASC")){
				
						$_GET["iid"] = $ext_com[0]->id;
						$parent_var ='';
						if (isset($_GET['parent'])) $parent_var = '&parent='.$_GET['parent'];
						echo '
							<script src="'.$this->url.'/dcms/plugins/fck/ckeditor.js"></script>
						<form action="'.$this->url.'dcms/page/c_edit_propertis?page='.$_GET['page'].'&com_id='.$ext_com[0]->id.'&iid='.$ext[0]->id.'" method="POST" enctype="multipart/form-data">';
						
						$count_edit = 1;
						foreach($cf as $f){
							$enname = $f->enname;
							$count_edit++;
							echo "
								<div class='com_items'>
								<script type='text/javascript'>
								var editor, html = '';
								function start".$count_edit."(){
								var config = {};
								//editor = CKEDITOR.appendTo('". $f->enname .$count_edit."', config, html );
								CKEDITOR.replace('1". $f->enname .$count_edit."', {
								
									filebrowserBrowseUrl: '".$this->url."dcms/files?path=../uploads&CKEditor=editor1&CKEditorFuncNum=3&langCode=ru/',
									filebrowserUploadUrl: '".$this->url."dcms/files/c_upload_fck'

								});
								}</script>";

							
							if ($f->type == 1) {echo '<p>'. $f->name. ':</p><input type="text" name="'. $f->enname. '" value="'.$ext[0]->$enname.'"/><b>Строка 120</b></div>'; continue;} 
							if ($f->type == 2) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$ext[0]->$enname.'"/><b>Строка 500</b></div>'; continue;} 
							if ($f->type == 3) { echo '<p>'. $f->name. '</p>	<a href="javascript: start'.$count_edit.'()">Переключить редактор</a>	<textarea  id="1'. $f->enname. $count_edit.'" rows="5" name="'. $f->enname. '">'.$ext[0]->$enname.'</textarea><b>Текст</b></div>'; continue;}
							if ($f->type == 4) { echo '<p>'. $f->name. '</p>	<a href="javascript: start'.$count_edit.'()">Переключить редактор</a>	<textarea  id="1'. $f->enname. $count_edit.'" rows="5" name="'. $f->enname. '">'.$ext[0]->$enname.'</textarea><b>Текст</b></div>'; continue;} 
							if ($f->type == 5) { // ДАТА	
								echo '<p>'. $f->name. '</p><input type="date" name="'. $f->enname. '" value="'.$ext[0]->$enname.'"/> <span class="input-group-addon"></div>'; 
								
								continue;
							} 
							if ($f->type == 6) { // ДАТА И ВРЕМЯ
								echo '<p>'. $f->name. '</p><input type="datetime-local" name="'. $f->enname. '" value="'.str_replace(" ","T", $ext[0]->$enname).'"/><b> DATETIME - 100001-01 00:00:00</b></div>'; 
								continue;
							} 
							if ($f->type == 7) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$ext[0]->$enname.'"/><b>TINYINT - -128 до 127</b></div>'; continue;} 
							if ($f->type == 8) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$ext[0]->$enname.'"/><b>Целое число</b></div>'; continue;} 
							if ($f->type == 9) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$ext[0]->$enname.'"/><b>Любое число</b></div>'; continue;} 
							if ($f->type == 10) {
								echo '<p>'. $f->name. '</p><input type="file" name="'. $f->enname. '" value=""/>'.$ext[0]->$enname.' <b>Картинка max 4mb</b>';
								if ($ext[0]->$enname != '') echo '<img class="comimgshow" src="'.$this->url.'uploads/com_page_extension/1_'.$ext[0]->$enname.'" />
								<a href="'.$this->url.'dcms/page/c_del_ext_file?page='.$_GET['page'].'&com_ext_id='.$ext[0]->id.'&field='.$enname.'">Удалить</a>
								';
								
								echo '</div>';
								continue;} 
							if ($f->type == 11) {
								echo '<p>'. $f->name. '</p><input type="file" name="'. $f->enname. '" value=""/>'.$ext[0]->$enname.' <b>Файл</b>';
								if ($ext[0]->$enname != '') echo '<a href="'.$this->url.'uploads/com_page_extension/'.$ext[0]->$enname.'" >Скачать ('.$ext[0]->$enname.')</a>
								<a href="'.$this->url.'dcms/page/c_del_ext_file?page='.$_GET['page'].'&com_ext_id='.$ext[0]->id.'&field='.$enname.'">Удалить</a>';
								echo '</div>'; continue;
								}
							if ($f->type == 12) {
								if ($ext[0]->$enname == 1) {
										echo '<p>'. $f->name. '</p><input type="checkbox" style=" width:20px;"  name="'. $f->enname. '" value="1" checked/><b>Off/On (Галочка)</b></div>';
									} else {
										echo '<p>'. $f->name. '</p><input type="checkbox" style=" width:20px;"  name="'. $f->enname. '" value="1"/><b>Off/On (Галочка)</b></div>';
									}
								continue;
							}
							
							if ($f->type == 13) { 
								$select_sajest = '<option value="">НЕТ</option>';
								if ($sujest = $this->db->select($f->param ,"name <> ''"))
								{
									foreach($sujest as $sj){
										if ($ext[0]->$enname ==  $sj->id) $select_sajest .= '<option selected value="'.$sj->id.'">'.$sj->name.'</option>';
										else $select_sajest .= '<option value="'.$sj->id.'">'.$sj->name.'</option>';
									}
								}
								echo '<p>'. $f->name. '</p><select  name="'. $f->enname. '" value="1"/>'.$select_sajest.'</select><b>Из списка</b></div>'; 
								continue;
							} 
							/*
							if ($f->type == 14) {	
								if ($com_use = $this->db->select('coms',"id='".$ext[0]->$enname."'")){
									echo 	'<div class="tools">
										<a href="'.$this->url.'dcms/page/show_com?page='.$_GET['page'].'&table='.$com_use[0]->table_name.'&pi='.$_GET['pi'].'&com_id='.$com_use[0]->id.'&parent='.$_GET['iid'].'">К подкомпоненту ('.$com_use[0]->name.')</a>
										</div>';
								} else echo '<p>Подкомпонент нельзя использовать для этого элемента <b>Создайте новый</b></p>';
								continue;
							}
							*/			
							echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$ext[0]->$enname.'"/></p></div>';
						
						} 
						echo '<input type="submit"   name="act"  value="Сохранить"/>
						<a href="javascript:go(\''. $this->url .'dcms/page/c_del_ext?com_ext_id='.$ext[0]->id.'&page='.$_GET['page'].'\')" style=" display: block; margin: 5px 0 0 0px; ">Удалить расширения</a>';
						echo '</form>';
					}
					
				} else {
					//echo "<h3>Расширения доступны:</h3>";		
					if ($ext_com = $this->db->select("coms","table_name = 'com_page_extension'")){
						if ($ext_com_fields = $this->db->select("coms_fields","parent = {$ext_com[0]->id} AND show_edit = 1 ORDER BY sort ASC")){
							$count_edit = 0;
							echo '<script src="'.$this->url.'/dcms/plugins/fck/ckeditor.js"></script>
							<form action="'.$this->url.'dcms/page/c_edit_propertis?page='.$_GET['page'].'&com_id='.$ext_com[0]->id.'" method="POST" enctype="multipart/form-data">';
			
							foreach($ext_com_fields as $f){
									$count_edit++;
								echo "
										<div class='com_items'>
										<script type='text/javascript'>
										var editor, html = '';
										function start".$count_edit."(){
										var config = {};
										//editor = CKEDITOR.appendTo('". $f->enname .$count_edit."', config, html );
										CKEDITOR.replace('1". $f->enname .$count_edit."', {
								
									filebrowserBrowseUrl: '".$this->url."dcms/files?path=../uploads&CKEditor=editor1&CKEditorFuncNum=3&langCode=ru/',
									filebrowserUploadUrl: '".$this->url."dcms/files/c_upload_fck'

								});
										}</script>";
								if (($f->enname == "parent") && (isset($_GET['parent']))) 	continue;	
								if ($f->type == 1) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>Строка 120</b></div>'; continue;} 
								if ($f->type == 2) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>Строка 500</b></div>'; continue;} 
								if ($f->type == 3) { echo '<p>'. $f->name. '</p>	<a href="javascript: start'.$count_edit.'()">Переключить редактор</a>	<textarea id="1'. $f->enname. $count_edit.'" rows="5" name="'. $f->enname. '"></textarea><b>Текст</b></p></div>'; continue;}
								if ($f->type == 4) { echo '<p>'. $f->name. '</p>	<a href="javascript: start'.$count_edit.'()">Переключить редактор</a>	<textarea id="1'. $f->enname. $count_edit.'"  rows="5" name="'. $f->enname. '"></textarea><b>Текст</b></p></div>'; continue;} 
								if ($f->type == 5) { // ДАТА	
									echo '<p>'. $f->name. '</p><input  type="date" name="'. $f->enname. '" value="'.$f->$enname.'"/> <b> DATE - 100001-01 </b></div>'; 	continue;
								} 
								if ($f->type == 6) {echo '<p>'. $f->name. '</p><input type="datetime-local" name="'.$f->$enname. '" value=""/><b> DATETIME - 1000-01-01 00:00:00</b></div>'; 	continue;} 
								if ($f->type == 7) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>TINYINT - -128 до 127</b></div>'; continue;} 
								if ($f->type == 8) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>Целое число</b></div>'; continue;} 
								if ($f->type == 9) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>Любое число</b></div>'; continue;} 
								if ($f->type == 10) {echo '<p>'. $f->name. '</p><input type="file" name="'. $f->enname. '" value=""/><b>Картинка</b></div>'; continue;} 
								if ($f->type == 11) {echo '<p>'. $f->name. '</p><input type="file" name="'. $f->enname. '" value=""/><b>Файл</b></div>'; continue;} 
								//new
								if ($f->type == 12) { echo '<p>'. $f->name. '</p><input type="checkbox" style=" width:20px;"  name="'. $f->enname. '" value="1"/><b>Да </b></div>'; continue;} 
								if ($f->type == 13) { 
									$select_sajest = '<option value="">НЕТ</option>';
									if ($sujest = $this->db->select($f->param ,"name <> ''"))
									{
										foreach($sujest as $sj){
											$select_sajest .= '<option value="'.$sj->id.'">'.$sj->name.'</option>';
										}
									}
									echo '<p>'. $f->name. '</p><select  name="'. $f->enname. '" value="1"/>'.$select_sajest.'</select><b>Из списка</b></div>'; 
									continue;
								} 
								/*
								if ($f->type == 14) {
									$com_use = $this->db->select('coms',"id='{$f->param}'");
									echo '<p>'. $f->name. ': '.$com_use[0]->name.'</p><input type="hidden" name="'. $f->enname. '" value="'.$f->param.'"/><b>Доступно при добавленом элементе</b></div>'; 
								continue;	}				 
								*/
								echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/></p></div>';
							
							}
							echo '<input type="submit"  value="Сохранить"/></form>';
							
						} 
					
					} else echo "<p>Информация повреждена!</p>";
				}
				echo "</div>";
			} 
			//SEO
				
				// Получить контент старницы в переменную
				// Заголовки 
				// Проверить вхождения заголовков в текст
				// img содержать alt
				
			// END SEO
		} else 	{
			// Главная
			echo "<h2>DCMS – Система управления сайтом </h2><br />";
		
			
			
			

			
			//Чистка логов
			if ($last_visit = $this->db->select("admin_logs" ,"","*", "ORDER BY RAND() LIMIT 25" )){
				foreach($last_visit as $ll){
					$date_10h = date("Y-m-d G:i:s",strtotime($ll->action_object) - 10*60*60);
					$curent_del = $this->db->delete("admin_logs" ,"user='{$ll->user}' AND action_object='{$ll->action_object}' AND date < '{$ll->date}' AND date > '{$date_10h}' ");
				}
				//print_r($dys);
			}
			$curent_del = $this->db->delete("admin_logs" ,"user IS NULL OR user=''");
	
			$date_6m = date("Y-m-d G:i:s",time() - 24*60*60*30*6);
			//$curent_del = $this->db->delete("admin_logs" ,"date < '{$date_6m}'");
			//конец чистки логов
			
			$usres_log =  array();
			if ($last200= $this->db->select("admin_logs" ,"","*", "ORDER BY date DESC LIMIT 500" )){
				//  1 разбить логи на пользователей  
				foreach($last200 as $log){
					$usres_log[$log->user][] = $log;
				}
				//  2 Просмотрель позозователей на интервалы и если более 5 часов то Переписывать в визиты
				$total_log = array();	
				foreach($usres_log as $ul){
					$last_date = date("Y-m-d G:i:s",time()+60*60*6);
					foreach($ul as $ul1){
						if (strtotime($last_date) > strtotime($ul1->date)+60*60*5){
							$total_log[] = $ul1;
						}
						$last_date = $ul1->date;
					}
				}
				
				//  3 Визиты сортировать по дате и вывести
				function mySort($f1,$f2){
					if($f1->date > $f2->date) return -1;
					elseif($f1->date < $f2->date) return 1;
					else return 0;
				}
				uasort($total_log,"mySort");
				//print_r($total_log);
				$total_log = array_slice($total_log, 0, 15);
				 $nmonth = array(
					  1 => 'января',
					  2 => 'февраля',
					  3 => 'марта',
					  4 => 'апреля',
					  5 => 'мая',
					  6 => 'июня',
					  7 => 'июля',
					  8 => 'августа',
					  9 => 'сентября',
					  10 => 'октября',
					  11 => 'нояюря',
					  12 => 'декабря'
					 );
				if (!isset($_GET['day'])){	 
				echo "<h3> Последние посещения:	</h3>";
				echo "<table>";
					foreach($total_log as $tl){
						
						$date = date("d.m.Y",strtotime($tl->date)); //$date_str->Format('d.m.Y');
						$date_month = date("d.m",strtotime($tl->date)); //$date_str->Format('d.m');
						$date_year = date("Y",strtotime($tl->date)); //$date_str->Format('Y');
						$date_time = date("H:i",strtotime($tl->date)); //$date_str->Format('H:i');
						$ndate = date('d.m.Y');
						$ndate_time = date('H:i');
						$ndate_time_m = date('i');
						$ndate_exp = explode('.', $date);
					
						foreach ($nmonth as $key => $value) {
						  if($key == intval($ndate_exp[1])) $nmonth_name = $value;
						}

						if ($date == date('d.m.Y')){
						$datetime = 'Cегодня в ' .$date_time;
						}

						else if ($date == date('d.m.Y', strtotime('-1 day'))){
						$datetime = 'Вчера в ' .$date_time;
						}

						else if ($date != date('d.m.Y')) {
						$datetime = $ndate_exp[0].' '.$nmonth_name.' '.$ndate_exp[2];
						}
						$date_am = date("Y-m-d",strtotime($tl->date));
						
						
						echo "
						<tr>
							<td><a href='?day=$date_am'>$datetime </a></td>
							<td><b>{$tl->user}</b> {$tl->ip} </td>
							
						</tr>";
					}
					echo "</table>";
				}
			}
			
			//print_r($usres_log);
			
			
			
			
			
			
			if (isset($_GET['log']))	$qmin = ($_GET['log'] * $show)-$show;
				
			
			if (isset($_GET['day'])){
				$date_log_s = $_GET['day'] . " 00:00:00";
				$date_log_e = $_GET['day'] . " 24:59:59";
				
				$nodate ="date > '$date_log_s' AND date < '$date_log_e' ORDER BY date DESC ";
				echo "<h3>События за ".date('d-m-Y',strtotime($_GET['day'] )) .";</h3>";
			} else {
				$nodate = "id>0 ORDER BY date DESC  LIMIT  0 ";
			}
			if ($adnlogs = $this->db->select('admin_logs' ,$nodate)){
				echo "<table class='com_table'>";
				foreach($adnlogs as $log){
					$log_adte = date('d-m-Y H:i',strtotime($log->date));
					echo "<tr>";
					$action = '';
					$at = str_replace(">", " &rarr; ", $log->action_type );
					if ($log->action_object != '') $action = " {$log->action_object}";
					echo "<td><b>{$log->user} </b><br /> {$log_adte}  </td>";
					echo "<td style='  text-overflow: ellipsis; '>{$at}<br />{$action}</td>";
					
					echo "</tr>";					
				}
				echo "</table>";
				
			}
			$count_log = $this->db->select('admin_logs','','COUNT(id)');
			$cval = 'COUNT(id)';
			echo "
			<p>Всего записей: {$count_log[0]->$cval}, <a href='{$this->url}dcms/page/c_claer_log'>Удалить все</a></p>
			<form>
				<p><input id='day_log'  type='date'  value='".date('Y-m-d')."' name='day'/><input type='submit' value='Показать за день' /></p>
				</form>";
			echo "<br/>".$this->func->check_files($this);
		}
	}
	
	function show_com(){ //  К Список позиций компонента
		//echo "<style type='text/css'> div.settings{display:none;}</style>";
		$sort = $this->db->select('coms' ,"id='{$_GET['com_id'] }'");
		echo $this->func->com_header2($this,$sort);//header
		
		$cache1 = '';
		$cache2 = '';
		
		$add_query ='';
		$add_link_parent ='';
		
		if (isset($_GET['parent'])) { $add_query = ' AND parent='.$_GET['parent'] .' '; $add_link_parent = '&parent='.$_GET['parent'] ;}
		/*пагинация*/
				$pagination='';
				$addp_sql='';
				$page_size=100;
				$sqlcount = db::$mysqli->query("SELECT id FROM ".$_GET['table']." WHERE page_item=".$_GET['pi'] . $add_query); 
				$total = $sqlcount->num_rows;
				if ($total > $page_size ){ //Пагинация доступна под $pagination;
					$pagination = '<ul class="pagination">';
					$pahe_count = ceil($total/$page_size); 
					$page_num = 0;
					$p_min = 0;
					$p_count = $page_size;
					if ((isset($_GET['pag'])) && ($_GET['pag'] <> 1)) {
						$p_min = $_GET['pag']*$page_size-$page_size;
						$p_count = $page_size;
					}
					//echo 'min'.$p_min;
					//echo 'max'.$p_count;
					$addp_sql = 'LIMIT '.$p_min.', '.$p_count;
					//echo $addp_sql;
					$order_parameters ='';
					if ((isset($_GET['order_by'])) && (isset($_GET['sort_type']))) {
						$order_parameters = "&order_by=".$_GET['order_by']."&sort_type=".$_GET['sort_type'];
					}
					for($pgi=0; $pgi<$pahe_count; $pgi++){
						$page_num++;
						if ((isset($_GET['pag'])) && ($_GET['pag'] == $page_num)) {
							$pagination  .= ' <li class="active"><a href="?page='.$_GET['page'].'&table='.$_GET['table'].'&pi='.$_GET['pi'].'&com_id='.$_GET['com_id'].$order_parameters.'&pag='.$page_num.$add_link_parent.'">'.$page_num.'</a></li>';
						}
						else
						{
							if ((!isset($_GET['pag'])) && (1 == $page_num)) {
								$pagination  .= ' <li class="active" ><a   href="?page='.$_GET['page'].'&table='.$_GET['table'].'&pi='.$_GET['pi'].'&com_id='.$_GET['com_id'].$order_parameters.'&pag='.$page_num.$add_link_parent.'">'.$page_num.'</a></li>';
								} else {
									$pagination  .= ' <li><a   href="?page='.$_GET['page'].'&table='.$_GET['table'].'&pi='.$_GET['pi'].'&com_id='.$_GET['com_id'].$order_parameters.'&pag='.$page_num.$add_link_parent.'">'.$page_num.'</a></li>';
								}
						}
					}
					$pagination .= '</ul>';
				}
								
		/*end пагинация*/
		
		if ((isset($_GET['order_by'])) && (isset($_GET['sort_type']))) {
			$sort[0]->query = 'ORDER BY '.$_GET['order_by'].' '.$_GET['sort_type'];
		}
		if ($datat = $this->db->select($_GET['table'] ,"page_item='{$_GET['pi']}' {$add_query} ".$sort[0]->query.' '.$addp_sql)){
			if ($cf = $this->db->select('coms_fields' ,"parent='{$_GET['com_id'] }' AND show_table = 1 ORDER BY sort")){
			$parent_var ='';
			if (isset($_GET['parent'])) $parent_var = '&parent='.$_GET['parent'];
			echo '
			<form action="'.$this->url.'dcms/page/c_del_items?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].$parent_var .'" method="POST" enctype="multipart/form-data">';
				
				echo '<table class="com_table">';
				
					echo '<th style="width: 17px"><input type="checkbox" class="select_all" /> </th>
						  <th style="width: 29px">id</th>';
					foreach($cf as $f){	
						if ((isset($_GET['order_by'])) && (isset($_GET['sort_type'])) && ($_GET['order_by']) == $f->enname) {
							if ($_GET['sort_type'] == 'ASC') {
								echo '<th><a href="'.$this->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&order_by='.$f->enname.'&sort_type=DESC&com_id='.$_GET["com_id"].$parent_var .'" title="'.$sort[0]->query.' &#8594; ORDER BY '.$f->enname.' DESC">'.$f->name.' &#9660;</a></th>';
							} else {
								echo '<th><a href="'.$this->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&order_by='.$f->enname.'&sort_type=ASC&com_id='.$_GET["com_id"].$parent_var .'" title="'.$sort[0]->query.' &#8594; ORDER BY '.$f->enname.' ASC">'.$f->name.' &#9650;</a></th>';
							}
						} else 
						{
							echo '<th><a href="'.$this->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&order_by='.$f->enname.'&sort_type=ASC&com_id='.$_GET["com_id"].$parent_var .'" title="'.$sort[0]->query.' &#8594; ORDER BY '.$f->enname.' ASC">'.$f->name.'</a></th>';
						}
					}
					echo '<th></th>';
					
					foreach($datat as $d){
						echo '<tr>
							  <td><input type="checkbox" class="all_items" name="del[]" value="'.$d->id.'"/></td>
							  <td>'.$d->id.'</td>';
						foreach($cf as $f){
							echo '<td>';
								$fname = $f->enname;
								if ($f->type < 3) echo $d->$fname;
								if (($f->type == 3) && ($d->$fname != '')) { $d->$fname = strip_tags($d->$fname);  $tmp = explode(' ',$d->$fname);  echo implode(' ', array_splice($tmp,0,2)).'..';  }
								if (($f->type == 4) && ($d->$fname != '')) echo $d->$fname;
								if ($f->type == 5) echo date('d m Y',strtotime($d->$fname));
								if ($f->type == 6) echo $d->$fname;
								if ($f->type == 7) echo $d->$fname;
								if ($f->type == 8) echo $d->$fname;
								if ($f->type == 9) echo $d->$fname;
								if (($f->type == 10) && ($d->$fname != '')) echo '<img style=" " class="comimgshow" src="'.$this->url.'uploads/'.$_GET['table'].'/1_'.$d->$fname.'" />';
								if (($f->type == 11) && ($d->$fname != '')) echo '<a  href="'.$this->url.'uploads/'.$_GET['table'].'/'.$d->$fname.'" >'.$d->$fname.'</a>';
								//new
								if ($f->type == 12) { if ($d->$fname == 1) echo '<b>Да</b>'; else echo 'Нет'; }
								if ($f->type == 13) { 
									if( $d->$fname != 0) {
										if ($sujest = $this->db->select($f->param ,"id = '{$d->$fname}'"))
										{
											echo $sujest[0]->name;
										}
									}
								}
								if ($f->type == 14) {  
									if ($cache1 != $d->$fname){
										if ($com_use = $this->db->select('coms',"id='".$d->$fname."'")){
											$cache1 = $d->$fname;
											$cache2 = $com_use;
										}
									}
									echo 	'<a href="'.$this->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$cache2[0]->table_name.'&pi='.$_GET['pi'].'&com_id='.$cache2[0]->id.'&parent='.$d->id.'">'.$cache2[0]->name.'</a>';
								} 
								
								
							echo '</td>';
						}
						echo '<td style=" width: 24px; "><a class="com_edit" href="'.$this->url.'dcms/page/edit_item?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&iid='.$d->id. $parent_var.'"><img src="'.$this->url.'dcms/style/icons2/manage.png" / ></a></td>
							  </tr>';
					}
				echo '</table>'. $pagination .'  
					  <input type="submit"  value="Удалить выбраное"/>
					  </form>';
			}
		}
		else echo "Данных нет";
	}
	
	function add_form(){ // К Форма добавления элемента
		$sort = $this->db->select('coms' ,"id='{$_GET['com_id'] }'");
		echo $this->func->com_header2($this,$sort);//header
		if ($cf = $this->db->select('coms_fields' ,"parent='{$_GET['com_id'] }' AND show_edit = 1 ORDER BY sort ASC")){
			$parent_var ='';
			if (isset($_GET['parent'])) $parent_var = '&parent='.$_GET['parent'];
			echo '
				<script src="'.$this->url.'/dcms/plugins/fck/ckeditor.js"></script>
			<form action="'.$this->url.'dcms/page/c_save_item?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].$parent_var.'" method="POST" enctype="multipart/form-data">';
			$count_edit = 1;
				foreach($cf as $f){
					$count_edit++;
				echo "
						<div class='com_items'>
						<script type='text/javascript'>
						var editor, html = '';
						function start".$count_edit."(){
						var config = {};
						//editor = CKEDITOR.appendTo('". $f->enname .$count_edit."', config, html );
						CKEDITOR.replace('1". $f->enname .$count_edit."', {
								
									filebrowserBrowseUrl: '".$this->url."dcms/files?path=../uploads&CKEditor=editor1&CKEditorFuncNum=3&langCode=ru/',
									filebrowserUploadUrl: '".$this->url."dcms/files/c_upload_fck'

								});
						}</script>";
				if (($f->enname == "parent") && (isset($_GET['parent']))) 	continue;	
				if ($f->type == 1) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>Строка 120</b></div>'; continue;} 
				if ($f->type == 2) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>Строка 500</b></div>'; continue;} 
				if ($f->type == 3) { echo '<p>'. $f->name. '</p>	<a href="javascript: start'.$count_edit.'()">Переключить редактор</a>	<textarea id="1'. $f->enname. $count_edit.'" rows="5" name="'. $f->enname. '"></textarea><b>Текст</b></p></div>'; continue;}
				if ($f->type == 4) { echo '<p>'. $f->name. '</p>	<a href="javascript: start'.$count_edit.'()">Переключить редактор</a>	<textarea id="1'. $f->enname. $count_edit.'" rows="5" name="'. $f->enname. '"></textarea><b>Текст</b></p></div>'; continue;} 
				if ($f->type == 5) { // ДАТА	
				echo '<p>'. $f->name. '</p><input  type="date" name="'. $f->enname. '" value=""/><b> DATE - 1000-01-01 </b></div>';
								continue;
							} 
				if ($f->type == 6) {echo '<p>'. $f->name. '</p><input type="datetime-local" name="'. $f->enname. '" value=""/><b> DATETIME - 100001-01 00:00:00</b></div>'; 
				continue;} 
				if ($f->type == 7) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>TINYINT - -128 до 127</b></div>'; continue;} 
				if ($f->type == 8) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>Целое число</b></div>'; continue;} 
				if ($f->type == 9) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/><b>Любое число</b></div>'; continue;} 
				if ($f->type == 10) {echo '<p>'. $f->name. '</p>
						<div style=" float: left; width: 300px; "><b>Один файл</b><input type="file" name="'. $f->enname. '" value=""/></div> 
						<div style=" float: left; width: 300px; "><b>Несколько файлов с добавлением новых записей</b><input type="file" name="'. $f->enname. '_multiple[]" value="" multiple  /></div> </div>'; continue;} 
				if ($f->type == 11) {echo '<p>'. $f->name. '</p><input type="file" name="'. $f->enname. '" value=""/><b>Файл</b></div>'; continue;} 
				//new
				if ($f->type == 12) { echo '<p>'. $f->name. '</p><input type="checkbox" style=" width:20px;"  name="'. $f->enname. '" value="1"/><b>Да </b></div>'; continue;} 
				if ($f->type == 13) { 
					$select_sajest = '<option value="">НЕТ</option>';
					if ($sujest = $this->db->select($f->param ,"name <> ''"))
					{
						foreach($sujest as $sj){
							$select_sajest .= '<option value="'.$sj->id.'">'.$sj->name.'</option>';
						}
					}
					echo '<p>'. $f->name. '</p><select  name="'. $f->enname. '" value="1"/>'.$select_sajest.'</select><b>Из списка</b></div>'; 
					continue;
				} 
				if ($f->type == 14) {
					$com_use = $this->db->select('coms',"id='{$f->param}'");
					echo '<p>'. $f->name. ': '.$com_use[0]->name.'</p><input type="hidden" name="'. $f->enname. '" value="'.$f->param.'"/><b>Доступно при добавленом элементе</b></div>'; 
				continue;	}				 
				
				echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value=""/></p></div>';
			
			} 
			echo '<div class="tools"><input type="submit"  name="act" value="Сохранить и добавить новый"/>';
			echo '<input type="submit"  name="act" value="Сохранить и к списку"/>';
			echo '<input type="submit"  name="act" value="Сохранить"/></div>';
			echo '</form>';
		}
	}
	
		function edit_item(){ // К Редактирование элемента
		//echo "<style type='text/css'> div.settings{display:none;}</style>";
		$sort = $this->db->select('coms' ,"id='{$_GET['com_id'] }'");
		echo $this->func->com_header2($this,$sort);//header
		if ($row = $this->db->select($_GET['table'] ,"id='{$_GET['iid'] }'")){
			if ($cf = $this->db->select('coms_fields' ,"parent='{$_GET['com_id'] }' AND show_edit = 1 ORDER BY sort ASC")){
				$parent_var ='';
				if (isset($_GET['parent'])) $parent_var = '&parent='.$_GET['parent'];
				
				//new view
				$subcombar='';
				foreach($cf as $f){ 
					$enname = $f->enname;
					if ($f->type == 14) {	
						if ($com_use = $this->db->select('coms',"id='".$f->param."'")){
							$subcombar .=' <a href="'.$this->url.'dcms/page/show_com?page='.$_GET['page'].'&table='.$com_use[0]->table_name.'&pi='.$_GET['pi'].'&com_id='.$com_use[0]->id.'&parent='.$_GET['iid'].'">'.$com_use[0]->name.'</a> &nbsp';
						} else echo '<a>!</a>';
						continue;
					}
				}
				if ($subcombar !='') echo "<p>$subcombar</p>"; 
				
				echo '
					<script src="'.$this->url.'/dcms/plugins/fck/ckeditor.js"></script>
				<form action="'.$this->url.'dcms/page/c_save_item?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&iid='.$_GET["iid"].$parent_var.'" method="POST" enctype="multipart/form-data">';
				echo '<input type="submit"   name="act"  value="Сохранить и добавить новый"/>';
				echo '<input type="submit"   name="act"  value="Сохранить и к списку"/>';
				echo '<input type="submit"   name="act"  value="Сохранить"/>';
				$count_edit = 1;
				foreach($cf as $f){
					$enname = $f->enname;
					$count_edit++;
					echo "<div class='com_items'>";
					if (($f->type == 3) || ($f->type == 4)) {
						echo "
							<script type='text/javascript'>
							var editor, html = '';
							function start".$count_edit."(){
							var config = {};
							//editor = CKEDITOR.appendTo('". $f->enname .$count_edit."', config, html );
							CKEDITOR.replace('1". $f->enname .$count_edit."', {
									
										filebrowserBrowseUrl: '".$this->url."dcms/files?path=../uploads&CKEditor=editor1&CKEditorFuncNum=3&langCode=ru/',
										filebrowserUploadUrl: '".$this->url."dcms/files/c_upload_fck'

									});
							}</script>";
					}
					
					if ($f->type == 1) {echo '<p>'. $f->name. ':</p><input type="text" name="'. $f->enname. '" value="'.$row[0]->$enname.'"/><b>Строка 120</b></div>'; continue;} 
					if ($f->type == 2) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$row[0]->$enname.'"/><b>Строка 500</b></div>'; continue;} 
					if (($f->type == 3) || ($f->type == 4)) {
						$text_for_area = str_replace(array ("<", ">"),  array ("&lt;", "&gt;"), $row[0]->$enname);  
						echo '<p>'. $f->name. '</p>	<a href="javascript: start'.$count_edit.'()">Переключить редактор</a>	 
						<textarea  id="1'. $f->enname. $count_edit.'" rows="5" name="'. $f->enname. '">'.$text_for_area .'</textarea><b>Текст</b></div>'; 
						continue;
					}
					//if ($f->type == 4) { echo '<p>'. $f->name. '</p>	<a href="javascript: start'.$count_edit.'()">Переключить редактор</a>	<textarea  id="1'. $f->enname. $count_edit.'" name="'. $f->enname. '">'.$row[0]->$enname.'</textarea><b>Текст</b></div>'; continue;} 
					if ($f->type == 5) {echo '<p>'. $f->name. '</p><input type="date" name="'. $f->enname. '" value="'.$row[0]->$enname.'"/><b> DATE - 1000-01-01</b></div>'; 
			
					continue;} 
					if ($f->type == 6) {echo '<p>'. $f->name. '</p><input type="datetime-local" name="'. $f->enname. '" value="'.str_replace(" ","T", $row[0]->$enname).'"/><b> DATETIME - 100001-01 00:00:00</b></div>'; 
					
					continue;} 
					if ($f->type == 7) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$row[0]->$enname.'"/><b>TINYINT - -128 до 127</b></div>'; continue;} 
					if ($f->type == 8) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$row[0]->$enname.'"/><b>Целое число</b></div>'; continue;} 
					if ($f->type == 9) {echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$row[0]->$enname.'"/><b>Любое число</b></div>'; continue;} 
					if ($f->type == 10) {
						echo '<p>'. $f->name. '</p><input type="file" name="'. $f->enname. '" value=""/>'.$row[0]->$enname.' <b>Картинка max 4mb</b>';
						if ($row[0]->$enname != '') echo '<img class="comimgshow" src="'.$this->url.'uploads/'.$_GET['table'].'/1_'.$row[0]->$enname.'" />
						<a href="'.$this->url.'dcms/page/c_com_del_file?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&iid='.$_GET["iid"].$parent_var.'&field='.$f->enname.'&file=1_'.$row[0]->$enname.'">Удалить</a>
						';
						
						echo '</div>';
						continue;} 
					if ($f->type == 11) {
						echo '<p>'. $f->name. '</p><input type="file" name="'. $f->enname. '" value=""/>'.$row[0]->$enname.' <b>Файл</b>';
						if ($row[0]->$enname != '') echo '<a href="'.$this->url.'uploads/'.$_GET['table'].'/'.$row[0]->$enname.'" >Скачать ('.$row[0]->$enname.')</a>
						<a href="'.$this->url.'dcms/page/c_com_del_file?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&iid='.$_GET["iid"].$parent_var.'&field='.$f->enname.'&file='.$row[0]->$enname.'">Удалить</a>';
						echo '</div>'; continue;
						}
					if ($f->type == 12) {
						if ($row[0]->$enname == 1) {
								echo '<p>'. $f->name. '</p><input type="checkbox" style=" width:20px;"  name="'. $f->enname. '" value="1" checked/><b>Off/On (Галочка)</b></div>';
							} else {
								echo '<p>'. $f->name. '</p><input type="checkbox" style=" width:20px;"  name="'. $f->enname. '" value="1"/><b>Off/On (Галочка)</b></div>';
							}
						continue;
					}
					
					if ($f->type == 13) { 
						$select_sajest = '<option value="">НЕТ</option>';
						if ($sujest = $this->db->select($f->param ,"name <> ''"))
						{
							foreach($sujest as $sj){
								if ($row[0]->$enname ==  $sj->id) $select_sajest .= '<option selected value="'.$sj->id.'">'.$sj->name.'</option>';
								else $select_sajest .= '<option value="'.$sj->id.'">'.$sj->name.'</option>';
							}
						}
						echo '<p>'. $f->name. '</p><select  name="'. $f->enname. '" value="1"/>'.$select_sajest.'</select><b>Из списка</b></div>'; 
						continue;
					} 
					if ($f->type == 14) { continue; } 
								
					echo '<p>'. $f->name. '</p><input type="text" name="'. $f->enname. '" value="'.$row[0]->$enname.'"/></p></div>';
				
				} 
				echo '<div class="tools"><input type="submit"   name="act"  value="Сохранить и добавить новый"/>';
				echo '<input type="submit"   name="act"  value="Сохранить и к списку"/>';
				echo '<input type="submit"   name="act"  value="Сохранить"/></div>';
				echo '</form>';
			}
		}

	}	
}


Class clear{

	function c_mak_refresh() // обновление макета с удалением
	{
		if ($page = $this->db->select("pages","id='".$_GET['id']."'")) {
			$this->db->delete('pages_items', 'parent="'.$_GET['id'].'"');
			$this->c_select_design($_GET['id'],$page[0]->design);
			header('Location: '.$this->url.'dcms/page?page='.$_GET['id'] );
		}
	}
	
	function c_select_design($id_page,$id_new_design,$sub_design = 0) //Смена макета
	{
		if ($all_new_item_des = $this->db->select("designes_items","parent='".$id_new_design."' AND for_all = 0"))
		{
			//$this->db->delete('pages_items', 'parent="'.$id_page.'"'); //удаление всех старых элементов страницы
			
			if ($old_items = $this->db->select("pages_items","design='".$id_new_design."' AND parent = '".$id_page."'"))
				{
					return;
				}
				
			foreach($all_new_item_des as $i)
			{
				$add->parent = $id_page; 
				$add->design = $id_new_design; 
				$add->name = $i->name;
				$add->text =  $i->text;
				$add->editor = $i->editor;
				$add->komp =  $i->komp;
				$add->sort =  $i->sort;
				$add->rules =  $i->rules;
				$this->db->insert("pages_items",$add);	
			}
		}
	}

	function c_sort_ci(){
		if (isset($_POST['resort'])) {
			$i->sort = $_POST['resort'];
			$this->db->update("pages","id='". $_POST['pid'] ."'",$i);
		}
	
		if ($sortitems = $this->db->select($_GET['table'],"page_item='".$_GET['pi']."' ORDER BY d_sort ASC"))
		{
			echo '
					<html>
						<head>
							<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.js"></script>
							<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.form.js"></script>
							<script type="text/javascript" src="'.$this->url.'dcms/js/jquery_ui.js"></script>
							<script type="text/javascript" src="'.$this->url.'dcms/js/minibox.js"></script>					
							<title>Сортировка '.$_GET['table'].'</title>
							<link rel="stylesheet" type="text/css" href="'.$this->url.'dcms/style/editors.css" />
							</head><body>
							<div class="editors_head">
								<h2 class="title">324234</h2>
							</div>
							<div class="editors">
						';
			
			echo '<div class="table" id="sortable_pages" class="table ui-sortable">';
			foreach($sortitems as $c)
			{
				echo '
					<div class="row" id="'.$c->id.'">
						<span>id:'.$c->id .' &nbsp; '.$c->name .'</span>
						<div class="notifications"><span style="display: none !important;"></span></div>
					</div>';
			}
			echo '</div>
				
				
				<script type="text/javascript">
				$(function() {

					$( "#sortable_pages" ).sortable({

						placeholder: "ui-state-highlight",
							stop: function(event, ui) {
								var result = $("#sortable_pages").sortable("toArray");
								console.log(result);
								$.post("'.$this->url.'dcms/page/c_ci_resort_script?table='.$_GET['table'].'",{arr:result},function(data){ // с двумя параметрами и вызовом функции
								if (data != 1) 
								{	
									alert(data);  
									
								} 
								else
								{
									save_complite();
									
								}
							});
						}

							});
						});
				</script>
				';
			
			
			
			echo '</form>
					</div>
					</body>
					</html>';
		}
		else echo "Данных нет";
	}
	
	function c_ci_resort_script()
	{
		$count=0;
		foreach($_POST['arr'] as $a)
		{
			$count+=1000;
			$i->d_sort = ++$count;
			$this->db->update($_GET['table'],"id=". $a, $i);
		}
		echo 1;
	}
	
	function c_page_resort()
	{
		if (isset($_POST['resort'])) {
		$i->sort = $_POST['resort'];
		$this->db->update("pages","id='". $_POST['pid'] ."'",$i);
		}
	
		if ($page = $this->db->select("pages","id='".$_GET['id']."'"))
		{
			echo '
				<html>
					<head>
						<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.js"></script>
						<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.form.js"></script>
						<script type="text/javascript" src="'.$this->url.'dcms/js/jquery_ui.js"></script>
						<script type="text/javascript" src="'.$this->url.'dcms/js/minibox.js"></script>					
						<title>'.$page[0]->name.'</title>
						<link rel="stylesheet" type="text/css" href="'.$this->url.'dcms/style/editors.css" />
						</head><body style="overflow: scroll;">
						<div class="editors_head">
							<h2 class="title">'.$page[0]->name .'</h2>
							<div class="notifications"><span style="display: none !important;"></span></div>
						</div>
						<div class="editors">
					';
		if ($child = $this->db->select("pages","parent='".$_GET['id']."' ORDER BY sort ASC"))
		{
			echo '<div class="table" id="sortable_pages" class="table ui-sortable">';
			foreach($child as $c)
			{
				if ($c->off == 1) $page_off = " (Выкл.)"; else $page_off='';
 				echo '<div class="row" id="'.$c->id.'">'; 
				
				echo '<form action="'. $this->url .'dcms/page/c_page_resort?id='.$_GET['id'].'" method="POST">';
					echo '<span>id:'.$c->id .' &nbsp; '.$c->name .''.$page_off.'</span>
					<input type="hidden" name="pid" value="'.$c->id.'"/>
					<input type="text" name="resort" value="'.$c->sort.'"/>';
					echo '<input type="submit" name="" value="Сохранить">';
				echo '</form>';
				echo '</div>';
			}
			echo '</div>
			
			
			<script type="text/javascript">
			$(function() {

				$( "#sortable_pages" ).sortable({

					placeholder: "ui-state-highlight",
						stop: function(event, ui) {
							var result = $("#sortable_pages").sortable("toArray");
							//console.log(result);
							$.post("'.$this->url.'dcms/page/c_page_resort_script?id='. $_GET['id'].'",{arr:result},function(data){ // с двумя параметрами и вызовом функции
							console.log(data);
							if (data != 1) 
							{	
								alert("Порядок сортировки не сохранен");  
								
							}
							else
							{
								save_complite("ok");
								//window.location.replace(window.location);
							}
						});
					}

						});
					});
			</script>
			';
		
		}
		
		echo '</form>
				</div>
				</body>
				</html>';
		}
		else echo "Страница не найдена";
	}
	
	function c_page_resort_script()
	{
		$count=0;
		$i = new stdClass();
		foreach($_POST['arr'] as $a)
		{
			$count+=1000;
			$i->sort = ++$count;
			$this->db->update("pages","id='". $a ."'",$i);
		}
		echo 1;
	}
	
	function c_items_edit() 
	{
		if (isset($_POST['resort'])) {
		$i->sort = $_POST['resort'];
		$i->name = $_POST['name'];
		$i->komp = $_POST['komp'];
		$i->design = $_POST['design'];
		if ($this->db->update("pages_items","id='". $_POST['pid'] ."'",$i)) echo 1;
		}	
	}
	
	function c_add_new_pi(){
		if ($page = $this->db->select("pages","id='".$_GET['id']."'")){
			$add->name   = 'Новый блок';
			$add->parent = $page[0]->id;
			$add->design = $page[0]->design;
			if ($this->db->insert("pages_items",$add))
			header('Location: '.$this->url.'dcms/page?page='.$_GET['id'].'&edit=1' );
		}
	}
	
	function c_page_iten_resort_script()
	{
		$count=0;
		$i = new stdClass();
		foreach($_POST['arr'] as $a)
		{
			$count+=1000;
			$i->sort = $count;
			$this->db->update("pages_items","id='". $a ."'",$i);
		}
		echo "1";
	}
	
	function c_del_p_i() 
	{
		$this->db->delete('pages_items', 'parent="'.$_GET['id'].'"'); //удаление всех старых элементов страницы
		header('Location: '.$this->url.'dcms/page?page='.$_GET['id'].'&edit=1' );
	}
	
	function c_del_p_i1() 
	{
		$this->db->delete('pages_items', 'id="'.$_GET['piid'].'"'); //1
		header('Location: '.$this->url.'dcms/page?page='.$_GET['id'].'&edit=1' );
	}
	
	function c_add_page() 
	{
		$use_parent_design = $this->db->select("pages","id=".$_GET['id']);
		$max_sort_id = $this->db->select("pages","parent=".$_GET['id']." ORDER BY sort DESC LIMIT 1");
		$add->parent = $_GET['id'];
		$add->name = 'Новая_страница';
		$add->path = 'path_'.rand(9999, 999999);;
		$add->sort = $max_sort_id[0]->sort+1;
		//Если подмакет не задан 
		if ($use_parent_design[0]->sub_design == 0){
			$add->design = $use_parent_design[0]->design;
		} else {
			$add->design = $use_parent_design[0]->sub_design;
			$add->sub_design = $use_parent_design[0]->sub_design;
		}
		if ($this->db->insert("pages",$add)) //создание страницы
		{
			//создание компонентов страницы
			if ($new_id = $this->db->select("pages","id>0  ORDER BY id DESC LIMIT 1 ","id"))
			{
				//print_r($add);
				$this->c_select_design($new_id[0]->id, $add->design, $use_parent_design[0]->sub_design);	
				header('Location: '.$this->url.'dcms/page?page='.$new_id[0]->id );
				//echo '<script type="text/javascript">location.replace("'. $this->url .'dcms/page");</script>';
			}
		}
		else echo 'Ошибка создание новой страницы';
	}

	function c_recurs_page_dell($id)
	{
		$this->db->delete('pages', 'id="'.$id.'"');
		$this->db->delete('z', 'parent="'.$id.'"');
		if ($pd = $this->db->select("pages","parent='".$id."'"))
		{
			foreach($pd as $p)
			{
				$this->c_recurs_page_dell($p->id);
			}
		}
	}
	
	function c_del_page()
	{	
		$this->db->delete('pages', 'id="'.$_GET['id'].'"');
		$this->db->delete('pages_items', 'parent="'.$_GET['id'].'"');
		if($pd = $this->db->select("pages","parent='".$_GET['id']."'"))
		{
			foreach($pd as $p)
			{
				$this->c_recurs_page_dell($p->id);
			}
		}
		header('Location: '.$this->url.'dcms/page?error=3');
	}
	
	function c_save_set()
	{
		$page = $this->db->select("pages","id='".$_GET['id']."'");
		$page[0]->name = $_POST['name'];
		if ($_POST['path']==''){
			if ((isset($this->use_rf_domain)) && ($this->use_rf_domain)) {
				//rf
				$page[0]->path = trim($_POST['name']); 
			} else {
				//lat
				$page[0]->path = trim($this->func->ru_en_translite($_POST['name'])); 
			}
		} else {
			$page[0]->path = $_POST['path'];
		}
		if ($_POST['path']!='') { 
			$par = explode('_', $_POST['path']);
			if ($par[0] == "path"){ // Если путь стандартный
				if ($_POST['name'] != "Новая_страница") { 
					if ((isset($this->use_rf_domain)) && ($this->use_rf_domain)) {
						//rf
						$page[0]->path = trim($_POST['name']); 
					} else {
						//lat
						$page[0]->path = trim($this->func->ru_en_translite($_POST['name'])); 
					}
				}
			}
		}
		$stop_chr = array(' ','/','"',"'",'?','=','$','%','&','(',')','dcms','#','№','^',':',',','.','[',']','{','}','!','@','*','+','\\','`','\<','\>');
		$page[0]->path = str_replace($stop_chr, '', $page[0]->path);
		$page[0]->path = strtolower($page[0]->path); 
		
		if ($_POST['title']=='') $page[0]->title = $_POST['name']; else $page[0]->title = $_POST['title'];
		if ($_POST['keyw']=='') $page[0]->keyw = $_POST['name']; else $page[0]->keyw = $_POST['keyw'];
		$page[0]->descr = $_POST['descr'];
		if (isset($_POST['parent'])) $page[0]->parent = $_POST['parent'];
		if (isset($_POST['sub_maket'])) $page[0]->sub_design = $_POST['sub_maket'];
		if (isset($_POST['maket'])) { if ($page[0]->design != $_POST['maket']) { $this->c_select_design($page[0]->id,$_POST['maket'],$_POST['sub_maket'] ); echo 'sd'; }}
		$page[0]->design = $_POST['maket']; // Смена макета
		if ((isset($_POST['onoff'])) && ($_POST['onoff'] == 1)) $page[0]->off = 1; else  $page[0]->off = 0;
		if ((isset($_POST['hide_child'])) && ($_POST['hide_child'] == 1))  $page[0]->hide_child = 1; else  $page[0]->hide_child = 0;
		$this->db->update("pages","id='".$_GET['id']."'",$page[0]); 		
	}

	function c_menu_show()
	{
		if (isset($_GET['new'])) {
		if (isset($_SESSION['map']))	$arr = $_SESSION['map']; 
		$arr[$_GET['new']] = 0; 
		$_SESSION['map'] = $arr;
		}	else 	print_r($_SESSION['map']);
	}
	
	function c_menu_hide()
	{
		if (isset($_GET['new'])) {
		if (isset($_SESSION['map'])) 	$arr = $_SESSION['map']; 
		$arr[$_GET['new']] = 1; 
		$_SESSION['map'] = $arr;
		} else print_r($_SESSION['map']);
	}
	
	function c_edit()
	{
		if ($page = $this->db->select("pages_items","id='".$_GET['id']."'"))
		{
			$page_parent_name = "***";
			if ($pn = $this->db->select("pages","id='".$page[0]->parent ."'")){
				$page_parent_name = $pn[0]->name;
			}
			echo '
			<html>
				<head>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.form.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery_ui.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/minibox.js"></script>
					
					<title>'.$page[0]->name .' — '.$page_parent_name.'</title>

				
					
					<script>
							 
							function is_ready_data(){
								if (typeof ready_data == "function") { 
									if (ready_data()) $(".rcform").submit();
								}
							}
						</script>	
					<link rel="stylesheet" type="text/css" href="'.$this->url.'dcms/style/editors.css" />
					</head><body>
					
				';
		
		
			if ($_GET['editor'] ==  'text' ){ //текст
				echo '<div class="editors_head">
						<form  class="myForm editform" action="'. $this->url .'dcms/page/c_save?editor='.$_GET['editor'].'&id='.$_GET['id'].'" method="POST">
						<h2 class="title">'.$page[0]->name .' — '.$page_parent_name.' </h2>
						<div class="notifications"><span style="display: none !important;"></span></div>
						<input class="right_submit" type="submit" name=""  onClick="is_ready_data();" value="Сохранить">
					</div>
					<div class="editors">
				<textarea style="width:100%;  " name="text'.$page[0]->id.'"  onkeydown="insertTab(this, event);">'.htmlspecialchars($page[0]->text).'</textarea></form>';
			}
			if ($_GET['editor'] ==  'fck' ){ // виз
				echo '
				<div class="editors_head">
						<form  class="" action="'. $this->url .'dcms/page/c_save_old?editor='.$_GET['editor'].'&id='.$_GET['id'].'" method="POST">
						<h2 class="title">'.$page[0]->name .' — '.$page_parent_name.' </h2>
						<div class="notifications"><span style="display: none !important;"></span></div>
						<input class="right_submit" type="submit" name=""  onClick="is_ready_data();" value="Сохранить">
					</div>
					<div class="editors">
				<script src="'.$this->url.'/dcms/plugins/fck/ckeditor.js"></script>';
									
				echo '<textarea id="editor1" class="ckeditor" name="text'.$page[0]->id.'">'. htmlspecialchars($page[0]->text).'</textarea>
				<script>
					CKEDITOR.replace( "editor1", {	
						filebrowserBrowseUrl: "'.$this->url.'dcms/files?path=../uploads&CKEditor=editor1&CKEditorFuncNum=3&langCode=ru/",
						filebrowserUploadUrl: "'.$this->url.'dcms/files/c_upload_fck"
					});
				
					function ready_data(){
						var inst = CKEDITOR.GetInstance("editor1").GetHTML(true);  
						$("#editor1").html(inst.GetHTML());
						return true;
					}
				</script>
				</form>';
			}
			
			if ($_GET['editor'] ==  'cm' ){  //код
				echo '  
					<div class="editors_head">
						<form  class="myForm editform" action="'. $this->url .'dcms/page/c_save?editor='.$_GET['editor'].'&id='.$_GET['id'].'" method="POST">
						<h2 class="title">'.$page[0]->name .' — '.$page_parent_name.' </h2>
						<div class="notifications"><span style="display: none !important;"></span></div>
						<input class="right_submit" type="submit" name=""  onClick="is_ready_data();" value="Сохранить">
					</div>
					<div class="editors">
						<textarea id="ace_code" name="text'.$page[0]->id.'" style="display:none;" >'.htmlspecialchars($page[0]->text).'</textarea>
				
						<div class="ace_item" id="ace_page_code" style="   margin-top: 3px;">'.htmlspecialchars($page[0]->text).'</div>'; 
				echo '
				<script src="'. $this->url.'dcms/plugins/ace/ace.js" type="text/javascript" charset="utf-8"></script>
						<script>
							 var ace_page_code = ace.edit("ace_page_code");
							 ace_page_code.setTheme("ace/theme/eclipse");
							 ace_page_code.getSession().setMode("ace/mode/php");
							// ace_page_code.setOption("maxLines", 90);
							 //ace_page_code.getSession().getDocument().getLength() * ace_page_code.renderer.lineHeight + ace_page_code.renderer.scrollBar.getWidth()
							
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
				
					</form>
				';
				
				
			}
				echo '
				</div>
				</body>
				</html>';
		}	
	} 

	function c_com_del_file(){
		//удаление файла
		$dell_file = '../uploads/'.$_GET['table'] .'/' . $_GET['file'];
		$file_deleting = '2_'.substr($_GET['file'],2);
		$dell_file2 = '../uploads/'.$_GET['table'] .'/' . $file_deleting;
		@unlink($dell_file2);
		if ($cf = $this->db->select($_GET['table'],"id={$_GET['iid']}")){
				$field = $_GET['field'];
				$cf[0]->$field  = '';
				$this->db->update($_GET['table'],"id='". $_GET['iid'] ."'",$cf[0]);
		}
		if (@unlink($dell_file)) header('Location: '.$this->url.'dcms/page/edit_item?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&iid='.$_GET['iid'] );
		else header('Location: '.$this->url.'dcms/page/edit_item?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&iid='.$_GET['iid'].'&error=1' );
	}
	
	function c_save_item(){ // К Добавление элемента
		function up_file($file_up,$func,$f){
			$uploaddir = '../uploads/'.$_GET['table'] .'/';
			if ($file_up['name'] != ''){ 
				$new_file_name = $func->func->check_filename($file_up['name'],$uploaddir,'1_');
				$new_file_name = substr($new_file_name,2); // удаляем 1_ 
				if (copy($file_up['tmp_name'], $uploaddir . $new_file_name)) {
					$resolutions = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/","", $f->param);
					if ($resolutions !=''){
						$resxy = explode('x', $resolutions);
						$minx = 640;
						$miny = 480;
						if ((isset($resxy[0])) && $resxy[0] > 0) {
							$minx = $resxy[0];
							if ((isset($resxy[1])) && $resxy[0] > 0) {
								$miny = $resxy[1];
								img_resize($uploaddir.$new_file_name, $uploaddir.'1_'.$new_file_name, $minx, $miny,  90, 0xFFFFF0, 0);
							}
						}
						if ((isset($resxy[2])) && $resxy[0] > 0){
							$min2x = $resxy[2];
							if ((isset($resxy[3])) && $resxy[0] > 0){
								$min2y = $resxy[3];
								img_resize($uploaddir.$new_file_name, $uploaddir.'2_'.$new_file_name, $min2x, $min2y,  80, 0xFFFFF0, 0);
							}
						}
						@unlink($uploaddir.$new_file_name);
					} else { rename($uploaddir.$new_file_name, $uploaddir.'1_'.$new_file_name);	}
					return $new_file_name;
				}
			} else  return '';	
		}
		
		function arrayImages( &$file_post )
		{
			if( empty( $file_post ) ) {
				return $file_post;
			}
			if( 'array'!==gettype($file_post['name']) ) {
				return $file_post;
			}
			$keys = array_keys($file_post['name']);
			$file_array = array();
			foreach ($keys as $key) {
			   foreach ($file_post as $res=>$item) {
				   $file_array[$key][$res] = $item[$key];
			   }
		   }
		   return $file_array;
		}

		$uploaddir = '../uploads/'.$_GET['table'] .'/';
		include 'img_res.php';
		if ($cf = $this->db->select('coms_fields' ,"parent='{$_GET['com_id'] }' AND show_edit = 1 ORDER BY sort ASC")){
			$add = new stdClass();
			$add->name='Без названия';
			$add->page_item	= $_GET["pi"];
			$add->sys_date	= date('Y-m-d');
			foreach($cf as $f){
				$enname = $f->enname;
				
				if (isset($_GET['parent'])) $add->parent = $_GET['parent'];	
				if ($f->type < 3) {  if(isset($_POST[$enname])) $add->$enname = str_replace('"','&#034;',$_POST[$enname]); } 
				if (($f->type < 6) && ($f->type > 2)) {  if(isset($_POST[$enname])) $add->$enname = $_POST[$enname]; } 
				if (($f->type == 6)) {  if(isset($_POST[$enname])) $add->$enname = str_replace("T"," ", $_POST[$enname]); } 
				if (($f->type < 10) && ($f->type > 6)) {  if(isset($_POST[$enname])) $add->$enname = $_POST[$enname]; } 
				if ($f->type == 10) {  //IMG

					if ($_FILES[$enname]['tmp_name'] == ''){
						//if array
						//Первую загрузить сюда 
						//print_r($_FILES[$enname.'_multiple']);
						$all_f = arrayImages($_FILES[$enname.'_multiple']);
						$add_mas = new stdClass();
						$add_mas->name='';
						$add_mas->page_item	= $_GET["pi"];
						$add_mas->sys_date	= date('Y-m-d');
						if (isset($_GET['parent'])) $add_mas->parent = $_GET['parent'];	
						$count_img=0;
						foreach($all_f as $fup){
							if ($count_img == 0) {
								$add->$enname =  up_file($fup,$this,$f);
							} else {
								$add_mas->$enname = up_file($fup,$this,$f);
								$this->db->insert($_GET['table'],$add_mas);	
							}
							$count_img++;
						}				
						
					}	else{
						//single
						$add->$enname =  up_file($_FILES[$enname],$this,$f);
					}
			   }
				if ($f->type == 11) {  //file
					if ($_FILES[$enname]['name'] != ''){
						$new_file_name = $this->func->check_filename($_FILES[$enname]['name'],$uploaddir,'f_');
						if (copy($_FILES[$enname]['tmp_name'], $uploaddir  . $new_file_name)) 
						{
							$add->$enname = $new_file_name;
						}
					}
				} 
				//new
				if ($f->type == 12){
					if (isset($_POST[$enname]))  $add->$enname  = 1;  else $add->$enname  = 0;
				}
				
				if ($f->type == 13) {  if(isset($_POST[$enname])) $add->$enname = $_POST[$enname]; } 
				if ($f->type == 14) {  if(isset($_POST[$enname])) $add->$enname = $_POST[$enname]; } 
				
			}
			
			if (isset($_GET['iid'])) { 	$this->db->update($_GET['table'],"id='". $_GET['iid'] ."'",$add); }
			else{
				$this->db->insert($_GET['table'],$add);
			}
		}
		$parent_var ='';
		
		if (isset($_GET['parent'])) $parent_var = '&parent='.$_GET['parent'];
		if ($_POST['act'] == "Сохранить и добавить новый") header('Location: '.$this->url.'dcms/page/add_form?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].$parent_var );
		if ($_POST['act'] == "Сохранить и к списку") header('Location: '.$this->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].$parent_var );
		
		if (isset($_GET['iid'])){
			if ($_POST['act'] == "Сохранить") header('Location: '.$this->url.'dcms/page/edit_item?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&iid='.$_GET['iid'].$parent_var );
		} else {
			if ($_POST['act'] == "Сохранить"){
				$indert_com_id = db::$mysqli->insert_id;
				header('Location: '.$this->url.'dcms/page/edit_item?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&iid='.$indert_com_id.$parent_var );
			}
		}
	}
	
	function c_edit_propertis(){ // К Добавление элемента
		$uploaddir = '../uploads/com_page_extension/';
		include 'img_res.php';
		if ($cf = $this->db->select('coms_fields' ,"parent='{$_GET['com_id'] }' AND show_edit = 1 ORDER BY sort ASC")){
			$add = new stdClass();
			$add->name='';
			$add->page_item	= $_GET['page'];
			$add->page_id	= $_GET['page'];
			$add->sys_date	= date('Y-m-d');
			foreach($cf as $f){
				$enname = $f->enname;
				if ($f->type < 3) {  if(isset($_POST[$enname])) $add->$enname = str_replace('"','&#034;',$_POST[$enname]); } 
				if (($f->type < 10) && ($f->type > 2)) {  if(isset($_POST[$enname])) $add->$enname = $_POST[$enname]; } 
				if ($f->type == 10) {  //IMG
					$new_file_name = 'img_'.substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz0123456789',5)),0,7) . strtolower(strrchr($_FILES[$enname]['name'],'.'));
					if (copy($_FILES[$enname]['tmp_name'], $uploaddir . $new_file_name)) 
					{
						$add->$enname = $new_file_name;
						//Ресайз по пораметру  *200x300x640x480
						$resolutions = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/","", $f->param);
						if ($resolutions != ''){
							$resxy = explode('x', $resolutions);
							$minx = 640;
							$miny = 480;
							if ((isset($resxy[0])) && $resxy[0] > 0) {
								$minx = $resxy[0];
								if ((isset($resxy[1])) && $resxy[0] > 0) {
									$miny = $resxy[1];
									img_resize($uploaddir.$new_file_name, $uploaddir.'1_'.$new_file_name, $minx, $miny,  90, 0xFFFFF0, 0);
								}
							}
							if ((isset($resxy[2])) && $resxy[0] > 0){
								$min2x = $resxy[2];
								if ((isset($resxy[3])) && $resxy[0] > 0){
									$min2y = $resxy[3];
									img_resize($uploaddir.$new_file_name, $uploaddir.'2_'.$new_file_name, $min2x, $min2y,  80, 0xFFFFF0, 0);
								}
							}
							if (unlink($uploaddir.$new_file_name)) echo "OK (i)";
							else { rename($uploaddir.$new_file_name, $uploaddir.'1_'.$new_file_name);	}
						} else { rename($uploaddir.$new_file_name, $uploaddir.'1_'.$new_file_name);	}
					}
				} 
				if ($f->type == 11) {  //IMG
					$new_file_name = date("m_Y").'_'.$this->func->ru_en_translite($_FILES[$enname]['name']);
					if (copy($_FILES[$enname]['tmp_name'], $uploaddir  . $new_file_name)) 
					{
						$add->$enname = $new_file_name;
					}
				} 
				//new
				if ($f->type == 12){
					if (isset($_POST[$enname]))  $add->$enname  = 1;  else $add->$enname  = 0;
				}
				
				if ($f->type == 13) {  if(isset($_POST[$enname])) $add->$enname = $_POST[$enname]; } 
				if ($f->type == 14) {  if(isset($_POST[$enname])) $add->$enname = $_POST[$enname]; } 
				
			}
			
			if (isset($_GET['iid'])) { 	$this->db->update("com_page_extension","id='". $_GET['iid'] ."'",$add); }
			else{
				$this->db->insert("com_page_extension",$add);
			}
		}
		header('Location: '.$this->url.'dcms/page?page='.$_GET['page']);
	}
	
	function c_del_items(){	// К Удаление элементов
		if (isset($_POST['del'])){
			$del_count = 0;
			foreach($_POST['del'] as $i)
			{
				$del_count++;
				$this->db->delete($_GET['table'], 'id="'.$i.'"');
			}
		}
		$parent_var ='';
		if (isset($_GET['parent'])) $parent_var = '&parent='.$_GET['parent'];
		header('Location: '.$this->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].$parent_var.'&error=2-'.$del_count  );
	}
	
	function c_save() {
		$page = $this->db->select("pages_items","id='".$_GET['id']."'");
		$page[0]->text = $_POST['text'.$_GET['id']];
		if ($this->db->update("pages_items","id='".$_GET['id']."'",$page[0])) echo 'ok'; 
		else echo 'error';
	}	
	
	function c_save_old() {
		$page = $this->db->select("pages_items","id='".$_GET['id']."'");
		$page[0]->text = $_POST['text'.$_GET['id']];
		if ($this->db->update("pages_items","id='".$_GET['id']."'",$page[0])) 
		header('Location: '.$this->url.'dcms/page/c_edit?editor=fck&id='.$_GET['id']);
	}
		
	function c_del_ext(){
		//c_del_ext
		$this->db->delete("com_page_extension", 'id='.$_GET['com_ext_id']);
		header('Location: '.$this->url.'dcms/page?page='.$_GET['page']);
	}
	
	
	function c_del_ext_file(){
		//'.$this->url.'dcms/page/c_del_ext_file?page='.$_GET['page'].'&com_ext_id='.$ext[0]->id.'
		$ext = $this->db->select("com_page_extension","id=".$_GET['com_ext_id']);
		$f = $_GET['field'];
		$ext[0]->$f = '';
		if ($this->db->update("com_page_extension","id=".$_GET['com_ext_id'],$ext[0])) {
		header('Location: '.$this->url.'dcms/page?page='.$_GET['page']);
		}
	}
	
	function c_error_list()
	{	
		// &error=2
		$er = explode("-",$_GET['error']);
		$er_list = array(
		1    => 'Файл не удалён',
		2    => 'Удалено',
		3    => 'Страница удалена',
		4    => 'Файл не удалён');
		
		echo $er_list[$er[0]].' '; 
		if (isset($er[1])) echo $er[1];
	}
	
	function c_help1(){
		echo "<h2>Переменные DCMS</h2>"; 
		echo "<table>"; 
		
		echo '<tr><td>
		$url
		</td><td>
		URL сайта
		</td></tr>';
		
		echo '<tr><td>
		$id
		</td><td>
		ID текущей страницы
		</td></tr>';
		
		echo '<tr><td>
		$name
		</td><td>
		ИМЯ текущей страницы
		</td></tr>';
		
		echo '<tr><td>
		$keywords
		</td><td>
		КЛЮЧЕВЫЕ СЛОВА текущей страницы
		</td></tr>';
		
		
		echo '<tr><td>
		$parent
		</td><td>
		ID страницы родителя текущей страницы
		</td></tr>';
		
		
		echo '<tr><td>
		$description
		</td><td>
		ОПИСАНИЕ текущей страницы
		</td></tr>';
		
		
		echo '<tr><td>
		$path
		</td><td>
		ФРАГМЕНТ ПУТИ к текущей страницы
		</td></tr>';
		
		echo '<tr><td>
		$brunch
		</td><td>
		МАССИВ ФОАГМЕНТОВ ПУТЕЙ до текущей страницы
		</td></tr>';
		
		echo '<tr><td>
		$page[0]
		</td><td>
		ОБЪЕКТ текущей страницы
		</td></tr>';
		
		
		echo '<tr><td>
		$d_iid
		</td><td>
		ОБЪЕКТ текущего элемента страницы
		</td></tr>';		
		echo "</table>"; 
	}	
	
	function c_help2(){
		echo "<h2>Функции DCMS</h2>"; 
		echo "<table>"; 
		
		echo '<tr><td>
		$dcms->check_num(string)
		</td><td>
		Преобразует строку в число
		</td></tr>';
		
		echo '<tr><td>
		$dcms->check_string(string)
		</td><td>
		Проверяет строку на опасное содержимое
		</td></tr>';
		
		echo '<tr><td>
		$dcms->email_t_get(имя,get_name)
		</td><td>
		ИМЯ текущей страницы
		</td></tr>';
		
		echo '<tr><td>
		$dcms->email_t_post(имя,post_name)
		</td><td>
		КЛЮЧЕВЫЕ СЛОВА текущей страницы
		</td></tr>';
		
		
		echo '<tr><td>
		$dcms->ru_en_translite(ru_sthing)
		</td><td>
		ID страницы родителя текущей страницы
		</td></tr>';
		
		
		echo '<tr><td>
		$dcms->is_login()
		</td><td>
		ОПИСАНИЕ текущей страницы
		</td></tr>';
		
	
		echo "</table>"; 
	}
	
	function c_claer_log()
	{	
		$this->db->delete('admin_logs','id>0',999999);
		header('Location: '.$this->url.'dcms/page' );
	}
	
	function c_exit()
	{	
		session_unset();
		header('Location: '.$this->url.'dcms' );
	}
}