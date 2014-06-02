<?php 
session_start();

die("Функция временно недоступна");

//Для всех файлов
require_once("global.php");
require_once("my_function.php");
require_once("spectate/game_table.php");
require_once("chat.php");
require_once("modules/mysql.php");
require_once("stat.php");
require_once("modules/functions.php");

$tableid = $_GET["id"];

if(is_numeric($tableid))
{
	$_SESSION['id_gt_spec'] = $tableid;
}

?>
<!DOCTYPE  HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"  "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html;  charset=Utf-8">
    <link href="spectate/screen.css"  rel="stylesheet" type="text/css" />
	<link href="style.css"  rel="stylesheet" type="text/css" />
	<link href="window_card.css"  rel="stylesheet" type="text/css" />
    <link href="ui-lightness/jquery-ui-1.8.9.custom.css"  rel="stylesheet" type="text/css" />
    
	
    <script type="text/javascript"  src="js/jquery-1.4.4.min.js"></script>
    <script type="text/javascript"  src="js/jquery-ui-1.8.9.custom.min.js"></script>
    <script type="text/javascript"  src="js/spectate_game.js"></script>
	
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

<div class="spectatebox">
<big>Вы находитесь в режиме просмотра  чужой игры.</big><br/>
<b>Игровой стол отличается от стола в режиме игры.</b>
<?php if(!isset($_SESSION["id_user"]))
{
	echo "<br/>Чтобы играть, вам следует войти или <a href=\"register.htm\">зарегистрироваться</a>";
} ?>
</div>

<div class="spectateexit" OnClick="location.href='<?=$_SERVER["HTTP_REFERER"] ?>'" title="Выйти из режима просмотра игры">
Выйти
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
