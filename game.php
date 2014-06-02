<?php 
session_start();
//Для всех файлов
require_once("global.php");
require_once("my_function.php");
require_once("game_table.php");
require_once("chat.php");
require_once("modules/mysql.php");
require_once("stat.php");
require_once("modules/functions.php");

//Если у чувака есть кукисы, авторизуем его
if(!isset($_SESSION['id_user']) && isset($_COOKIE["auth"]) && !isset($_POST['logout']))
{
	gocookauth();
}

if ((isset($_SESSION['id_user'])) && (isset($_SESSION['id_gt'])) && (isset($_SESSION['init'])) )
{
	$mysql->sql_query('UPDATE users SET active='.time().', last_ip="'.getIP().'", last_page="game" WHERE (id_user='.$_SESSION['id_user'].')');
}
elseif(!isset($_SESSION['id_user']))
{
	die("<meta http-equiv=\"content-type\" content=\"text/html;  charset=Utf-8\">Вы должны авторизироваться на сайте. Введите свои логин и пароль \n <script>alert('Вы должны авторизироваться на сайте. Введите свои логин и пароль'); location.href='/index.htm'; </script>");
}

if(!isset($_SESSION["level"])){$_SESSION["level"]=0;}
?>
<!DOCTYPE  HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html;  charset=Utf-8">
    <link href="screen.css"  rel="stylesheet" type="text/css" />
	<link href="style.css"  rel="stylesheet" type="text/css" />
	<link href="window_card.css"  rel="stylesheet" type="text/css" />
    <link href="./ui-lightness/jquery-ui-1.8.9.custom.css"  rel="stylesheet" type="text/css" />
    
	
    <script type="text/javascript"  src="js/jquery-1.4.4.min.js"></script>
    <script type="text/javascript"  src="js/jquery-ui-1.8.9.custom.min.js"></script>
	<script type="text/javascript"  src="js/userinfo.js"></script>
    <script type="text/javascript"  src="js/game.js"></script>
   <script type="text/javascript">       
         $(document).ready(function(){  
			main_body_game();  							
        });  
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
<body BGCOLOR="#FFCC99" >
<div id="mymessagesbtn" style="left:50%;" OnClick="mymessagesshow('in'); newmessagecheck();">
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
<div id="user_info" style="display:none; "></div>
<div id="mymessages" style="display:none; "></div>

<div id="waitplease" style="top:0px; left:0px; z-index:1000; position:absolute; display:inline; width:99%; height:99%;">
	<img src="picture/wait.png" style="position:absolute; top:40%; left:45%; opacity:1;">
</div>
<?php 
show_table(); 
?>
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
