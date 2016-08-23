##ОПИСАНИЕ
Довольна часто лазью по вк. Он сделано очень удобным. И там есть одна очень интересная функция. При вставление ссылки в поле, автоматический определяет и выводит видео под поле, и при нажатие открывает это видео. Решил сделать эту функцию для DLE. Это модуль аналогична ищет ссылку, определяет ее и выводит видео под поле коммент. Для открывание видео использовал PhotoBox js.

##КАКИЕ ССЫЛКИ МОЖНО ДОБАВИТЬ ?

>Пока на данной версии, можно добавить ссылки - youtube и vk

1 .Ссылка с youtube
 - https://www.youtube.com/watch?v=kVhL5dEXVsE
 - https://www.youtube.com/embed/kVhL5dEXVsE
 
2 .Ссылка с vk
 - https://new.vk.com/video?z=video-93808317_456239919/pl_cat_ugc_popular
 - https://vk.com/video-93808317_456239919
 - https://vk.com/video_ext.php?oid=-93808317&id=456239919&hash=3f81f47619d75a8b&hd=2


##НА ЧИПСЫ :
 - Qiwi: +79994768647
 - WM: R246895222292 , Z869848337718 

##УСТАНОВКА

###ШАГ 1 								    
 
>Залейте все файлы на сервер 											    

    открыть файл main.tpl													    
 
найти это код перед <body> ...											    
  
    <link rel="stylesheet" href="/templates/{THEME}/photobox.css"> 
    <link href="/templates/{THEME}/css/svc.css" type="text/css" rel="stylesheet">
  
найти это код перед </body> ...					

    <script src="/templates/{THEME}/css/jquery.photobox.js"></script>
	  <script src="/templates/{THEME}/js/svc.js"></script>

###ШАГ 2 								    

     Открыть файл engine/classes/comments.claass.php							
  
найти ... 																	

	if( ! defined( 'DATALIFEENGINE' ) ) {
		die( "Hacking attempt!" );
	}  
 
вставить после																

	include (ENGINE_DIR . "/classes/hostsearch.class.php");
	$svc = new search_video_to_comments();  
 
найти ... 																	
  
    $news_author, $replace_links; 												
  
заменить ... 																
  
    $news_author, $replace_links, $svc;										
  
найти ... 																	
  
    $row['text'] = stripslashes( $row['text'] );								
  
ниже вставить ... 															

	$svc->comments = $row['text'];	
	$row['text'] = $svc->run( $row['text'] );
 
###ШАГ 3 								    

    Открыть файл engine/modules/bbcode.php										
  
найти ... 																	
  
    <textarea name="comments" id="comments" cols="70" rows="10" onfocus="setNewField(this.name, document.getElementById( 'dle-comments-form' ))">{text}</textarea>
  
  после вставить																

		<!-- SVC -->
		<div class="svc" id="svc"></div>
		<!-- /SVC -->
 

##ВАЖНО:
>НЕЛЬЗЯ вставить ссылки без http или https. Модуль не будет работать.

>В одном комменте можно добавить только одно видео.

##ЧТО БУДЕТ В НОВОЙ ВЕРСИИ
1 .Возможность добавления несколько видео

2 .Другие сайты (жду ваших предложений)

>Будут вопросы пишите в скайп - liser07
