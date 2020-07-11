//htmlspecialchars js
function escapeHtml(text) {
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}

//из высоты окна вычесть шапку нашу и шапку и подвал редактора 
// отсаток присвоить высотой контента
function resFCKwin(){
	console.log('resFCKwin');
	var chaight = $(window).height();
	chaight = chaight  - $("div.editors_head").height() - $("span.cke_top").height() - $("span.cke_bottom").height() ;
	chaight -=20;
	$("div.cke_contents").height(chaight); 
}

function resACEwin(){
	console.log('resACEwin');
	var chaightace = $(window).height();
	chaightace = chaightace  - $("div.editors_head").height(); 
	$('ace_item').height(chaightace);
	//console.log(chaightace);
	$("div#ace_page_code").height(chaightace);
	//$("div#ace_content").height(chaightace);
	//$("div.ace_layer").height(chaightace);
	//ace_page_code.resize();
}

$(document).ready(function(){	
	if( $('.editors textarea').hasClass('ckeditor')) {
		window.setInterval(resFCKwin,1000);
		$( window ).resize(resFCKwin);
	}
	
	if( $('#ace_page_code').hasClass('ace_item')) {
		$( window ).resize(function() {
			resACEwin();
		/*
			var chaightace = $(window).height();
			chaightace = chaightace  - $("div.editors_head").height(); 
			$("div#ace_page_code").height(chaightace);
			$('ace_item').height(chaightace);
			var all_height = ace_page_code.getSession().getDocument().getLength() * ace_page_code.renderer.lineHeight + ace_page_code.renderer.scrollBar.getWidth();
		*/	
			//
			//var all_height = ace_page_code.getSession().getDocument().getLength() * ace_page_code.renderer.lineHeight + ace_page_code.renderer.scrollBar.getWidth();
			//var all_height  = ace_page_code.getSession().getDocument().getLength() * ace_page_code.renderer.lineHeight + ace_page_code.renderer.scrollBar.getWidth();
			//console.log(ace_page_code);
			//$("div#ace_page_code").height(all_height);
		});
		resACEwin();
			
			
		
		
    }
	
	
	$(".title").click(function(){	
		resACEwin();
	});
});


//ctrs + S

// Функция для добавления обработчиков событий
function addHandler(object, event, handler, useCapture) {
    if (object.addEventListener)
        object.addEventListener(event, handler, useCapture);
    else if (object.attachEvent)
        object.attachEvent('on' + event, handler);
    else object['on' + event] = handler;
}

// Определяем браузеры
var ua = navigator.userAgent.toLowerCase();
var isIE = (ua.indexOf("msie") != -1 && ua.indexOf("opera") == -1);
var isSafari = ua.indexOf("safari") != -1;
var isGecko = (ua.indexOf("gecko") != -1 && !isSafari);

// Добавляем обработчики
if (isIE || isSafari) addHandler (document, "keydown", hotSave);
else addHandler (document, "keypress", hotSave);

function hotSave(evt) {
    // Получаем объект event
    evt = evt || window.event;
    var key = evt.keyCode || evt.which;
    // Определяем нажатие Ctrl+S
    key = !isGecko ? (key == 83 ? 1 : 0) : (key == 115 ? 1 : 0);
    if (evt.ctrlKey && key) {
        // Блокируем появление диалога о сохранении
        if(evt.preventDefault) evt.preventDefault();
        evt.returnValue = false;
        // Запускаем любую функцию, по желанию
        clientFunction();
        // Возвращаем фокус в окно
        window.focus();
        return false;
    }
}

function clientFunction() {
	if (typeof ready_data == 'function') { 
		if (ready_data()) $(".right_submit").click();
	} else {
		$(".right_submit").click();
	}

	
}

function save_complite(responseText, statusText, xhr, $form){
	console.log(responseText);
	if (responseText ==="ok"){
		$(".notifications > span").first().before('<span class="_ok"></i>Сохранено</span>');
		$(".notifications > span").first().css({"backgroundColor": "#41CCFF  ", "position": "fixed"});
		var t =  window.setTimeout('$(".notifications > span").hide();', 700);
	} else {
		$(".notifications > span").first().before('<span class="_error"></i>Ошибка</span>');
		$(".notifications > span").first().css({"backgroundColor": "#FF0000  ", "position": "fixed"});
		var t =  window.setTimeout('$(".notifications > span").hide();', 700);
	}	
}

  var aFoptions = { 
       // http://malsup.com/jquery/form/#ajaxForm
        success:       save_complite  // post-submit callback 
    }; 
	
$(".editform").ajaxForm(aFoptions);

function insert_ace_text(inset_text){
	var editor = document.querySelector(".ace_editor").env.editor;
	var cursor = editor.selection.getCursor() // returns object like {row:1 , column: 4}
	editor.insert(inset_text) // insert string at cursor
}
	
