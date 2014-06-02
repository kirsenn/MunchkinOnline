<?php
session_start();
require_once("global.php");
require_once("show_table_games.php"); 
require_once("my_function.php");
require_once("modules/functions.php");
require_once("modules/mysql.php");
require_once("stat.php");

//Если у чувака есть кукисы, авторизуем его
if(!isset($_SESSION['id_user']) && isset($_COOKIE["auth"]) && !isset($_POST['logout']))
{
	gocookauth();
}

if (!isset($_SESSION['id_user']))
{
	die("<meta http-equiv=\"content-type\" content=\"text/html;  charset=Utf-8\">Вы должны авторизироваться на сайте. Введите свои логин и пароль \n <script>alert('Вы должны авторизироваться на сайте. Введите свои логин и пароль'); location.href='/index.htm'; </script>"); 
}

if(!isset($_SESSION["level"])){$_SESSION["level"]=mysql_result($mysql->sql_query("SELECT level FROM users WHERE id_user='".$_SESSION['id_user']."'"),0);}
if (isset($_SESSION['control_level'])){unset($_SESSION['control_level']);}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html;  charset=Utf-8">
<title>Munchkin Game - Игровое меню</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="gamemenu.css" rel="stylesheet" type="text/css" />
<link href="./ui-lightness/jquery-ui-1.8.9.custom.css"  rel="stylesheet" type="text/css" />

<script type="text/javascript"  src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript"  src="js/jquery-ui-1.8.9.custom.min.js"></script>

<script type="text/javascript"  src="js/userinfo.js"></script>
 
