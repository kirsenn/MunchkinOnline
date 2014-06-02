<?php
if (!function_exists('json_encode')) {  
    function json_encode($value) 
    {
        if (is_int($value)) {
            return (string)$value;   
        } elseif (is_string($value)) {
	        $value = str_replace(array('\\', '/', '"', "\r", "\n", "\b", "\f", "\t"), 
	                             array('\\\\', '\/', '\"', '\r', '\n', '\b', '\f', '\t'), $value);
	        $convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
	        $result = "";
	        for ($i = mb_strlen($value) - 1; $i >= 0; $i--) {
	            $mb_char = mb_substr($value, $i, 1);
	            if (mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, "UTF-8"), $match)) {
	                $result = sprintf("\\u%04x", $match[1]) . $result;
	            } else {
	                $result = $mb_char . $result;
	            }
	        }
	        return '"' . $result . '"';                
        } elseif (is_float($value)) {
            return str_replace(",", ".", $value);         
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $with_keys = false;
            $n = count($value);
            for ($i = 0, reset($value); $i < $n; $i++, next($value)) {
                        if (key($value) !== $i) {
			      $with_keys = true;
			      break;
                        }
            }
        } elseif (is_object($value)) {
            $with_keys = true;
        } else {
            return '';
        }
        $result = array();
        if ($with_keys) {
            foreach ($value as $key => $v) {
                $result[] = json_encode((string)$key) . ':' . json_encode($v);    
            }
            return '{' . implode(',', $result) . '}';                
        } else {
            foreach ($value as $key => $v) {
                $result[] = json_encode($v);    
            }
            return '[' . implode(',', $result) . ']';
        }
    } 
}

//Данные из формы
function safform($text)
{
 $text = trim(htmlspecialchars($text));
 return $text;
}


//Вывод ссылок на страницы
function show_pages($i,$current,$col,$page)
{
	if($i!==$current && $current!=="")
	{echo " <a class=\"pagelink\" href=\"$page,page$i.htm\"> $i </a>&nbsp;| ";}
	elseif($i!==$col)
	{echo " <a class=\"pagelinkcur\" href=\"\"> $i </a>&nbsp;| ";}
	elseif($i==$col)
	{echo " <a class=\"pagelinkcur\" href=\"\"> $i </a>";}
}

//Вычисление ссылок на страницы
function link_pages($records,$query,$page)
{
	//Создаем подключение к MySQL
	$mysql = new MySQL;
	//Обнуляем счетчики количества на страницу, кол-ва страниц, и начала запроса
	$m=1;
	$col=0;
	$lim0 = 0;

	//Текущая страница
	if(isset($_GET['page'])){$current = $_GET['page'];} else $current = null;
	

	//Если нужна страница не первая
	if($current!==1 && $current!==null)
	{$lim0 = $records*$current-$records;}

	//Проверка на правильность ввода номера страницы
	if(isset($current))
	{
		if(is_numeric($current) && $current>0)
		{settype($current,integer); }
		else
		{die("Введите целое число!");}
	}
	$get_an = $mysql->sql_query($query);

	//Посчитаем количество страниц
	$num = mysql_num_rows($get_an); //Общее количество
	while($m<=$num)
	{
		$col++; //Количество страниц
		$m+=$records; //Количество на страницу
	}

	//Вывод ссылок со страницами при кол-ве страниц меньше 14
	if($col>1 && $col<14)
	{
		?>
		<table align="center" border="0" width="100%">
		<tr><td align="center">
		<?php
			if($current!==null && $current!==1) { $prev=$current-1; echo "<a class=\"pagelink\" href=\"$page,page$prev.htm\">&lt;&lt;&lt;</a>&nbsp;| "; }
			for($i=1;$i<=$col;++$i)	{show_pages($i,$current,$col,$page);}
			if($current!==$col) {$next=$current+1; if($current==""){$next++;} echo "<a class=\"pagelink\" href=\"$page,page$next.htm\">&gt;&gt;&gt;</a>"; }
		?>
		</td></tr></table><p></p>
		<?php
	}

	//Вывод ссылок со страницами при кол-ве страниц больше 14
	if($col>=14)
	{
		?>
		<table class="pages" align="center" border="0" width="100%">
		<tr><td align="center">
		<?php if(isset($current) && $current!==1) {$prev=$current-1; echo "<a class=\"pagelink\" href=\"$page,page$prev.htm\">&lt;&lt;&lt;</a>&nbsp;| "; }
		if(($current>0 && $current<4) || $current=="")
			{
				for($i=1;$i<=5;++$i){show_pages($i,$current,$col,$page);}
				echo "...&nbsp;| ";
				for($i=$col-2;$i<=$col;++$i){if($i<$col+1){show_pages($i,$current,$col,$page);}}
			}
		if($current>3 && $current<=$col)
			{
				for($i=1;$i<=2;++$i){show_pages($i,$current,$col,$page);}
				echo "...&nbsp;| ";
				for($i=$current-1;$i<=$current+1;++$i){if($i<$col+1){show_pages($i,$current,$col,$page);}}
				if($i<$col-2){echo "...&nbsp;| ";for($i=$col-1;$i<=$col;++$i){if($i<$col+1){show_pages($i,$current,$col,$page);}}}
			}
		if($current!==$col) {$next=$current+1; if($current==""){$next++;} echo "<a class=\"pagelink\" href=\"$page,page$next.htm\">&gt;&gt;&gt;</a>"; } ?>
		</td></tr></table>
		<?php
	}
	$new_query = "$query LIMIT $lim0,$records";
	return $new_query;
}


function gocookauth()
{
	$mysql = new MySQL;
	$cookdata = explode("|",$_COOKIE["auth"]);
	$trylogin = $cookdata[0];
	$tryloghash = $cookdata[1];
	$trypasshash = $cookdata[2];
	
	if($tryloghash==md5($trylogin))
	{
		$linkcheckuser = $mysql->sql_query("SELECT * FROM users WHERE login='$trylogin' AND pass='$trypasshash'");
		if(mysql_num_rows($linkcheckuser)>0)
		{
			$row_udata = mysql_fetch_array($linkcheckuser);
			$_SESSION['id_user'] = $row_udata['id_user'];
			$_SESSION['login'] = $row_udata['login'];
			if (($row_udata['sex']=="м") || ($row_udata['sex']=="m"))$_SESSION['sex']= "man";
			else $_SESSION['sex']= "woman";
			$_SESSION["level"] = $row_udata['level'];
		}
	}
}

function putmailtoqueue($email, $subject, $letter, $headers, $expiredays, $maxattempts)
{
	$mysql = new MySQL;
	$time = time();
	$expirequeue = time() + $expiredays*3600;
	$mysql->sql_query("INSERT INTO mailqueue (`id`, `email`, `subject`, `letter`, `headers`, `timestarted`, `timeexpire`, `attempts`, `maxattempts`, `lastattempt`) VALUES (0, '$email', '$subject', '$letter', '$headers', '$time', '$expirequeue', '1', '$maxattempts', '$time')");
}

function get_exper_level($u_level)
{
	if ($u_level==0)
	{
		$exper_level=100;
		return $exper_level;			
	}else
	{
		$u_level_next=$u_level-1;
		$exper_level=get_exper_level($u_level_next)+($u_level*100+100);
		return $exper_level;
	}
}
?>