//================================================
 // SVC модуль для DLE
 //-----------------------------------------------
 // Автор: oxxxiss aka LisER
 //-----------------------------------------------
 // Почта: osimi98@yandex.ru
 //-----------------------------------------------
 // skype: liser07
 //-----------------------------------------------
 // Назначение: Анимации и AJAX запросы хака
//================================================

	
	function remove_video(a){
		$("#addcomment-video").remove();
		textarea = $("#comments").val().replace(""+a+"" , "");
		$("#comments").val(textarea);
	}
	
	document.onkeyup = function (e) {
		e = e || window.event;
		if (e.keyCode === 86 && e.ctrlKey) {			
			var textarea =	$("#comments").val();
				$.ajax({
					 type: "POST",
					 url: "/engine/ajax/search_comments.php",
					 data: ({ comments: textarea}),
					 dataType: "html",
					 beforeSend: function ()
					 {
						 ShowLoading("");
					 },
					 success: function(data)
					 {
						$("#svc").html(data);
						HideLoading("");
					 }
				});
					
		}
		return false;
	}

	
	$('nav.playvideo').click(function (){
		$('nav.playvideo').photobox('a', {thumbs:true, loop:true });
		return false;
	});