<script type="text/javascript">       
         $(document).ready(function(){
            $.ajax({
                  type: "POST", 
                  url: "minichat.php",  
                  data: "send_com_forum="+"1",                      
                  cache: false,  
                  success: function(html){                              
                       $("#minichat_place").html(html); 
                       document.getElementById('minichat_place').scrollTop = document.getElementById('minichat_place').scrollHeight;                                 
                  }
             });  	
			 
			function send_text_minichat(){
                    send_text=$("#minichat_text").attr("value");
					if (send_text.length){
						$.ajax({  
							  type: "POST", 
							  url: "minichat.php",  
							  data: "send_com_forum="+"1"+"&send_text="+send_text,                      
							  cache: false,  
							  success: function(html){                              
								   $("#minichat_place").html(html); 
								   $("#minichat_text").attr("value",""); 
								   document.getElementById('minichat_place').scrollTop = document.getElementById('minichat_place').scrollHeight;                                         
							  }
						});    
					}					
             } 
             
             $("#minichat_com").bind("click",send_text_minichat);  	
			 
             $('#minichat_text').keyup(function(e) { 
                    if (e.keyCode == 13) {
                          send_text_minichat();
                    }
             }) 		
			 
             $("#minichat_com").hover(
             function(){
                $(this).css("cursor","pointer");
                $(this).css("background","#D2691E");
             },
             function(){
                $(this).css("background","#FFCC99");
             }) 
 
            function return_game(){
				var scrw = screen.width;
				var scrh = screen.height;
                     $.ajax({  
                          type: "POST", 
                          url: "init_game.php",  
                          data: "send_com_init="+"1"+"&scrw="+scrw+"&scrh="+scrh,
                          cache: false,  
                          success: function(html){        
                                if (html==1){                      
                                  location.href="game.php";
                                }else {
                                    $("body").append(html)
                                }                                                                              
                          }
                    });                          
             }   
             
            $(".return_game").bind("click",return_game);   
                                         
             function destroy_table() {
                    id_gt=$(this).attr("value");
                    $.ajax({  
                          type: "POST", 
                          url: "destroy_table.php",  
                          data: "send_com_destroy="+"0"+"&id_gt="+id_gt,                      
                          cache: false,  
                          success: function(html){                             
                              location.href="gamemenu.php";                                              
                          }
                    });              
             }
             
             $(".destroy_table").bind("click",destroy_table);       
             $(".destroy_table_whole").bind("click",destroy_table);
             
             function start_game() {
			 var scrw = screen.width;
			 var scrh = screen.height;
                     $.ajax({  
                          type: "POST", 
                          url: "init_game.php",   
                          data: "send_com_init="+"1"+"&scrw="+scrw+"&scrh="+scrh,
                          cache: false,  
                          success: function(html){                       
                                if (html==1){         
                                    location.href="game.php";    
                                }else{
                                    $("body").append(html);
                                }                                                                              
                          }
                    });                          
             }   
             
             $(".start_game").bind("click",start_game);               

            function join_game(){
                    id_gt=$(this).attr("value");
                    $.ajax({  
                          type:'POST', 
                          url: "join_table.php", 
                          data: "id_gt="+id_gt, 
                          cache:false, 
                          success: function(html){  
							  $("body").html(html);
                              location.href="gamemenu.php";                                    
                          }
                    });      
             }             
                         
             $(".join_game").bind("click",join_game); 

            function leave_table() {
                    id_gt=$(this).attr("value");
                    $.ajax({  
                          type: "POST", 
                          url: "leave_table.php",  
                          data: "id_gt="+id_gt,                      
                          cache: false,  
                          success: function(html){      
                              location.href="gamemenu.php";                                              
                          }
                    });              
            }
                                         
            $(".leave_table").bind("click",leave_table);
	
			function button_click_help() {
				click_obj=$(this);
				if (click_obj.attr("id")=="button_help2"){
					s_com=2;
				}else{
					s_com=1;
				}
				if ($("#window_message").dialog( 'isOpen' )){$("#window_message").dialog('destroy');}				  
				$("#window_message").dialog({
						  position: ["left","center"],
						  width: 900,
						  height: 500,
						  title: 'Помощник',
						  modal: true,
						  open: function(eve, ui) { 
								$.ajax({  
									type: "POST", 
									url: "help.php", 
									data: "send_com_help="+s_com, 										  
									cache: false,  
									success: function(html){                              
										$("#window_message").html(html); 
										
										$("#button_help1, #button_help2").hover(
										 function(){
											$(this).css("cursor","pointer");
											$(this).css("background","#D2691E");
										 },
										 function(){
											$(this).css("background","#FFCC99");
										 }) 
									}
								});    
						  },
						  close: function(eve, ui) { 
							  $("#window_message").dialog('destroy');
						  }  
				})     
			}

			$("#button_help, #button_help1, #button_help2").live("click",button_click_help);
			
            //Выполняется каждые 6 секунд			
            setInterval(function()
            {
				//Обновляется чат
				$.ajax({  
					  type: "POST", 
					  url:  "minichat.php",
					  data: "send_com_forum="+"2",   
					  cache: false,  
					  success: function(html){ 
						  $("#minichat_place").html(html);                                                                                                                                                              
					  }
				});  
				//Отображаются ваши игровые столы
               if (!($("#form_create_table").length)) {  
                       $.ajax({  
                          type: "POST", 
                          url:  "show_table_games.php",
                          data: "send_com="+"2",   
                          cache: false,  
                          success: function(html){ 
                               $("#your_table").html(html); 
                               
                               $(".destroy_table").bind("click",destroy_table); 
                               $(".destroy_table_whole").bind("click",destroy_table);   
                               $(".start_game").bind("click",start_game);   
                               $(".leave_table").bind("click",leave_table);
                               $(".return_game").bind("click",return_game);                                                                                                                                                                 
                              }
                   });                
               }                       
               //Отображаются все игровые столы
               $.ajax({  
                      type: "POST", 
                      url:  "show_table_games.php",
                      data: "send_com="+"1",   
                      cache: false,  
                      success: function(html){ 
                           $("#table_game").html(html);
                             
                           $(".join_game").bind("click",join_game);                                                                                                                                         
                      }
                }); 
                                         
            },4000);
			
			//Выполняется каждые 40 секунд
			setInterval(function()
            {
				//Отображаются текущее время сервера и количество пользователей онлайн
				$.ajax({  
					  type: "POST", 
					  url:  "time_now.php",
					  datatype: 'json_data',
					  cache:false, 
					  success: function(json_data){ 
							var data = eval( '(' + json_data + ')' );  
							$("#time_now").html(data.time_now);							
							$("#user_online").html(data.user_online);	
					  }
				});
			},60000);
			
			//Выполняется каждые 30 секунд
			setInterval(function()
			{
				newmessagecheck();
			},30000);
        })
 


