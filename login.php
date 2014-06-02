<?php
if (isset($_POST['login']) && isset($_POST['pass']))
{
	$login = $_POST['login'];
	$pass = $_POST['pass'];
	$canlogin = true;
	$loginerror = "";
	$passwordHash = md5($pass);
	
	// Есть ли пользователь с таким логином?
	$result = $mysql->sql_query("SELECT * FROM users WHERE login='".$login."'");
	if (mysql_num_rows($result) < 1)
	{
		$canlogin = false;
		$loginerror = "Пользователя с таким логином нет";
	}
	else
	{
		//Проверяем верно ли введен пароль
		$row = mysql_fetch_assoc($result);
		if($row['pass']!= $passwordHash) 
		{
			$canlogin = false;
			$loginerror = "Неверный пароль";
		}	  
	}
	
	// Какой статус у пользователя?
	if($canlogin)
	{
		if ($row['user_status']!= 1)
		{
			$canlogin = false;
			$loginerror = "Логин не активирован";
		}
	}
	
	if($canlogin)
	{
		$_SESSION['id_user'] = $row['id_user'];
		$_SESSION['login'] = $row['login'];
		if (($row['sex']=="м") || ($row['sex']=="m"))$_SESSION['sex']= "man";
		else $_SESSION['sex']= "woman";
		$_SESSION['level'] = $row['level'];
		
		if(isset($_POST['remember']))
		{
			$cookvalue = $_SESSION['login']."|".md5($_SESSION['login'])."|".$passwordHash;
			setcookie("auth", $cookvalue, time()+1209600);
		}
		
		//Делаем пометку что пользователь в ОНЛАЙН
		$mysql->sql_query("UPDATE users SET active=".time().", last_ip='".getIP()."', last_page='index' WHERE id_user=".$_SESSION['id_user']."");
		//Авторизуем на форуме
		header ("location: forum/autologin.php?user=$login&pass=$pass");
	}
}
?>
