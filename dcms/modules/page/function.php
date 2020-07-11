<?php
Class fpage extends func {



	function format_size($size) {

		$mod = 1024;

		$units = explode(' ','B KB MB GB TB PB');

		for ($i = 0; $size > $mod; $i++) {

			$size /= $mod;
		}

		return round($size, 2) . ' ' . $units[$i];

	}

	function recurs($data,$start=0,$url,$level=0)
	{
		$level++;
		$res = '';	
		if (isset($_SESSION['map'])) { $menu_map = $_SESSION['map'];		 }
		$datanew = $data;
		foreach($data as $d)
		{
			$hide='';
			$buttonsh='';
			if ($d->parent == $start)
			{
				$pname = '';
				if ($d->name == '') $pname = '! Без названия'; else $pname = $d->name;
				if ($d->off == 1) $pname .= ' (Выкл.)';
				
					//есть или нет потомки
					$is_childe = false;
					foreach($datanew as $k)
					{
						if ($k->parent == $d->id)
						{
							$is_childe = true;
						}
					}
					
					if ($is_childe)  //по настройкам 
					{
						if ($d->hide_child == 1) {	//Показать					
							$buttonsh = '<a href="javascript:void(0)" pid="'.$d->id.'" class="_close _closed"></a>';
							$hide = 'class="hide"';
						}
						else{	//скрыть				
							$buttonsh = '<a href="javascript:void(0)"  pid="'.$d->id.'" class="_close _opened"></a>';
							$hide = '';
						}
					
						if ($level >= 2) { $buttonsh = '<a href="javascript:void(0)"  pid="'.$d->id.'"  class="_close _closed"></a>'; 	$hide = 'class="hide"';}
						if (isset($menu_map[$d->id])) //по сесии
						{
							if ($menu_map[$d->id] == 1) { $buttonsh = '<a href="javascript:void(0)"  pid="'.$d->id.'"  class="_close _closed"></a>'; 	$hide = 'class="hide"'; }
							if ($menu_map[$d->id] == 0) { $buttonsh = '<a href="javascript:void(0)" pid="'.$d->id.'"   class="_close _opened"></a>';  $hide=''; }
						}
					}
					
				if ($d->id == 1) { $buttonsh = ''; $hide = ''; }
			
				if ((isset($_GET['page'])) && ($_GET['page'] == $d->id)) //по поисутствию
				{
				
						$menu_map[$d->id] = 0; 
						$_SESSION['map'] =  $menu_map;
						
					$res .= '
					<li class="active">
						'.$buttonsh.'
					    <a class="_add"  rel="tooltip" data-original-title="Добавить подрстраницу" href="'.$url.'dcms/page/c_add_page?id='.$d->id.'" class="add_pm" data-placement="right"></a>
						<a class="_name" href="'.$url.'dcms/page?page='.$d->id.'"><b>'.$pname .'</b></a>		
					';
					$hide = '';
				}
				else
				{
					$res .= '
					<li>
						'.$buttonsh.'					
						<a class="_add" rel="tooltip" data-original-title="Добавить подрстраницу" href="'.$url.'dcms/page/c_add_page?id='.$d->id.'" class="add_pm" data-placement="right"></a>
						<a class="_name" href="'.$url.'dcms/page?page='.$d->id.'">'.$pname .'</a>	
					';
				}
				
				$res .= '<ul '.$hide.'>'.$this->recurs($data,$d->id,$url,$level) .'</ul>';
				$res .=  '</li>';
			}
		} 
		return $res;
	} 
	
	function page_settings($dcms, $page, $url,$all_page,$makets,$sub_makets)
	{
		if ($page->off == 1) { $off = '<input type="checkbox" checked name="onoff" value="1">';
		} else { $off = '<input type="checkbox" name="onoff"  value="1">'; }
		if ($page->hide_child  == 0) { $hide_child  = '<input type="checkbox" name="hide_child" value="a2">';
		} else { $hide_child  = '<input type="checkbox" name="hide_child" checked value="a2">'; }
		$out = '<div class="block">
		
						<a tabindex="16" href="javascript:void(0)" rel="tooltip" data-original-title="Сохранить" data-placement="left" class="_master"></a>
						<h2>Параметры страницы</h2>
					</div>
					
					<div class="block">
						<div class="group">
							<form class="myForm rcform" action="'. $url .'dcms/page/c_save_set?id='.$_GET['page'].'" method="POST">
								<p>
									<label>Название&nbsp;<a style="display:inline;" data-original-title="Название в панели управления и в некоторых случаях на сайте " data-placement="right" rel="tooltip" href="javascript:void(0)" >?</a></label>
									<input  tabindex="11" class="must_reload" type="text" name="name" value="'.$page->name.'" />
								</p>
								<p> 
									<label>Путь&nbsp;<a style="display:inline;" data-original-title="Часть пути к этой странице, пример: contacts" data-placement="right" rel="tooltip" href="javascript:void(0)" >?</a></label>
									<input  tabindex="12" type="text"  name="path" value="'.$page->path.'" />
								</p>
								<p>
									<label>Заголовок&nbsp;<a style="display:inline;" data-original-title="Заголовок страницы, отображается вверху окна браузера" data-placement="right" rel="tooltip" href="javascript:void(0)" >?</a></label>
									<input  tabindex="13" type="text" name="title" value="'.$page->title.'" />
								</p>
								<p>
									<label>Ключевые слова&nbsp;<a style="display:inline;" data-original-title="Ключевые фразы для поиска данной страницы" data-placement="right" rel="tooltip" href="javascript:void(0)" >?</a></label>
									<textarea  tabindex="14" name="keyw">'.$page->keyw.'</textarea>
								</p>
								<p>
									<label>Описание&nbsp;<a style="display:inline;" data-original-title="Описание страницы, используется для продвижения" data-placement="right" rel="tooltip" href="javascript:void(0)" >?</a></label>
									<textarea  tabindex="15" name="descr">'.$page->descr.'</textarea>
								</p>';
						
					$out .= '		<p>
									'.$off.'<span class="checkbox_title">Выключена&nbsp;<a style="display:inline;" data-original-title="Отключение страницы изымает её из некоторых меню" data-placement="right" rel="tooltip" href="javascript:void(0)" >?</a></span>
								</p>';
					if ($dcms->user[1] >= 2) $out .= '<!--';	
								$out .= '<p>
									<label>Макет страницы. <a style=" display: inline; " href="javascript:go(\''. $url .'dcms/page/c_mak_refresh?id='.$_GET['page'].'\')" >Обновить макет</a></label>
									<select class="must_reload"  name="maket" style=" width: 260px; ">
									'.$makets.'	
								   </select>
								</p>	
								<p>
									<label>Макет подстраниц</label>
									<select class="must_reload"  name="sub_maket" style=" width: 260px; ">
									'.$sub_makets.'	
								   </select>
								</p>	
								';
								
								if ($_GET["page"] !=1) $out .=	'<p>
									<label>Переместить раздел&nbsp;<a style="display:inline;" data-original-title="Переместить раздел в другой подраздел" data-placement="right" rel="tooltip" href="javascript:void(0)" >?</a> </label>
									<select class="must_reload"  name="parent" style=" width: 260px; ">
									'.$all_page.'
								   </select>
								</p><p><a href="javascript:go(\''. $url .'dcms/page/c_del_page?id='.$_GET['page'].'\')" >Удалить страницу</a></p>';
					
							
				if ($dcms->user[1] >= 2) $out .= '-->';
			$out .= '</form>';				
				$out .='</div>
					
						<br />
						
						<a href="javascript:window.open(\''.$url.'dcms/page/c_page_resort?id='.$_GET["page"].'\',\'\',\'width=900,height=500\'); void(0)">Сортировка подстраниц</a>';
					
					
						
				$out .= '</div>';
				
		return $out;
	}
	
	function page_edit_text($pi,$url){
		//return '<div class="page_item"><a href="'.$url.'dcms/page/c_edit?id='.$pi->id.'">'.$pi->name.'</a><span>'.substr(strip_tags($pi->text),0,100).'</span><a href="_e_text">txt</a></div>';
		$out_content =  '
		<div class="pageitem">
			<div class="_head">
				<h3>
					<a rel="tooltip"  href="javascript:void(0)">'.$pi->name.'</a>
				</h3>
				<div class="editors">
					<a rel="tooltip" data-placement="bottom" data-original-title="Текст" href="javascript:window.open(\''. $url .'dcms/page/c_edit?editor=text&id='.$pi->id.'\',\'\',\'width=900,height=500\'); void(0)" class="_text"></a>
					<a rel="tooltip" data-placement="bottom" data-original-title="Подсветка кода" href="javascript:window.open(\''. $url .'dcms/page/c_edit?editor=cm&id='.$pi->id.'\',\'\',\'width=900,height=500\'); void(0)" class="_code"></a>
					';
				if  ((strpos($pi->text, "<?")) || (strpos($pi->text, "?>"))) { }	 else {$out_content .= '<a rel="tooltip" data-placement="bottom" data-original-title="Визуальный редактор"href="javascript:window.open(\''. $url .'dcms/page/c_edit?editor=fck&id='.$pi->id.'\',\'\',\'width=900,height=500\'); void(0)" class="_edit"></a>'; }
				$out_content .= '</div>
			</div>
			<div class="_body">
				<p>'.substr(strip_tags($pi->text),0,1000).'</p>
			</div>
		</div>';
		
		return	$out_content; 
	}
	
	
	function page_edit_com($pi,$dcms){
		if ($com = $dcms->db->select("coms","id='{$pi->komp}'")){

		if ($datat = $dcms->db->select($com[0]->table_name ,"page_item='{$pi->id}'",'id')) $data_count = count($datat); else $data_count =0;
		$links='';
		if ($com_5data = $dcms->db->select($com[0]->table_name ,"page_item='{$pi->id}' AND name <> '' {$com[0]->query}   LIMIT 0, 20 ",'id,name')){
			foreach($com_5data as $d5){
				$links .= "<a href='{$dcms->url}dcms/page/edit_item?page={$_GET["page"]}&table={$com[0]->table_name}&pi={$pi->id}&com_id={$pi->komp}&iid={$d5->id}'>{$d5->name}</a><br />";
			}
		}
			
		return ' 
			<div class="pageitem">
				<div class="_head">
					<h3>
						<a href="'.$dcms->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$com[0]->table_name.'&pi='.$pi->id.'&com_id='.$pi->komp.'">'.$com[0]->name.' ('.$data_count.')</a>
					</h3>
						<div class="editors">
							<p style="    margin: 10px 30px;"> Компонент</p>
						</div>
				</div>
				<div class="_body">
					'.$links.'
				</div>
			</div>';
		}
		else return ' 
			<div class="pageitem">
				<div class="_head">
					<h3>
						<a>Компонент не найден id='.$pi->komp.'</a>
					</h3>
						<div class="editors"><p style="    margin: 10px 30px;"> Компонент</p></div>
				</div>
			</div>';
	}
	
	function page_not_found()
	{
		$out = '<div class="block"><h2>Страница не найдена</h2></div>';
		return $out;
	}
	
	function com_header($dcms)
	{
		return '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>DCMS Редактор контента</title>

	<link rel="stylesheet" type="text/css" href="'.$dcms->url.'dcms/style/rama.css" />
	<link rel="stylesheet" type="text/css" href="'.$dcms->url.'dcms/style/style.css" />
	<link rel="stylesheet" type="text/css" href="'.$dcms->url.'dcms/style/bootstrap.min.css" />
	<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300&subset=latin,cyrillic" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="'.$dcms->url.'dcms/js/jquery.js"></script>
	<script type="text/javascript" src="'.$dcms->url.'dcms/js/jquery.form.js"></script>
	<script type="text/javascript" src="'.$dcms->url.'dcms/js/jquery_ui.js"></script>
	<script type="text/javascript" src="'.$dcms->url.'dcms/js/bootstrap-tooltip.js"></script>
	<script type="text/javascript" src="'.$dcms->url.'dcms/js/bootstrap.min.js"></script>
	<script type="text/javascript">var surl = \''.$dcms->url.'\';</script>
	<script type="text/javascript" src="'.$dcms->url.'dcms/js/dcms.js"></script>

</head>
<body>
	<div class="layout">
		<div class="header">
			<div class="x960">
				<h2 style="padding: 0px 20px 11px 20px;" ><a style="height:0px; width: 200px" href="'.$dcms->url.'dcms/page?page='.$_GET["page"].'">К страницам</a></h2>
				<h2 style="padding: 0px 20px 11px 20px;" ><a style="height:0px; width: 200px"  href="'.$dcms->url.'dcms/page/c_show_com?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'">К списку</a></h2>
				<h2 style="padding: 0px 20px 11px 20px;" ><a style="height:0px; width: 200px"  href="'.$dcms->url.'dcms/page/c_add_form?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'">Добавить</a></h2>
			</div>
		</div>
		<div class="main">
			<div class="x960 com_editor">
				
				
				
				
				
		';
	}
	
	function com_header2($dcms,$cname=''){	
		$out = '';
		if ($this_page = $dcms->db->select("pages","id='".$_GET['page']."'")){	
			$endpath = $this_page[0]->path;
			$parent = $this_page[0]->parent;
			if ($this_page[0]->id!=1) {
				for(;;){
					$q = $dcms->db->select("pages","id='".$parent."'");
					if ($parent==1) break;
					$endpath = $q[0]->path.'/'.$endpath;
					$parent = $q[0]->parent;
				}
				$path_tp = $dcms->url.$endpath;
			} 
			else $path_tp = $dcms->url;				
			$out .=  '<h2><a target="_blank" href="'.$path_tp.'">'.$this_page[0]->name.'</a></h2>';	
		}
		if (isset($_GET['parent'])) {	
			if ($page_item = $dcms->db->select("pages_items","id='".$_GET["pi"]."'")){
				if ($com_1 = $dcms->db->select("coms","id='".$page_item[0]->komp."'")){
					$c1name = $com_1[0]->name;
				}
			}
			$sort_items ='';
			if ((isset($cname[0]->query)) && ($cname[0]->query  == 'ORDER BY d_sort ASC')) {
				$sort_items='<a href="javascript:window.open(\''.$dcms->url.'dcms/page/c_sort_ci?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&parent='.$_GET["parent"].'\',\'\',\'width=900,height=500\'); void(0)">Сортировать вручную</a>';
			}
			$out.='
			<div class="tools">
			<a href="'.$dcms->url.'dcms/page?page='.$_GET["page"].'">Страница</a>';
			$namei='';
			if ($page_item = $dcms->db->select("pages_items","id='".$_GET["pi"]."'")){
				if ($com_1 = $dcms->db->select("coms","id='".$page_item[0]->komp."'")){
					if ($item_name = $dcms->db->select($com_1[0]->table_name,"id='".$_GET['parent']."'")){
						$namei = $item_name[0]->name;
					}
					if($namei=='') $namei="Без названия";
					$out.= '<a href="'.$dcms->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$com_1[0]->table_name.'&pi='.$_GET["pi"].'&com_id='.$com_1[0]->id.'">'.$com_1[0]->name.'</a>';
					
					$out.= '<a href="'.$dcms->url.'dcms/page/edit_item?page='.$_GET["page"].'&table='.$com_1[0]->table_name.'&pi='.$_GET["pi"].'&com_id='.$com_1[0]->id.'&iid='.$_GET['parent'].'">'.$namei.'</a>';
				}
			}	
			$out.= '<a href="'.$dcms->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&parent='.$_GET["parent"].'">'.$cname[0]->name.'</a>
			<a href="'.$dcms->url.'dcms/page/add_form?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'&parent='.$_GET["parent"].'">Добавить </a>
			'.$sort_items.' 
			</div>
			';
		}  else {
			$sort_items ='';
			if ((isset($cname[0]->query)) && ($cname[0]->query  == 'ORDER BY d_sort ASC')) {
				$sort_items='<a href="javascript:window.open(\''.$dcms->url.'dcms/page/c_sort_ci?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'\',\'\',\'width=900,height=500\'); void(0)">Сортировать вручную</a>';
			}
			$out.='
			<div class="tools">
			<a href="'.$dcms->url.'dcms/page?page='.$_GET["page"].'">Страница</a>
			<a href="'.$dcms->url.'dcms/page/show_com?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'">'.$cname[0]->name.'</a>
			<a href="'.$dcms->url.'dcms/page/add_form?page='.$_GET["page"].'&table='.$_GET["table"].'&pi='.$_GET["pi"].'&com_id='.$_GET["com_id"].'">Добавить </a>
			'.$sort_items.'
			</div>
			';
		}	
		return $out;
	}
	
	function com_footer() 
	{
		
		return '
		</div>
		</div>
				
		</body>
		</html>';
	}
	
	function check_files($dcms)  
	{
		$out = '';
		$rulse_uploads = intval(substr(decoct(fileperms("../uploads")), 2, 6 ));
		
		
		
		
		
		//if ($rulse_uploads > 764) 	$out .= '<p><b>Внимание:</b>  Черезменрый доступ к /uploads '.$rulse_uploads.' (764)</p>'; 
		//if ($rulse_uploads < 764)  $out .= '<p><b>Внимание:</b>  Недостаточный доступ к /uploads '.$rulse_uploads.' (764)</p>'; 
		
		//if (substr(decoct(fileperms("../uploads")), 2, 6 ) != 777) 	$out .= '<p><b>Внимание:</b>  Необходимо предоставить права доступа к /uploads</p>'; 

		
		if (substr(decoct(fileperms("../dcms")), 2, 6 ) == 777) $out .= '<p><b>Внимание:</b> Необходимо ограничение прав доступа к /dcms</p>'; 
		if (substr(decoct(fileperms("../index.php")), 2, 6 ) == 777) $out .= '<p><b>Внимание:</b> Необходимо ограничение прав доступа к файлу index.php</p>'; 
		if (substr(decoct(fileperms("../dcms/config.php")), 2, 6 ) == 777) $out .= '<p><b>Внимание:</b> Необходимо ограничение прав доступа к файлу config.php</p>';
		if ($page_404 = $dcms->db->select("pages","name='404'")) {		return $out;	} 
		else $out .= '<p><b>Внимание:</b> Необходимо создать страницу 404</p>'; 
		
		return $out;
	}

}
?>