<?php
/*
=====================================================
 SVC модуль для DLE
-----------------------------------------------------
 Сайт: http://lis-er.ru/
-----------------------------------------------------
 Skype: liser07
=====================================================
 Файл: hostsearch.class.php
-----------------------------------------------------
 Назначение: Функции модуля
=====================================================
*/

  // =========================================================================== ++
  // 				               УСТАНОВКА                                     ||
  // =========================================================================== ++
  //							 	  ШАГ 1 								     ||
  //============================================================================ ++ 
  // Залейте все файлы на сервер 											     ||
  //============================================================================ ++
  // открыть файл main.tpl													     ||
  //-----------------------------------------------------------------------------++
  // найти это код перед <body> ...											     ||
  //---------------------------------------------------------------------------- ++
  /* <link rel="stylesheet" href="/templates/{THEME}/photobox.css"> 
     <link href="/templates/{THEME}/css/svc.css" type="text/css" rel="stylesheet">*/
  //-----------------------------------------------------------------------------++  
  // найти это код перед </body> ...											 ||
  //---------------------------------------------------------------------------- ++
  /* 
     <script src="/templates/{THEME}/css/jquery.photobox.js"></script>
	 <script src="/templates/{THEME}/js/svc.js"></script>
  */
  //-----------------------------------------------------------------------------++  
  // =========================================================================== ++
  //							 	  ШАГ 2 								     ||
  //============================================================================ ++
  // Открыть файл engine/classes/comments.claass.php							 ||
  //---------------------------------------------------------------------------- ++
  // найти ... 																	 ||
  /*---------------------------------------------------------------------------- ++
	if( ! defined( 'DATALIFEENGINE' ) ) {
		die( "Hacking attempt!" );
	}  
  *///-------------------------------------------------------------------------- ++
  // вставить после																 ||
  /*---------------------------------------------------------------------------- ++
	include (ENGINE_DIR . "/classes/hostsearch.class.php");
	$svc = new search_video_to_comments();  
  *///-------------------------------------------------------------------------- ++
  //---------------------------------------------------------------------------- ++
  // найти ... 																	 ||
  //---------------------------------------------------------------------------- ++
  // $news_author, $replace_links; 												 ||
  //---------------------------------------------------------------------------- ++
  // заменить ... 																 ||
  //---------------------------------------------------------------------------- ++
  // $news_author, $replace_links, $svc;										 ||
  //---------------------------------------------------------------------------- ++
  // найти ... 																	 ||
  //---------------------------------------------------------------------------- ++
  // $row['text'] = stripslashes( $row['text'] );								 ||
  //---------------------------------------------------------------------------- ++
  // ниже вставить ... 															 ||
  /*---------------------------------------------------------------------------- ++
	$svc->comments = $row['text'];	
	$row['text'] = $svc->run( $row['text'] );
  *///-------------------------------------------------------------------------- ++
  // =========================================================================== ++
  //							 	  ШАГ 3 								     ||
  //============================================================================ ++
  // Открыть файл engine/modules/bbcode.php										 ||
  //---------------------------------------------------------------------------- ++
  // найти ... 																	 ||
  //---------------------------------------------------------------------------- ++
  //<textarea name="comments" id="comments" cols="70" rows="10" onfocus="setNewField(this.name, document.getElementById( 'dle-comments-form' ))">{text}</textarea>
  //---------------------------------------------------------------------------- ++
  // после вставить																 ||
  /*---------------------------------------------------------------------------- ++
		<!-- SVC -->
		<div class="svc" id="svc"></div>
		<!-- /SVC -->
  *///-------------------------------------------------------------------------- ++

  
	class search_video_to_comments
	{	
		var $authorized_hosts = array();			
		var $comments 		  = '';	
		var $vk_token 		  = '';	
		
		function __construct()
		{
				global $db, $row, $config; 
				
				$this->db       = $db;     // Стандартная функция dle для отправлении запроса
				$this->row      = $row;	   // Массив с данными
				$this->config   = $config; // Конфигурации DLE
				$this->_POST    = $_POST;  // Посты
				$this->_GET     = $_GET;   // GET
				
				@header('Content-Type: text/html; charset=' . $this->configp[charset]);
				
				$this->status   = false;
				
				if ( isset ( $this->_POST[comments] ) AND $this->_POST[comments] != "" )
					$this->comments = $this->_POST[comments];
				
				$this->authorized_hosts = array("www.youtube.com", "youtube.com", "new.vk.com", "vk.com");
				$this->vk_token = "";
		}
		
		private function curl($url){
			$uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";				
			$ch = curl_init($url);			
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, $uagent);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,1);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		}
			
		private function search_url_and_host()
		{
			$this->comments = trim ( $this->comments );
			$this->comments = htmlentities ( $this->comments );
			
			if( preg_match_all("#(https|http)://\S+[^\s.,>)\];'\"!?]#", $this->comments, $this->comments ) )
			{
				$this->url = $this->comments[0][0];
				$this->url = trim ( $this->url );
				$this->url = htmlentities ( $this->url );
				$this->host = parse_url ( $this->url ); 
				$this->host = $this->host[host];
				$this->_count = count ($this->authorized_hosts);
				
				for($i = 0; $i <= $this->_count; $i++)
				{
					if( $this->authorized_hosts[$i] == $this->host)
					{
						$this->status = true;
						break;
					}	
				}
				
				if ( $this->status == true )
				{				
					$this->result = array (
						'url'    => $this->url,
						'host'   => $this->host,
						'status' => $this->status,
					);
				
				}
				
				return $this->result;				
			}	
			
		}		
			
		private function youtube ($youtube)
		{
			if( ! preg_match_all("#/embed/(.+?)#Uis", $youtube, $this->youtube ) )
			{
				preg_match_all("#v=(.+?)#Uis", $youtube, $this->youtube); 
			}
								
			$this->youtube_id = $this->youtube[1][0];		
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://www.youtube.com/get_video_info?video_id='. $this->youtube_id);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$links = curl_exec ($ch);
			curl_close ($ch);
			
			parse_str($links, $this->info);		
						
		}
		
		private function vk ($vk)
		{
			
			if( ! preg_match_all("#https://vk.com/video(.+?)#Uis", $vk, $this->vk ) )
			{	
				if( ! preg_match_all("#z=video(.+?)%#is", $vk, $this->vk ) )
				{
					
					$this->vk = parse_url($vk);
					preg_match_all("#oid=(.+?)id=(.+?)hash#Uis", $this->vk[query] , $this->vk );
					$this->vk = $this->vk[1][0] . "_" . $this->vk[2][0];
					$this->vk = str_replace("&amp;amp;","",$this->vk);
				
				}
				else				
					$this->vk = $this->vk[1][0];
				
			}		
			else				
					$this->vk = $this->vk[1][0];
						
			$this->vk_url = array (
				"videos"	   => $this->vk,
				"access_token" => $this->vk_token,					
			);
			
			$this->vk_url = http_build_query($this->vk_url);			
			$this->method = "video.get";			
			$this->url_to_open = "https://api.vk.com/method/" . $this->method . "?". $this->vk_url;	
			
			$this->open = $this->curl( $this->url_to_open );
			$this->open = json_decode( $this->open , true );		
			
			$this->info = array(
				"poster"  => $this->open[response][1][image_medium],
				"title"   => $this->open[response][1][title],
				"player"  => $this->open[response][1][player]
			);
			
		}
		
		public function run_ajax ()
		{
				$this->search_url_and_host();
				
				if($this->result['host'] == "www.youtube.com" || $this->result['host'] == "youtube")
				{
					$this->youtube($this->result['url']);
					$this->info[poster] = $this->info[iurlmq]; 
					$this->url = "https://www.youtube.com/embed/". $this->youtube_id;
					
				}
				else
				if($this->result['host'] == "new.vk.com" || $this->result['host'] == "vk.com")	
				{
					$this->vk($this->result['url']);
					$this->url = $this->info[player];	
				}
				
				if ( $this->status == true )
				{
					$this->tpl  = "<script>$('.circle').photobox('a', {thumbs:true, loop:true }, callback);</script>";
					$this->tpl .= '<div class="video-svc-block" id="addcomment-video">';
					$this->tpl .= '<div class="video-svc">';
					$this->tpl .= '<nav id="close" onclick="remove_video(\''.$this->result[url].'\'); return false;">X</nav>';
					$this->tpl .= '<nav id="circle" class="circle">';
					$this->tpl .= '<a href="' . $this->url . '" rel="video">';
					$this->tpl .= '<nav id="play"></nav>';
					$this->tpl .= '</a></nav>';
					$this->tpl .= '<nav id="poster"><img src="' . $this->info[poster] . '" style="float:left;" alt="' . $this->info[title] . '" title="' . $this->info[title] . '"></nav>';
					$this->tpl .= '<nav id="title">' . $this->info[title] . '</nav>';
					$this->tpl .= '</div>';
					$this->tpl .= '</div>';
				}
				
				else
					
				{
					$this->tpl = ""; 
				}
				
				echo $this->tpl;
				
		}
		
		public function run ($text)
		{
			
			$this->search_url_and_host();
			
			if($this->result['host'] == "www.youtube.com" || $this->result['host'] == "youtube")
			{
				$this->youtube($this->result['url']);
				$this->info[poster] = $this->info[iurlmq]; 
				$this->url = "https://www.youtube.com/embed/". $this->youtube_id;
				
			}
			else
			if($this->result['host'] == "new.vk.com" || $this->result['host'] == "vk.com")	
			{
				$this->vk($this->result['url']);
				$this->url = $this->info[player];	
			}
			
			if ( $this->status == true )
			{
				$this->tpl .= '<div class="video-svc-block">';
				$this->tpl .= '<div class="video-svc">';
				$this->tpl .= '<nav id="circle" class="playvideo">';
				$this->tpl .= '<a href="' . $this->url . '" rel="video">';
				$this->tpl .= '<nav id="play"></nav>';
				$this->tpl .= '</a></nav>';
				$this->tpl .= '<nav id="poster"><img src="' . $this->info[poster] . '" style="float:left;" alt="' . $this->info[title] . '" title="' . $this->info[title] . '"></nav>';
			//	$this->tpl .= '<nav id="title">' . $this->info[title] . '</nav>';
				$this->tpl .= '</div>';
				$this->tpl .= '</div>';
			}
			
			else
				
			{
				$this->tpl = ""; 
			}
			
			$this->run = str_replace( $this->result['url'], $this->tpl, $text);
			
			return $this->run;
			
		}
		
	}
	
?>