</script> 

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-22682576-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
    
</head>
<body>
	<div id="mymessagesbtn" OnClick="mymessagesshow('in'); newmessagecheck();">
		<?php
		$new_msg_cnt = mysql_num_rows($mysql->sql_query("SELECT * FROM messages WHERE `to`='".$_SESSION['id_user']."' AND isread=0"));
		if($new_msg_cnt<1)
		{
			?><img width="32" src="picture/mail_generic.png"><?php
		}
		else
		{
			?>
			<img width="32" src="picture/newmessage.gif">
			<span style="position:absolute; background:red; color:#ffffff; display:block; font-size:9px; width:12px; height:12px; padding:2px; text-align:center"><?=$new_msg_cnt ?></span>
			<?php
		}
		?>
	</div>
	<div id="user_info" style="display:none; position:absolute;"></div>
	<div id="mymessages" style="display:none; position:absolute;"></div>
	<div id="window_message"></div>
	<table align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" align="center">
				<table width="100%" align="center" border="0">
					<tr>
						<td>
							
						</td>
						<td width="1001">
							<a href="/index.htm" style="position:absolute; margin-top:11px; margin-left:440px; text-shadow: 3px 3px 3px #ffffff; font-weight:bold;">&laquo; Главная страница</a>
							<img width="1000" id="menu_pic" src="picture/munchkin-game-bg.jpg" /><br/>
							<div id="time_now"><?php echo date("H:i"); ?></div>
							<div id="user_online">
							<?php 
								//Делаем пометку что пользователь в ОНЛАЙН
								if (!isset($_SESSION['id_user']))
								{
									$mysql->sql_query('UPDATE users SET active='.time().', last_ip="'.getIP().'", last_page="gmenu" WHERE id_user='.$_SESSION['id_user'].'');
								}
								//Получаем информацию сколько сейчас пользоватеелй онлайн на данной странице
								$nowonpage = mysql_num_rows($mysql->sql_query("SELECT * FROM users WHERE active>=".(time()-240)." AND last_page = 'gmenu' "));
								echo "$nowonpage";
								
								//Сбрасываем все параметры игрока характерезуюшие его игровой, вдруг игрока выкинули из-за стола
								if (isset($_SESSION['init'])){
									unset($_SESSION['init']);
								}
								if (isset($_SESSION['id_gt'])){
									unset($_SESSION['id_gt']);
								}
								if (isset($_SESSION['table_name'])){
									unset($_SESSION['table_name']);
								}
								if (isset($_SESSION['id_gt_spec'])){
									unset($_SESSION['id_gt_spec']);
								}
							?>	
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<div id="minichat_place" onload="location.hash=\'#endLine\'"></div>
			</td>
		</tr>
		<tr>
			<td width="790px">
				<input id="minichat_text" type="text" maxlength="200" />
			</td>
			<td align="left">
				 <div id="minichat_com">Отправить</div>
			</td>
		</tr>
	</table>
	
	
     <table class="gamememu" align="center" style="text-align: left; width: 965px;" border="0" cellpadding="2" cellspacing="2">
         <tbody>		
            <tr>
               <td align="center" valign="top" width="100%">
					<span id="button_help" class="helplink"><img src="picture/help-icon.png" /><br/><b>Как тут играть?</b></span>
					<hr/>
              </td>
            </tr>
            <tr>
               <td align="center" valign="top" width="100%">
                  <?php 
                  if (isset($_SESSION['id_user']))
				  {
                      echo '<div id="your_table">';
						your_table();
                      echo '</div>';
                  }
                  ?>                  
              </td>

            </tr>   
            <tr>
               <td align="center" valign="top" width="100%">
                  <?php 
                  if (isset($_SESSION['id_user'])) {
                      echo '<div id="table_game">';                      
                      show_table();  
                      echo '</div>';     
                  }
                  ?>                  
              </td>
            </tr>                      
          </tbody>
        </table>
		
<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter6764125 = new Ya.Metrika({id:6764125,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        }
        catch(e) { }
    });
})(window, 'yandex_metrika_callbacks');
</script></div>
<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
<noscript><div><img src="//mc.yandex.ru/watch/6764125" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

</body>
</html>