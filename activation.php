<?php
if (isset($_GET['login']) && isset($_GET['key']))
{
	$login = $_GET['login'];
	$key = $_GET['key'];
	$canact = true;
	$error = "";
	$time = time();
	$res = $mysql->sql_query("SELECT id_user, email, user_status, timeactive FROM users WHERE login='$login' LIMIT 1");
	
	// Есть ли пользователь с таким логином?
	if (mysql_num_rows($res) < 1)
	{
		$canact = false;
		$error .= "Такого пользователя нет!<br/>";
	}
	
	if($canact)
	{
		$user = mysql_fetch_array($res);
		// Может он уже активен?
		if ($user['user_status'] == 1)
		{
			$canact = false;
			$error .= "Данный логин уже подтвержден!<br/>";
		}
	}
	
	if($canact)
	{
		// Успел ли юзер активировать логин? (если нет - удаляем из базы)
		if ($user['timeactive'] - $time > 5*24*60*60)
		{
			$mysql->sql_query("DELETE FROM users WHERE login='$login' LIMIT 1");
			$canact = false;
			$error .= "Срок активации истёк! Регистрируйтесь заново.<br/>";
		}
		else
		{
			$key1 = md5(substr($user[1], 0 ,2).$user[0].substr($login, 0 ,2));
		}
	}

	// Поверяем "keystring"
	if ($key1 != $key)
	{
		$canact = false;
		$error .= "Неправильная контрольная сумма!<br/>";
	}
	
	if($canact)
	{
		$mysql->sql_query("UPDATE users SET user_status = 1 WHERE login='$login'");
		echo "<script>alert('Поздравляем вы успешно активировали аккаунт<br/> Используйте ваш логин ($login) для входа в игру'); location.href='index.php'; </script>";
	}
	else
	{
		echo "<span style=\"color:red\">$error</span>";
	}
}
?>
