<?php
class MySQL
{
	private $database='munchkin'; //Датабаза СУБД МуСКЛ
	private $host='localhost'; //Хост СУБД
	private $username = 'root'; //Имя поьзователя СУБД
	private $password='fastimport'; //Пароль к СУБД
	
	var $queries = 0; //Количество запросов будем считать
	var $arrays = 0; //Количество массивов
	var $timems = 0; //Время выполнения запросов
	
	//Устанавливаем соединение и ставим кодировку!
	function __construct()
	{
		$connection = @mysql_connect($this->host,$this->username,$this->password) or die('<html><head><meta http-equiv="content-type" content="text/html;  charset=utf-8"></head><body>Невозможно соединиться с сервером MySQL</body></html>');
		@mysql_select_db($this->database,$connection) or die('<html><head><meta http-equiv="content-type" content="text/html;  charset=utf-8"></head><body>Невозможно соединиться с базой данных на сервере MySQL</body></html>');
		$this->sql_query( "SET NAMES utf8;");
	}
	
	//Запрос к базе
	function sql_query($str_query)
	{
		$start = microtime(true);
		$this->queries++;
		//echo "<b><font color=\"#FF0000\">#".$this->queries." = ".$str_query."</font></b><br/>"; //Для отладки и просмотра текста запросов
		$link = @mysql_query($str_query);
		if($link)
		{
			$end = microtime(true);
			$this->timems=$this->timems+($end-$start);
			return $link;
		}
		else
		{
			echo "MySQL died because ";
			echo mysql_errno() . ": " . mysql_error(). "<br/>";
			echo "'$str_query'";
		}
	}
	
}

$mysql = new MySQL;
?>