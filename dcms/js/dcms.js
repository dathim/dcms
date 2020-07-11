/*
DCMS 4
*/

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function save_ajax(element) { 
	$(element).parents("form.rcform").submit();
}

function escapeHtml(text) {
  return text
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
}

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
$(document).ready(function(){

	/* PAGES*/
	// Дерево старниц
	$("._close").click(function() {
		var pid = $(this).attr("pid");
		if ($(this).attr("class") == "_close _closed")
		{
			//alert('открыть');
			$(this).next().next().next().show();
			$(this).removeClass("_closed");
			$(this).addClass("_opened");
			$.get(surl+"dcms/page/c_menu_show?new="+pid);
			//alert(pid);
			
		}
		else
		{
			//alert('Закрыть');
			$(this).next().next().next().hide();
			$(this).removeClass("_opened");
			$(this).addClass("_closed");
			$.get(surl+"dcms/page/c_menu_hide?new="+pid);
		}		
	});

	// Перезагрузка при смене названия и пути страницы
	var reload = false;
	$(".must_reload").change(function() {
		reload = true;
		console.log('reload');
	});

	
	//настройка страницы
	$( ".togle_ext" ).click(function() {
		  $( this ).parent().next().toggleClass( "hide" );
	});
	
	
	//Таблички сохранено
		
	$("._master").click(function() {
		$(".rcform").submit();
	});
	$(".rcform").ajaxForm(function() {
		var h = new Date();
		//var  time_save = h.getHours() +':'+ h.getMinutes() +':'+ h.getSeconds();	
		$(".notifications > span").first().before('<span class="_ok"></i>Сохранено </span>');
		$(".notifications > span").first().css({"backgroundColor": "#41CCFF  ", "position": "fixed"});
		var t =  window.setTimeout('$(".notifications > span").first().css({"backgroundColor" : "#FFF", "position": "relative"});', 1500);
		if (reload) { location.reload(); }
   });
   
	/* END PAGE */
	
	$("._mastername").click(function() {
		$(this).parents('form').submit();
	});
	
	$(".alert").css("display","none");	
	
   
	
	//fast edit
	$(".edit_pm").click(function() {
	$(".edit_select").hide();
	var pid = $(this).attr('name');
    $(this).siblings(".edit_select").show();
	 	$(this).siblings(".edit_select").fadeTo("fast",1);
	   $(this).siblings(".edit_select").load(surl+"dcms/pages/c_get_link?id="+pid);
		$(this).siblings(".edit_select").delay(2300).hide("fast");
	});
	
	$(".col > input[type=checkbox]").click(function() {
		if ($(this).is(":checked")) {
				$(this).parents(".row").removeClass('fon_red').addClass('fon_gren');
			}	
		else {
				$(this).parents(".row").removeClass('fon_gren').addClass('fon_red');
			}
	});
	
	
	$("span.active").parents("ul").show();

   //filemanager
   $("a.fobject").click(function() {
		
		var className = $(this).attr('class');
		if (className == "fobject opened") { 
			$(this).removeClass('opened');
			$(this).next().hide();
			$(this).parent().parent("tr").removeClass('context');
		} else {
			//
			$(".opened").removeClass('opened');
			$(".context").removeClass('context');
			$("._name span").hide();
			
			$(this).addClass('opened');
			$(this).next().show();
			$(this).parent().parent("tr").addClass('context');
			
			
		}
		
		console.log('2'); 
   });
   
   //tooltip
	$("[rel='tooltip']").tooltip();
	//console.log('tt');
	
	//select img
	function GetUrlParam( paramName )
	{
		var oRegex = new RegExp( '[\?&]' + paramName + '=([^&]+)', 'i' ) ;
		var oMatch = oRegex.exec( window.top.location.search ) ;

		if ( oMatch && oMatch.length > 1 )
		return decodeURIComponent( oMatch[1] ) ;
		else
		oRegex = new RegExp( '&' + paramName + '=([^&]+)', 'i' ) ;
		oMatch = oRegex.exec( window.top.location.search ) ;
		if ( oMatch && oMatch.length > 1 )
		return decodeURIComponent( oMatch[1] ) ;
		else {
		return '';
		}
	}

	//встроить файл
	$("._finsert").click(function() {
		var fileUrl = $(this).attr('href');
		funcNum = GetUrlParam('CKEditorFuncNum') ;
		window.top.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl);
		window.top.close() ;
		window.top.opener.focus() ; 
	});
	
	// Вывод ошибок http://test5.ru/dcms/page/c_error_list?error=1
	var error_code = getUrlParameter('error');
	if (error_code){
		$.get(surl+'dcms/page/c_error_list?error='+error_code, function (data) {	
			$(".notifications > span").first().before('<span class="_warning"></i>'+data+'</span>');
			$(".notifications > span").first().css({"backgroundColor": "#FFBB0E  ", "position": "fixed"});
			var t =  window.setTimeout('$(".notifications > span").first().css({"backgroundColor" : "#FFF", "position": "relative"});', 1500);
		});
	}
	
	//выделить все
	$(".select_all").click(function() {
		console.log('select_all');
		if ($('.select_all').is(':checked')){
			$(".all_items").attr('checked', true); // Deprecated
			$(".all_items").prop('checked', true);
		} else {
			$(".all_items").attr('checked', false); // Deprecated
			$(".all_items").prop('checked', false);
		}
	});
});
/*----------------------------------------------------------------------------------------------------------*/

