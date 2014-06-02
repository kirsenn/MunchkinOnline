<table align="center" width="100%" cellpadding="6" cellspacing="6">
 <tbody>
	<tr>
		<td align="center" width="20%" valign="undefined">
			<h2>Регистрация</h2>
		</td>
	</tr>
	<tr>
		<td align="center">
			<form action="" method="post">
			<table cellpadding="6" cellspacing="6">
			<tr>
			   <td align="left">Логин* :</td>
			   <td colspan="2"><input type="text" name="rLogin" value="<?php echo $_POST["rLogin"]; ?>" size="25" maxlength="15" /></td>
			</tr>
			<tr>
			   <td align="left">Пароль* :</td>
			   <td colspan="2"><input type="password" name="rPass" value="<?php echo $_POST["rPass"]; ?>" size="25" maxlength="15" /></td>
			</tr>
			<tr>
			   <td align="left">Повторите пароль* :</td>
			   <td colspan="2"><input type="password" name="rPass2" value="<?php echo $_POST["rPass2"]; ?>" size="25" maxlength="15" /></td>
			</tr>
			<tr>
			   <td align="left">E-mail* :</td>
			   <td colspan="2"><input type="text" name="rEmail" value="<?php echo $_POST["rEmail"]; ?>" size="25" maxlength="30" /></td>
			</tr>
			<tr>
			   <td align="left">Имя :</td>
			   <td colspan="2"><input type="text" name="rName" value="<?php echo $_POST["rName"]; ?>" size="25" maxlength="15" /></td>
			</tr>
			<tr>
			   <td align="left">Фамилия :</td>
			   <td colspan="2"><input type="text" name="rSname" value="<?php echo $_POST["rSname"]; ?>" size="25" maxlength="15" /></td>
			</tr>
			<tr>
				<td align="left">
					Год рождения:
				</td>
				<td align="left" colspan="2">
					<input type="text" name="bday" value="<?php if(strlen($_POST["bday"])>0){echo $_POST["bday"]; }else{echo "ДД";} ?>" size="3" />
					<input type="text" name="bmth" value="<?php if(strlen($_POST["bmth"])>0){echo $_POST["bmth"]; }else{echo "ММ";} ?>" size="3" />
					<input type="text" name="byar" value="<?php if(strlen($_POST["byar"])>0){echo $_POST["byar"]; }else{echo "ГГГГ";} ?>" size="3" />
				</td>
			</tr>
			<tr>
			   <td align="left">Город :</td>
			   <td colspan="2"><input type="text" name="rCity" value="<?php echo $_POST["rCity"]; ?>" size="25" maxlength="15" /></td>
			</tr>
			<tr>
			   <td align="left">Пол :</td>
			   <td align="left" colspan="2">				
					<input name="rsex" type="radio" value="m" checked>муж.
					<input name="rsex" type="radio" value="f">жен.
				</td>
			</tr>
			<tr>
			   <td align="left">Введите ответ* :</td>
			   <td><input type="text" name="rAnswer" value="" size="10" maxlength="10" /></td><td><img src="./pic.php" /></td>
			</tr>					
			<tr>
			   <td></td>
			   <td colspan="2">
				   <input style="border:#524231 1px solid;	border-radius:5px; -moz-border-radius:5px;	padding:6px; cursor:pointer;" type="reset" name="reset" value="Очистить" />
				   <input style="border:#524231 1px solid;	border-radius:5px; -moz-border-radius:5px;	padding:6px; cursor:pointer;" type="submit" name="ok" value="Готово" />
				</td>
			</tr>
			</table>
			</form>
	   </td>
	</tr>  
</tbody>
</table>
<?php

