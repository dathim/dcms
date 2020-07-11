<?php 
// dcms/modules/files/files.php


Class files {

	function constructor()
	{
		$dir= '../';
		if (isset($_GET['path'])) {
			if (substr($_GET['path'],-1,1) != '/')	$dir = $_GET['path'].'/';
			else $dir = $_GET['path'];
		}
		echo '<div class="tree files_bar ucol">
		<h2>Загрузить новые файлы</h2>
		<form action="'.$this->url.'dcms/files/c_upload?placte='.$dir.'" method="POST" enctype="multipart/form-data" >
			<input id="file" type="file" name="nfiles[]" multiple />
			<p><input type="submit" value="Загрузить" /></p>
			
		';
			
		
		echo '</form>';
		
		echo '
		<h2>Создать папку</h2>
			<form action="'.$this->url.'dcms/files/c_newdir?placte='.$dir.'" method="POST" enctype="multipart/form-data" >
				<p><input type="text" name="fname" /></p>
				<p><input type="submit" value="Создать" /></p>
			</form>
		';
			
		
		$dir= '../';
		if (isset($_GET['path'])) {
			if (substr($_GET['path'],-1,1) != '/')	$dir = $_GET['path'].'/';
		    else $dir = $_GET['path'];
		}
	
		echo '</div>';
		echo '<div class="content ucol"><div class="block">';
	}
	
	function destructor() 
	{
		echo '</div></div>';
	}
	
	
	function index()
	{
	
		function p_img($url,$dir,$file,$newpath){
		
			$resize='';
			if (@filesize($dir.$file) > 300000) $resize =" [Рекомендуется сжать]";
			$indert_img='';
			if (isset($_GET['CKEditor'])){ $indert_img = "<a style='font-weight: bold;' class='_finsert' href='{$url}{$newpath}{$file}'>Вставить</a>"; }
			if (isset($_GET['path'])) $path_for_img = $_GET['path']; else $path_for_img = '../uploads/';
			echo "<tr>
				<td class='_icon'><a href='{$url}{$newpath}{$file}' style='background-image:url({$url}{$newpath}{$file});'></a>	
				</td>
				<td class='_name'>
					<a class='fobject' href='javascript:void(0)'>{$file} {$resize}</a>				

					<span>
						<a href='{$url}{$newpath}{$file}'>Открыть</a>
						{$indert_img}
						<a href='{$url}dcms/files/c_f_img_resize?file={$path_for_img}{$file}&placte={$path_for_img}&resx=250&resy=150'>Сжать до 250x150</a>
						<a href='{$url}dcms/files/c_f_img_resize?file={$path_for_img}{$file}&placte={$path_for_img}&resx=800&resy=600'>Сжать до 800x600</a>
						<a href='{$url}dcms/files/c_f_img_resize?file={$path_for_img}{$file}&placte={$path_for_img}&resx=1024&resy=768'>Сжать до 1024x768</a>
						<a href='{$url}dcms/files/c_f_img_resize?file={$path_for_img}{$file}&placte={$path_for_img}&resx=1920&resy=1080'>Сжать до 1920x1080</a>
						<a href='{$url}dcms/files/c_f_del?df={$dir}{$file}&path={$dir}'>Удалить</a>
					</span>
				</td>
				<td>". round(@filesize($dir.$file)/1048576,3)." МБ</td>
				<td>".substr ( decoct ( @fileperms ( $dir.$file ) ), 2, 6 )."</td>
				<td>	<a href='{$url}dcms/files/c_f_del?df={$dir}{$file}&path={$dir}'>Удалить</a></td>
			</tr>";
		}
		
		function p_file($url,$dir,$file,$ico,$newpath,$edit=false){
			$edit_link='';
			if ($edit) $edit_link='<a href="javascript:window.open(\''.$url.'dcms/files/c_edit_file?p='.$dir.$file.'\',\'\',\'width=900,height=500\'); void(0)">Редактировать</a>';			
			echo "	<tr>
					<td class='_icon'><a href='javascript:void(0)' style='background-image:url({$url}dcms/modules/files/icons/{$ico});'></a>	
					</td>
					<td class='_name'>
						<a class='fobject' href='javascript:void(0)'>{$file}</a>				
	
						<span>
							
							<a href='{$url}{$newpath}{$file}' target='_blank'>Загрузить</a>
							{$edit_link}
							<a href='{$url}dcms/files/c_f_del?df={$dir}{$file}&path={$dir}'>Удалить</a>
						</span>
					</td>
				<td>". round(filesize($dir.$file)/1048576,3)." МБ</td>
				<td>".substr ( decoct ( @fileperms ( $dir.$file ) ), 2, 6 )."</td>
				<td><a href='{$url}dcms/files/c_f_del?df={$dir}{$file}&path={$dir}'>Удалить</a></td>
				</tr>";
		} 
		
		$dir= '../';
	
		
		if (isset($_GET['path'])) {
			if (substr($_GET['path'],-1,1) != '/')	$dir = $_GET['path'].'/';
			else $dir = $_GET['path'];
		}
		/*links*/
		$path_arr = explode('/', $dir);
		$links = '';
		$path_old = '../';
		foreach($path_arr as $p)
		{
			if ($p == '..') continue;
			if ($p == '') continue;
			$path_old .= $p.'/';
			$links .='<a href="'.$this->url.'dcms/files?path='.$path_old.'" >'.$path_old.'</a>';
			
		}

		
		
		echo '<h2>Менеджер файлов </h2>
		<div class="filemanager">
			<div class="_path">
				<a  href="'.$this->url.'dcms/files">Корень сайта</a>'.$links.'
			</div>
			<table>
				<tr>
					<th></th>
					<th>Название файла</th>
					<th>Размер</th>
					<th>Права</th>
					<th>Действие</th>
				</tr>
		';
		
		if ($handle = opendir($dir)) {
			
			while (false !== ($file = readdir($handle))) { 
			
				if ($file == '..') continue;
				if ($file == '.') continue;
				if ($file == '.htaccess') continue;
				if ($file == 'index.php') continue;
				if ($file == 'dcms') continue;
				if (is_dir($dir.$file)) { $alldir[] = $file; }
				else					{ $allfile[] = $file;}
			}
		
			if (@asort($alldir)){
				
				foreach($alldir as $file){
					$submenu = "";
					$alert="!";
					$addfck = '';
					if (isset($_GET['CKEditor'])){ $addfck = "&CKEditor=editor1&CKEditorFuncNum=3&langCode=ru";	}
						$submenu = "
							<a class='fobject'  href='javascript:void(0)'><img src='style/icons2/lists.png' alt='pr'/></a>
								<span>
									<a href='?path={$dir}{$file}{$addfck}/'>Открыть</a>
									<a href='{$this->url}dcms/files/c_dir_del?df={$dir}{$file}&path={$dir}'>Удалить</a>
								</span>";
						$alert ='';
					echo "
					<tr>
						<td class='_icon'>
							<a href='?path={$dir}{$file}{$addfck}/' style='background-image:url({$this->url}dcms/modules/files/icons/folder.png);'></a>	
						</td>
						<td class='_name'>
							<a href='?path={$dir}{$file}{$addfck}/'>{$file} {$alert}</a>				
						</td>
						<td>dir</td>
						<td>".substr ( decoct ( @fileperms ( $dir.$file ) ), 2, 6 )."</td>
					
						<td>
							{$submenu}
						</td>
					</tr>
					
					";
				}
			}	
			if (@asort($allfile)){
				foreach($allfile as $file){
					$type = substr(strrchr($file, '.'), 1);
					$newpath = substr_replace($dir,'','', 3); 
					switch (strtolower($type)) {
						case 'html': p_file($this->url,$dir,$file,'html.png',$newpath,1); break;
						case 'htm':  p_file($this->url,$dir,$file,'html.png',$newpath,1); break;
						case 'xml':  p_file($this->url,$dir,$file,'html.png',$newpath,1); break;
						case 'php':  p_file($this->url,$dir,$file,'html.png',$newpath,1); break;
						case 'sql':  p_file($this->url,$dir,$file,'html.png',$newpath,1); break;
						case 'css':  p_file($this->url,$dir,$file,'css.png', $newpath,1);  break;
						case 'txt':  p_file($this->url,$dir,$file,'html.png', $newpath,1);  break;
						
						case 'doc':  p_file($this->url,$dir,$file,'Word.png',$newpath); break;
						case 'docx': p_file($this->url,$dir,$file,'Word.png',$newpath); break;
						case 'xls':  p_file($this->url,$dir,$file,'xls.png',$newpath);  break;
						case 'xlsx': p_file($this->url,$dir,$file,'xls.png',$newpath);  break;
						case 'zip':  p_file($this->url,$dir,$file,'zip.png',$newpath);  break;
						case 'rar':  p_file($this->url,$dir,$file,'rar.png',$newpath);  break;
						case 'pdf':  p_file($this->url,$dir,$file,'pdf.png',$newpath);  break;
						
						case 'png':  p_img($this->url,$dir,$file,$newpath); break;
						case 'jpg':  p_img($this->url,$dir,$file,$newpath); break;
						case 'jpeg': p_img($this->url,$dir,$file,$newpath); break;
						case 'bmp': p_img($this->url,$dir,$file,$newpath); break;
						case 'tif': p_img($this->url,$dir,$file,$newpath); break;
						
						default: p_file($this->url,$dir,$file,'file-broken.png',$newpath);   break;
					}
				}
			}
		}
		
		echo '
			</table>
		</div>';
	}
}