//sortable maket

function nsave(){

var time = new Date(),
		h = time.getHours(), // 0-24 format
		m = time.getMinutes();
		$(".notifications > span").first().before('<span class="_ok"><i>'+h+':'+m+'</i>Сохранено</span>');
		$(".notifications > span").first().css({backgroundColor: "#EFE"});
		var t =  window.setTimeout('$(".notifications > span").first().css({backgroundColor: "#FFF"});', 500);
}




function go(Url) {
	if (confirm('Внимание:Подтвердите действие.')) {
		 top.location=Url
		}
}

function insertTab(o, e)
	{
		var kC = e.keyCode ? e.keyCode : e.charCode ? e.charCode : e.which;
		if (kC == 9 && !e.shiftKey && !e.ctrlKey && !e.altKey)
		{
			var oS = o.scrollTop;
			if (o.setSelectionRange)
			{
				var sS = o.selectionStart;
				var sE = o.selectionEnd;
				o.value = o.value.substring(0, sS) + "\t" + o.value.substr(sE);
					o.setSelectionRange(sS + 1, sS + 1);
				o.focus();
			}
			else if (o.createTextRange)
			{
				document.selection.createRange().text = "\t";
				e.returnValue = false;
			}
			o.scrollTop = oS;
			if (e.preventDefault)
			{
				e.preventDefault();
			}
			return false;
		}
		return true;
	}

//ctrs + S
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
		console.log('save');
        clientFunction();
        // Возвращаем фокус в окно
        window.focus();
        return false;
    }
}

function clientFunction() {
	if (typeof ready_data == 'function') { 
		if (ready_data()) $(".rcform").submit();
	} else {
		$(".rcform").submit();
	}
}

/*
function placet_img(path){
 var return_value = path;

   opener.document.getElementById("cke_108_textInput").value = return_value;
    window.close();

}


	$( "div.column li a" ).hover(
	function(){
		$(this).parent().find('ul li a').css({ 'background-color':'transparent' });
	}
);

$( "a.add_pm" ).hover(
	function(){
		$(this).parent().find('a:not(".add_pm")').css({ 'background-color':'#666' });
		$(this).parent().find('ul li a').css({ 'background-color':'transparent' });
	},
	function(){
		$(this).parent().find('a').css({ 'background-color':'transparent' });
	}
);

	*/

	