if(isset($_POST['rLogin'])) 
{
	$rLogin = safform($_POST['rLogin']);
	$rPass  = safform($_POST['rPass']);
	$rPass2 = safform($_POST['rPass2']);
	$rEmail = safform($_POST['rEmail']);
	$rName = safform($_POST['rName']);
	$rSname = safform($_POST['rSname']);
	$rCity = safform($_POST['rCity']);
	$rAnswer = safform($_POST['rAnswer']);
	$rsex = safform($_POST['rsex']);
	
	//Дата рождения
	if(is_numeric($_POST["bday"]) && is_numeric($_POST["bmth"]) && is_numeric($_POST["byar"]))
	{
		$birth=mktime(0,0,0,$_POST["bmth"],$_POST["bday"],$_POST["byar"]);
		$chbirth = $birth;
	}
	else
	{
		$chbirth = "";
	}

	$error = "";
	
	if (strlen($rLogin)<1)
	{
		$error .= "Поле логин должно быть заполнено<br/>";
	}
	
	if (strlen($rEmail)<1)
	{
		$error .= "Поле email должно быть заполнено<br/>";
	}
	
	if(!preg_match("/^[a-zA-Z0-9_\.\-]+@([a-zA-Z0-9\-]+\.)+[a-zA-Z]{2,6}$/", $rEmail))
	{
		$error .= "Указанный E-mail имеет недопустимый формат<br/>";
	}
	
	if (strlen($rPass)<3)
	{
		$error .= "Пароль должен быть длиной 3 и более символов<br/>";
	}
	
	if($rPass !== $rPass2)
	{
		$error .= "Пароли не совпадают<br/>";
	}
	
	if($rAnswer !== $_SESSION['regcode'])
	{
		$error .= "Неправильно введен ответ на вопрос-картинку<br/>";
	}	   

	// В базе данных у нас будет храниться md5-хеш пароля
	$mdPassword = md5($rPass);
	// А также временная метка (зачем - позже)
	$time = time();

	$checklogin = $mysql->sql_query("SELECT * FROM users WHERE login='$rLogin'");
	if(mysql_num_rows($checklogin)>0)
	{
		$error .= "Извините, введённый вами логин уже зарегистрирован. Используйте другой логин<br/>";
	}
	
	$checkmail = $mysql->sql_query("SELECT * FROM users WHERE email='$rEmail'");
	if(mysql_num_rows($checkmail)>0)
	{
		$error .= "Извините, введённый вами email уже использован для регистрации, попробуйте восстановить пароль<br/>";
	}

	if ($rsex=="f")$rsex="ж";	
	else $rsex="м";

	$ip_addr = getIP();

	if(strlen($error)<1)
	{
		$mysql->sql_query("INSERT INTO users (login, pass, name, sname, email, timeactive, sex, last_ip, u_level, birth, city) VALUES ('$rLogin', '$mdPassword', '$rName', '$rSname', '$rEmail', $time, '$rsex', '$ip_addr', 1, '$chbirth', '$rCity')");
		
		// Получаем Id, под которым юзер добавился в базу
		$id = mysql_result($mysql->sql_query("SELECT LAST_INSERT_ID()"), 0);
		
		//Создаем аналогичного юзера в базе форума
		$login = $rLogin;
		$pass = $mdPassword;
		$email =  $rEmail;
		$lastvisit =  time();
		$registered =  time();
		$mysql->sql_query("INSERT INTO forum_users VALUES ($id, 3, '$login', '$pass', '', '$email', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 'Russian', 'Oxygen', 1, 1304646541, NULL, NULL, $registered, '', $lastvisit, NULL, NULL, NULL);");
		
		// Составляем "keystring" для активации
		$key = md5(substr($rEmail, 0 ,2).$id.substr($rLogin, 0 ,2));
		$date = date("d.m.Y",$time);
		// Компонуем письмо
		require_once('modules/smtp-func.php');
		
		$title = 'Munchkin registration ';
		$headers  = "Content-type: text/plain; charset=utf-8\r\n";
		$headers .= "From: \"Munchkin Online\" <".$config['smtp_username'].">\r\n";
		$subject = $title;
		$letter = "
Здравствуйте!
Это администрация онлайн игры МАНЧКИН. 

Ваши регистрационные данные:
логин: $rLogin
пароль: $rPass

Для активации аккаунта вам следует пройти по ссылке:
http://funcardgame.ru/index.php?activation&login=$rLogin&key=$key
Данная ссылка будет доступна в течении 5 дней.
$date";

		// Отправляем письмо
		if (smtpmail($rEmail, $subject, $letter, $headers))
		{
			echo "<script>alert('Вы успешно зарегистрировались в системе. На указанный вами почтовый ящик было отправлено письмо со ссылкой для активации аккаунта. У вас 5 дней!'); location.href='index.php'</script>";
		}
		else
		{
			// Если письмо не отправилось, помещаем его в очередь
			putmailtoqueue($rEmail, $subject, $letter, $headers, 1, 12);
			echo "<script>alert('Вы зарегистрировались в системе. На указанный вами почтовый ящик будет отправлено письмо со ссылкой для активации аккаунта.'); location.href='index.php'</script>";
		}
		unset($_SESSION['regcode']);
	}
	else
	{
		echo "<span style=\"color:red\">$error</span>";
	}
}

?>
