<?php
if($_GET["restore"]=="try")
{
	?>
	<script>
		function trysendform()
		{
			if(document.restorepassform.restorelogin.value=='' && document.restorepassform.restoreemail.value=='' && document.restorepassform.captcha.value=='')
			{
				alert('Вы не заполнили поля');
				return false;
			}
			else
			{
				document.restorepassform.submit();
			}
		}
	</script>
	<center><h3>Восстановление забытого пароля</center></h3>
	<form name="restorepassform" method="post">
	<table align="center" width="50%">
		<tr>
			<td colspan="2" align="center">
				Введите логин <u>или</u> e-mail на который вы регистрировали аккаунт.
			</td>
		</tr>
		<tr>
			<td>
				Логин:
			</td>
			<td>
				<input type="text" name="restorelogin" value="<?=$_POST["restorelogin"] ?>" />
			</td>
		</tr>
		<tr>
			<td>
				E-mail:
			</td>
			<td>
				<input type="text" name="restoreemail" value="<?=$_POST["restoreemail"] ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<img src="pic.php" />
			</td>
			<td>
				<input type="text" name="captcha" value="" />
			</td>
		</tr>
	</form>
		<tr>
			<td colspan="2" align="center">
				<button OnClick="trysendform()">Отправить запрос</button>
			</td>
		</tr>
	</table>
	
	<?php
	if(isset($_POST["restorelogin"]) || isset($_POST["restoreemail"]))
	{
		$restorelogin = safform($_POST["restorelogin"]);
		$restoreemail = safform($_POST["restoreemail"]);
		$canrestore = true;
		$error = "";
		
		if(strlen($restorelogin)>1)
		{
			$tryforlogin = true;
			$linkcheckuser = $mysql->sql_query("SELECT * FROM users WHERE login='$restorelogin' ");
			if(mysql_num_rows($linkcheckuser)>0)
			{
				$row_udata = mysql_fetch_assoc($linkcheckuser);
			}
			else
			{
				$canrestore = false;
				$error = "Логина не существует!<br/>";
			}
		}
		
		if(strlen($restoreemail)>1 && $canrestore)
		{
			$tryforemail = true;
			if($tryforlogin && $row_udata["email"]!==$restoreemail)
			{
				$canrestore = false;
				$error = "Логин и email не относятся к одному аккаунту!<br/>";
			}
			else
			{
				$linkcheckuser = $mysql->sql_query("SELECT * FROM users WHERE email='$restoreemail' ");
				if(mysql_num_rows($linkcheckuser)>0)
				{
					$row_udata = mysql_fetch_assoc($linkcheckuser);
				}
				else
				{
					$canrestore = false;
					$error = "Пользователя с таким адресом нет в базе!<br/>";
				}
			}
		}
		
		if($_SESSION['regcode']!==$_POST["captcha"])
		{
			$canrestore = false;
			$error = "Вы не ввели проверочный код с картинки!<br/>";
		}
		
		if(!$tryforlogin && !$tryforemail)
		{
			$canrestore = false;
			$error = "Вы не ввели логин или email адрес!<br/>";
		}
		
		if($canrestore)
		{
			$time = time();
			$date = date("d.m.Y");
			$email = $row_udata["email"];
			
			$key = md5(substr($email, 0 ,2).$time);
			$linktorestore = "index.php?restore=go&user=".$row_udata["id_user"]."&key=$key";
			
			$subject = 'Munchkin восстановление пароля';
			$headers  = "Content-type: text/plain; charset=utf-8\r\n";
			$headers.= "From: Munchkin Online \r\n";
			$letter = "
Здравствуйте!
Это администрация онлайн игры МАНЧКИН. 

Вы или кто-то запросил восстановление пароля на сайте www.funcardgame.ru

Для восстановления пароля вам следует пройти по ссылке:
http://funcardgame.ru/$linktorestore
Данная ссылка будет доступна в течении 5 дней.
Отправлено: $date
ВНИМАНИЕ! Во избежание потери аккаунта, никому не показывайте данную ссылку!
";
			include('modules/smtp-func.php');
			// Отправляем письмо
			if (smtpmail($email, $subject, $letter, $headers))
			{
				$exp_time = time() + 5*24*60*60;
				$mysql->sql_query("DELETE FROM restorepass WHERE id_user=".$row_udata["id_user"].";");
				$mysql->sql_query("INSERT INTO restorepass VALUES (".$row_udata["id_user"].", '$key', $exp_time); ");
				echo "<script>alert('На указанный вами почтовый ящик было отправлено письмо со ссылкой для смены пароля. У вас 5 дней!'); location.href='/index.htm'</script>";
			}
			else
			{
				putmailtoqueue($email, $subject, $letter, $headers, 1, 12);
				echo "Произошла ошибка при отправке письма. Попробуйте зарегистрироваться еще раз";
			}
			unset($_SESSION['regcode']);
		}
		else
		{
			echo "<span width=\"100%\" style=\"text-align:center; color:red\">$error</span>";
		}
	}
}


if($_GET["restore"]=="go" && isset($_GET["key"]) && isset($_GET["user"]))
{
	$restoreid = safform($_GET["user"]);
	$restorekey = safform($_GET["key"]);
	$canrestore = true;
	$error = "";
	$time = time();
	
	$link_check_restoring = $mysql->sql_query("SELECT time_exp FROM restorepass WHERE id_user='$restoreid' AND kpass='$restorekey' ");
	
	//Удаляем просроченные
	$mysql->sql_query("DELETE FROM restorepass WHERE time_exp<$time"); 
	
	if(mysql_num_rows($link_check_restoring)>0)
	{
		$exptime = mysql_result($link_check_restoring,0);
		if($exptime < $time)
		{
			$canrestore = false;
			$error = "Срок смены пароля (5 дней) истек. Запросите еще раз!<br/>";
		}
	}
	else
	{
		$canrestore = false;
		$error = "Неверная ссылка. Запросите восстановление пароля еще раз!<br/>";
	}
	
	if($canrestore)
	{
		?>
		<center><h3>Восстановление забытого пароля</center></h3>
		<form name="restorepassform" method="post">
		<table align="center" width="50%">
			<tr>
				<td>
					Новый пароль:
				</td>
				<td>
					<input type="password" name="newpass1" value="" />
				</td>
			</tr>
			<tr>
				<td>
					Подтверждение:
				</td>
				<td>
					<input type="password" name="newpass2" value="" />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" value="Сохранить" />
				</td>
			</tr>
		</table>
		</form>
		<?php
		
		if(isset($_POST["newpass1"]))
		{
			$newpass1 = md5($_POST["newpass1"]);
			$newpass2 = md5($_POST["newpass2"]);
			
			if(strlen($newpass1)<1)
			{
				$canrestore = false;
				$error = "Пароль не может быть пустым!<br/>";
			}
			
			if($newpass1 !== $newpass2)
			{
				$canrestore = false;
				$error = "Пароли не совпадают!<br/>";
			}
			
			if($canrestore)
			{
				$mysql->sql_query("UPDATE users SET pass='$newpass1' WHERE id_user='$restoreid' ");
				$mysql->sql_query("DELETE FROM restorepass WHERE id_user='$restoreid' ");
				echo "<script>alert('Ваш пароль изменен. Используйте его для входа!'); location.href='/index.htm'</script>";
			}
		}
	}
	
	if(!$canrestore)
	{
		echo "<span width=\"100%\" style=\"text-align:center; color:red\">$error</span>";
	}
}

?>