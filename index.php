<?php  
session_start();
require_once("global.php");
require_once("my_function.php");
require_once("modules/mysql.php");
require_once("modules/functions.php");
require_once("stat.php");

$loginerror = "";

//Выход с сайта
if (isset($_POST['logout']))
{
	//Удаляем все переменные сессии
	foreach($_SESSION as $key => $val)
	{
		unset($_SESSION[$key]);
	}
	setcookie("auth", "", time()-13600);
	header ("location: forum/autologin.php?logout");
}

//Вход на сайт
if (isset($_POST['login']) && isset($_POST['pass']))
{
	include("login.php");
}

//Если у чувака есть кукисы, авторизуем его
if(!isset($_SESSION['id_user']) && isset($_COOKIE["auth"]) && !isset($_POST['logout']))
{
	gocookauth();
}

if(isset($_SESSION['id_user']))
{
	$lastpage = $_SERVER["REQUEST_URI"];
	$mysql->sql_query("UPDATE users SET active=".time().", last_ip='".getIP()."', last_page='$lastpage' WHERE (id_user=".$_SESSION['id_user'].")");
}

?>
 
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html>
<head>
    <meta http-equiv="content-type" content="text/html;  charset=utf-8">
    <link href="style.css" rel="stylesheet" type="text/css" />
    <title>Манчкин Онлайн игра - Главная страница</title>
	<link rel="icon" href="favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="favicon.png" type="image/x-icon" />
	<link rel="icon" href="favicon.png" type="image/png" />
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
<script type="text/javascript"  src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript"  src="js/userinfo.js"></script>
</head>
<body>
<div id="user_info" style="display:none; position:absolute;"></div>
      <table class="maintable" border="0">
         <tbody>
            <tr>
				<td align="center">
					<a href="index.php"><img width="700px" height="250px" border="0" alt="Манчкин онлайн" src="./picture/mainlogo.png"></a>
				</td>
				<td width="200px" valign="top">
					<table width="100%">
						<tr>
							<a class="topmenu" title="Правила настольного манчкина" href="index.php?gamerule">Правила игры</a>
							<a class="topmenu" title="Как играть на этом сайте?" href="index.php?help">Справка</a>
							<a class="topmenu" title="Права на игру" href="index.php?rights">Права/rights</a>
							<a class="topmenu" title="Кто разработал игру" href="index.php?about">О нас/about us</a>
							<a class="topmenu" title="Перейти на форум" href="./forum">Форум</a>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="justify" valign="top">
				<?php
				#MAIN CONTENT
				if(strlen($_SERVER['QUERY_STRING'])<1){require_once("default.php");}
				if(isset($_GET["gamerule"])){require_once("gamerule.php");}
				if(isset($_GET["help"])){require_once("about_it.php");}
				if(isset($_GET["rights"])){require_once("rights.php");}
				if(isset($_GET["about"])){require_once("about_us.php");}
				if(isset($_GET["register"])){require_once("reg.php");}
				if(isset($_GET["watch"])){require_once("other_game.php");}
				if(isset($_GET["profile"])){require_once("kabinet.php");}
				if(isset($_GET["activation"])){require_once("activation.php");}
				if(isset($_GET["restore"])){require_once("restorepass.php");}
				if(isset($_GET["best"])){require_once("best.php");}
				#ENDOF MAINCONTENT
				?>
				</td>
				<td align="center" valign="top" width="200px" valign="undefined">
					<table class="authtable">						
						<tr>
							<td>Сервер запущен:12/04/11<hr></td>
						</tr>
						<tr>
							<td>Зарегистрировано:<?php echo mysql_num_rows($mysql->sql_query("SELECT * FROM users")); ?></td>
						</tr>
						<tr>
							<td>Активных:<?php echo mysql_num_rows($mysql->sql_query("SELECT * FROM users WHERE (active>=".(time()-(5*24*3600)).")")); ?></td>
						</tr>							
						<tr>
							<td><a style="text-decoration:underline;" href="whoonline.htm">Онлайн</a>:<?php echo mysql_num_rows($mysql->sql_query("SELECT * FROM users WHERE (active>=".(time()-180).")")); ?></td>
						</tr>
						<tr>
							<td><a style="text-decoration:underline;" title="Созданные столы" href="watch.htm">Смотреть игры</a> (<?php echo mysql_num_rows($mysql->sql_query("SELECT * FROM game_tables")); ?>)</td>
						</tr>
						<tr>
							<td><a style="text-decoration:underline;" title="Созданные столы" href="best.htm">Доска почета</a></td>
						</tr>
					</table>
					<br>
					<?php
					if (isset($_SESSION['id_user']) && isset($_SESSION['login']))
					{
						$login = $_SESSION['login'];
						?>
						<table class="authtable">
							<tr>
								<td>Привет, <b><?=$login?></b>!<hr /></td>
							</tr>
							<tr>
								<td><a style="font-size:14px; font-weight:bold;" title="Перейти в игровое меню" href="gamemenu.php">Играть</a></td>
							</tr>
							<tr>
								<td>
									<a title="Мои данные" href="index.php?profile=my">Личный кабинет</a>
									<?php 
									$get_new_msg = $mysql->sql_query("SELECT * FROM messages WHERE `to`='".$_SESSION['id_user']."' AND isread=0");
									$new_msg_cnt = mysql_num_rows($get_new_msg); 
									if($new_msg_cnt>0){echo "<br/><small style=\"color:red\">(Новые сообщения: $new_msg_cnt)</small>";}
									
									$getnewfriends = $mysql->sql_query("SELECT * FROM friends WHERE user2='".$_SESSION['id_user']."' AND status=0  ORDER BY id DESC");
									$countfriends = mysql_num_rows($getnewfriends);
									if($countfriends>0){echo "<br/><small style=\"color:red\">(Новые друзья: $countfriends)</small>";}
									?>
								</td>
							</tr>
							<tr>
								<td><form method="post"><input name="logout" type="submit" class="submit" value="Выйти" /></form></td>
							</tr>
						</table>
						<?php
					}
					else
					{
						?>
						<form name="authform" method="post">
						<table class="authtable">
							<tr>
								<td><b>Авторизация</b><hr /><?php if(strlen($loginerror)>0)	echo "<span style=\"color:red\">$loginerror</span><br/>"; ?></td>
							</tr>
							<tr>
								<td>Логин:</td>
							</tr>
							<tr>
								<td><input name="login" type="text" size="17" maxlength="15" /></td>
							</tr>
							<tr>
								<td>Пароль:</td>
							</tr>
							<tr>
								<td><input name="pass" type="password" size="17" maxlength="15" /></td>
							</tr>
							<tr>
								<td><input style="padding:1px;" type="checkbox" name="remember" /> <span style="cursor:pointer;" onClick="if(document.authform.remember.checked==true){document.authform.remember.checked=false;}else{document.authform.remember.checked=true;}" >Запомнить</span></td>
							</tr>
							<tr>
								<td><input class="submit" type="submit" name="submit" value="Войти" /></td>
							</tr>
							<tr>
								<td><a title="Перейти к восстановлению пароля" href="restore.htm">Забыли пароль?</a></td>
							</tr>
							<tr>
								<td><b><a title="Перейти к форме регистрации на сайте" href="register.htm">Зарегистрироваться</a></b></td>
							</tr>
						</table>
						</form> 
						<?php
					}
					?>
					<br/>
				<table class="authtable">
					<tr>
						<td><h3>Новости</h3><hr /></td>
					</tr>
					<tr>
						<td>
						<?php
						$getlastnews = $mysql->sql_query("SELECT * FROM forum_posts WHERE topic_id=2 ORDER BY id DESC LIMIT 3");
						while($ndata = mysql_fetch_assoc($getlastnews))
						{
							$ndata["message"] = str_replace("\n","<br/>",$ndata["message"]);
							?>
							<div class="newsitem">
							<span><?php echo date("d.m.Y H:i",$ndata["posted"]);?></span><br/>
							<a href="forum/viewtopic.php?pid=<?php echo $ndata["id"]."#p".$ndata["id"]; ?>"><?php echo substr($ndata["message"],0,124); if(strlen($ndata["message"])>124){echo "...";} ?></a>
							</div>
							<?php
						}
						?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<center>FunCardGame &copy; 2011</center>
			</td>
		</tr>
	</tbody>
</table>
</body>
</html>