Class clear{

	function c_upload_fck() 
	{
		//upload
		$dir= '../uploads/'; 
		$callback = $_REQUEST['CKEditorFuncNum'];
		$new_file_name = date("m_Y").'_'.$this->func->ru_en_translite($_FILES['upload']['name']);
		$path_img = $this->url.'uploads/'.$new_file_name;
		$size = intval($_FILES['upload']['size']);
		if ($size < 300000){
			if (copy($_FILES['upload']['tmp_name'], $dir . $new_file_name)){
				echo "<html><body><script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction('{$callback}','{$path_img}');</script></body></html>";
			}
		}
		if (($size >= 300000) && ($size <= 15000000)){
			if (copy($_FILES['upload']['tmp_name'], $dir . $new_file_name)){
				include("img_res.php");
				img_resize($dir . $new_file_name, $dir . $new_file_name, 800, 800,  95, 0xFFFFF0, 0);
				echo "<html><body><script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction('{$callback}','{$path_img}');</script></body></html>";
			}
		}
		if ($size > 15000000)  { 	echo "<html><body><script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction('{$callback}','','Файл большого размера! разм.-{$size}');</script></body></html>"; }
	}
	
	
	function c_upload(){
	
		$dir= '../'; 
		if (isset($_GET['placte'])) {
			if (substr($_GET['placte'],-1,1) != '/')	$dir = $_GET['placte'].'/';
			else $dir = $_GET['placte'];
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
		$all_f = arrayImages($_FILES['nfiles']);
		
		
		foreach($all_f as $fup){
			$new_file_name = date("m_Y").'_'.$this->func->ru_en_translite($fup['name']);
			if (copy($fup['tmp_name'], $dir . $new_file_name)){			
				header('Location: '.$this->url.'dcms/files?path='.$_GET['placte']);
			}
		}
		header('Location: '.$this->url.'dcms/files?path='.$_GET['placte']);
		
	}
	
	function c_newdir(){
		$user_name = get_current_user();
		//echo $user_name ;
		$dir= '../';
		if (isset($_GET['placte'])) $dir= $_GET['placte'].'/';
		
		if (@mkdir($dir . $_POST['fname'], 0777 ,true)){	
			chown($dir . $_POST['fname'], $user_name);
			$stat = stat($dir . $_POST['fname']);
			//print_r(posix_getpwuid($stat['uid']));
			//echo 'OK';
		}
		else { echo "Нет возможности для создания каталога"; }
		header('Location: '.$this->url.'dcms/files?path='.$_GET['placte']);
	}
	
	function c_f_img_resize(){
		include("img_res.php");
		img_resize($_GET['file'], $_GET['file'], $_GET['resx'], $_GET['resy'],  95, 0xFFFFF0, 0);
		header('Location: '.$this->url.'dcms/files?path='.$_GET['placte']);
	}	
	
	function c_f_del(){
		if (isset($_GET['df'])) $dir= $_GET['df'];
		if (unlink($dir)) header('Location: '.$this->url.'dcms/files?path='.$_GET['path']);
		else "can't del";
	}	
	
	
	function c_removeDirectory($dir) {
    if ($objs = glob($dir."/*")) {
       foreach($objs as $obj) {
         is_dir($obj) ? $this->c_removeDirectory($obj) : unlink($obj);
       }
    }
    rmdir($dir);
	}
	
	function c_dir_del(){
		if (isset($_GET['df'])) $dir= $_GET['df'];
		if (@rmdir($dir)) { header('Location: '.$this->url.'dcms/files?path='.$_GET['path']); }
		else{
			$this->c_removeDirectory($dir);
			header('Location: '.$this->url.'dcms/files?path='.$_GET['path']);
		}
	}
	
	function c_edit_file(){
	
	echo '
			<html>
				<head>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery.form.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/jquery-ui-1.8.14.custom.min.js"></script>
					<script type="text/javascript" src="'.$this->url.'dcms/js/minibox.js"></script>
					
					<title>'.$_GET["p"].'</title>

					<script>
							 
							function is_ready_data(){
								if (typeof ready_data == "function") { 
									if (ready_data()) $(".rcform").submit();
								}
							}
						</script>	
					
					
					<link rel="stylesheet" type="text/css" href="'.$this->url.'dcms/style/editors.css" />
					</head><body><div class="editors_head"><div style="    font-size: 13px;    padding: 0 30px;    color: #FFB800;">';
					
					if ((isset($_POST['text'])) && ($_POST['text'] != '')) {
						file_put_contents($_GET['p'], $_POST['text']);
					}
					
					
					
					echo '</div>
					
						<form  class="myForm" action="'. $this->url .'dcms/files/c_edit_file?p='.$_GET['p'].'" method="POST">
						<h2 class="title">'.$_GET["p"] .'<i>chmod:'.substr ( decoct ( @fileperms ( $_GET['p'] ) ), 2, 6 ).'</i></h2>
						<input class="right_submit" type="submit" name="" onClick="is_ready_data();" value="Сохранить">
					</div>
					<div class="editors">
				';
				
				$text = file_get_contents($_GET["p"]);
				
				echo '
						<style type="text/css">
						div.CodeMirror-scroll{height:100% !important; }
						</style>
						<textarea id="ace_code" name="text"  onkeydown="insertTab(this, event);" style="display:none;" >'.htmlspecialchars($text).'</textarea>
						<div class="ace_item" id="ace_page_code" style="   margin-top: 3px;">'.htmlspecialchars($text).'</div> 	
						<script src="'. $this->url.'dcms/plugins/ace/ace.js" type="text/javascript" charset="utf-8"></script>
							<script>
							 var ace_page_code = ace.edit("ace_page_code");
							 ace_page_code.setTheme("ace/theme/eclipse");
							 ace_page_code.getSession().setMode("ace/mode/php");
							
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
						';
						
						
				echo '</form>
				</div>
				</body>
				</html>';
	}

}