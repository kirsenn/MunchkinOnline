<?php
header('Content-type: text/html; charset=windows-1251');
// Подключение библиотеки
require('geoip_class.php');

// Самый простой пример: получение всех данных

// инициализация класса
$geo_ip = new geo_ip();

// Настройки (если требуются)
// $geo_ip->ip_limit = 4000;           // Максимальное кол-во IP в запросе
// $geo_ip->encoding = 'windows-1251'; // Кодировка, в которой нужно вернуть данные (UTF-8, KOI8-R, WINDOWS-1251)
// $geo_ip->return_type = 'array';     // Тип возвращаемых данных: array - массив, xml - ответ сервера (XML)
// $geo_ip->check_ip = false;          // true - проверять IP на правильность, false - не проверять
// $geo_ip->bad_ip = false;            // Если IP неверный: true - пропустить, false - вернуть ошибку
// $geo_ip->id = false;                // Хнанит ID полученый в ответ на запрос
// $geo_ip->status = true;             // Добавлять ключ status в возвращаемый массив: true - да, false - нет
// $geo_ip->ip_array_index = false;    // Тип ключей массива: true - IP, false - обычные цифровые индексы (0,1,...)
// $geo_ip->default_template = 'DEF';  // Список возвращаемых значений (см. в файле geo_ip.class.inc)
                                       // D - город, E - регион, F - федеральный округ (см. geo_ip.class.inc)

$array = $geo_ip->get_ip($_GET['ip']);

if($array[0]['status'] == 'OK'){ // Проверяем статус перед использованием массива
	//echo '<b>Cтатус:</b> ', $array[0]['status'], '<br />'; // Статус выполнения: OK - норма, Not Found - не найден
	//echo '<b>[ IP ]:</b> ', $array[0]['ip'], '<br />'; // IP
	//echo '<b>[ Inetnum ]:</b> ', $array[0]['inetnum'], '<br />'; // блок адресов, к которому относится искомый ip-адрес)
    //echo '<b>[ Inet-status ]:</b> ', $array[0]['inet-status'], '<br />'; // статус блока по базе RIPE
    echo '<b>Город:</b> ', $array[0]['city'], '<br />'; // город, к которому относится искомый ip
    echo '<b>Область:</b> ', $array[0]['region'], '<br />'; // регион, к которому относится искомый ip
    echo '<b>Регион:</b> ', $array[0]['district'], '<br />'; // федеральный округ РФ, к которому относится искомый ip
	echo '<b>Провайдер:</b> ', $array[0]['inet-descr'], '<br />'; // описание блока по базе RIPE (www.ripe.net)
    //echo '<b>[ Lat ]:</b> ', $array[0]['lat'], '<br />'; // географическая широта города
    //echo '<b>[ Lng ]:</b> ', $array[0]['lng'], '<br />'; // географическая долгота города
}
else
{
    echo $array[0]['status'];
}
?>
