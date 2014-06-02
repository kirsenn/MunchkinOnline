<?php
/*
 * PHP-Class: Geo IP v. 0.92 beta
 * Copyright (c) 2009 Kuc Alexander <hodokii@ya.ru>
 *
 * Распространяется по GNU-лицензии (см. license.txt)
 * Вопросы об коммерческом использовании по e-mail: hodokii@ya.ru
*/

class geo_ip{
    var $url = 'http://194.85.91.253:8090/geo/geo.html'; // URL для запроса
    var $ip_limit = 4000;           // Максимальное кол-во IP в запросе
    var $encoding = 'windows-1251'; // Кодировка, в которой нужно вернуть данные (UTF-8, KOI8-R, WINDOWS-1251)
    var $return_type = 'array';     // Тип возвращаемых данных: array - массив, xml - ответ сервера (XML)
    var $timeout = 2;               // Время ожидания ответа до возникновения ошибки (сек.)
    var $user_agent = 'PHP-Class ( Geo_IP/0.92b )'; // Как скрипт должен представится серверу
    var $check_ip = false;          // true - проверять IP на правильность, false - не проверять
    var $bad_ip = false;            // Если IP неверный: true - пропустить, false - вернуть ошибку
    var $id = false;                // Хнанит ID полученый в ответ на запрос
    var $status = true;             // Добавлять ключ status в возвращаемый массив: true - да, false - нет
    var $ip_array_index = false;    // Тип ключей массива: true - IP, false - обычные цифровые индексы (0,1,...)
    var $default_template = 'Z';  // Список возвращаемых значений (см. ниже) (возвращается в том же порядке)
    var $parameners_assoc = array(
                                  'A' => 'inetnum',      // блок адресов, к которому относится искомый ip-адрес)
                                  'B' => 'inet-descr',   // описание блока по базе RIPE (www.ripe.net)
                                  'C' => 'inet-status',  // статус блока по базе RIPE
                                  'D' => 'city',         // город, к которому относится искомый ip
                                  'E' => 'region',       // регион, к которому относится искомый ip
                                  'F' => 'district',     // федеральный округ РФ, к которому относится искомый ip
                                  'G' => 'lat',          // географическая широта города
                                  'H' => 'lng',          // географическая долгота города
                                //'I' => '???',          // На случай, если добавится какой-то новый параметр :)
                                //'J' => '???',          // И этих параметров может быть много :)
                                  'Z' => 'all'           // все вышеперечисленные поля
                                 );

    function get_ip($ip_array = false, $id = false){
        if($ip_array == false) $ip_array = $this->get_real_ip();
        if(($query = $this->format_query($ip_array, $id)) == false)
            return false;
        if(($xml_request = $this->XML_query($query)) == false)
            return false;
        if($this->return_type == 'xml') return $this->encoding($xml_request);
        elseif($this->return_type == 'array'
               && ($request_array = $this->format_request($xml_request)) != false) return $request_array;
        return false;
    }

    function format_query(&$ips, $id){
        $tmp_fields = false;
        if(is_array($ips)) $ips = array_unique($ips);
        elseif(is_string($ips)) $ips = array_unique(preg_split("~[^0-9\.]+~", $ips, -1, PREG_SPLIT_NO_EMPTY));
        else return false;
        if($this->check_ip == true && is_ip($ips) == false)
            return false;
        $ips = array_slice($ips, 0, (int)$this->ip_limit);
        $tmp_ip = '<ip>' . implode('</ip><ip>', $ips) . '</ip>';
        if(strstr($this->default_template, 'Z')) $tmp_fields = '<all/>';
        else
            for($i = 0; $i < strlen($this->default_template); ++$i)
                if(isset($this->parameners_assoc[$this->default_template[$i]]))
                    $tmp_fields .= '<' . $this->parameners_assoc[$this->default_template[$i]] . '/>';
        if($tmp_fields == false) return false;
        if($id != false) $id = ' id="'.$id.'"';
        $xml_query = '<ipquery'.$id.'><fields>'.$tmp_fields.'</fields><ip-list>'.$tmp_ip.'</ip-list></ipquery>';
        unset($ips, $tmp_ip);
        return $xml_query;
    }

    function is_ip(&$ips){
        foreach($ips as $ip)
            if( !preg_match('~^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$~', $ip)
                && $bad_ip == false) return false;
        return true;
    }

    function get_real_ip(){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != ""){
            $client_ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : false;
            $client_ip = (getenv('REMOTE_ADDR')) ? getenv('REMOTE_ADDR') : false;
            $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
            reset($entries);
            while(list(, $entry) = each($entries)){
                $entry = trim($entry);
                if(preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)){
                    $private_ip = array(
                            '~^0\.~', '~^127\.0\.0\.1~', '~^192\.168\..*~',
                            '~^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*~', '~^10\..*~');
                    $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
                    if($client_ip != $found_ip){
                        $client_ip = $found_ip;
                        break;
                    }
                }
            }
        }else{
            if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] != "") return (string)$_SERVER['REMOTE_ADDR'];
            if(getenv("REMOTE_ADDR")) return (string)getenv('REMOTE_ADDR');
            return false;
        }
        return (string)$client_ip;
    }

    function format_request(&$xml){
        $array_out = array();
        if(preg_match('~<ip-answer id=[\'"](.*?)[\'"]~is', $xml, $id)){
            $this->id = $id[1];
        }
        if(!preg_match_all("~<ip value=['\"]([0-9\.]{7,15})['\"]>(.*?)<\/ip>~is", $xml, $found, PREG_PATTERN_ORDER))
            return false;
        unset($found[0]);
        $param = $this->parameners_assoc;
        $template = $this->default_template;
        if(strstr($template, 'Z')){
            $template = str_replace('Z', '', join(array_keys($param)));
        }
        foreach($found[1] as $key => $val){
            $arr = array();
            if(!$this->ip_array_index) $arr['ip'] = $val;
            for($i = 0; $i < strlen($template); ++$i){
                $tag = $param[$template[$i]];
                if(!preg_match('~<'.$tag.'>(.*?)<\/'.$tag.'>~i', $found[2][$key], $tag_cont)){
                    if($this->status) $arr['status'] = 'Не найдено';
                    continue;
                }
                if($this->status) $arr['status'] = 'OK';
                $arr[$tag] = $this->encoding($tag_cont[1]);
            }
            if($this->ip_array_index) $array_out[$val] = $arr;
            else $array_out[] = $arr;
        }
        return (isset($array_out[0]))? $array_out: false;
    }

    function encoding($s){
        $this->encoding = strtolower($this->encoding);
        if($this->encoding == 'windows-1251')
            return $s;
        elseif($this->encoding == 'koi8-r')
            return convert_cyr_string($s, 'w', 'k');
        elseif($this->encoding == 'utf-8')
            return $this->win_to_utf($s);
    }

    function win_to_utf($s){
        $t = '';
        $c209 = chr(209); $c208 = chr(208); $c129 = chr(129);
        for($i = 0; $i < strlen($s); $i++){
            $c = ord($s[$i]);
            if($c >= 192 and $c <= 239) $t .= $c208.chr($c-48);
            elseif($c > 239)  $t .= $c209.chr($c-112);
            elseif($c == 184) $t .= $c209.$c209;
            elseif($c == 168) $t .= $c208.$c129;
            else $t .= $s[$i];
        }
    return $t;
    }

    function XML_query(&$query){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        if($this->user_agent != '') curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        $result = curl_exec($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        return ($errno != 0 || empty($result))? false: $result;
    }
}
?>
