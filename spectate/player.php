<?php 
session_start();
require_once("../global.php");

function show_back_card($result1,$showelement){
	$i=1;
	while ($i<=10){
		$per[$i]="&nbsp &nbspМесто $i";				
		$i=$i+1;
	}		
	while ($row1=mysql_fetch_array($result1)){

		if ($row1['card_type']=="door"){
			$type_door='<img class="id_card_hand" src="./picture/door_for_hand.jpg">';
		}else{
			$type_door='<img class="id_card_hand" src="./picture/loot_for_hand.jpg">';
		}						
		if ($row1['place_card']==20){
			$per[1]=$type_door;
		}elseif ($row1['place_card']==21){
			$per[2]=$type_door;
		}elseif ($row1['place_card']==22){
			$per[3]=$type_door;
		}elseif ($row1['place_card']==23){
			$per[4]=$type_door;
		}elseif ($row1['place_card']==24){
			$per[5]=$type_door;
		}elseif ($row1['place_card']==25){
			$per[6]=$type_door;
		}elseif ($row1['place_card']==26){
			$per[7]=$type_door;
		}   elseif ($row1['place_card']==27){
			$per[8]=$type_door;
		}   elseif ($row1['place_card']==28){
			$per[9]=$type_door;
		}   elseif ($row1['place_card']==29){
			$per[10]=$type_door;
		}                     
	}   
	$i=1;
	while ($i<=10){
		$showelement=$showelement."<div id=\"card_hand$i\">".$per[$i].'</div>';			
		$i=$i+1;
	}			   						  
	$showelement=$showelement.'<div id="player_str2"><center>Карты на руках</center></div>';	  
	return $showelement;
}

if (isset($_SESSION['id_gt_spec']))
{
	//Выполняем при открытии окна "Пользователь"
    if ($_REQUEST['send_com']==0) 
	{
        $DBLink=connectdb(); 			
		$query="SELECT * FROM users WHERE (id_user=".$_REQUEST['id_user'].")";       
		$result=mysql_query($query);
		$row=mysql_fetch_array($result);
		  
		$showelement='<div id="player_str1"><center>Общая информация</center></div>';
		$showelement=$showelement.'<div id="blok_info">';
		$showelement=$showelement.'<b>Имя: </b>'.$row['login'].'<br>';
		$showelement=$showelement.'<div id="sex1"><b>Пол: </b>'.$row['sex'].'</div>';
		$showelement=$showelement.'</div>';
			
		$showelement=$showelement.'<div id="player_str2"><center>Карты на руках</center></div>';	
		  
		//Отображаем сколько карт у пользователя на руках
		$query1="SELECT * FROM cards_of_user JOIN cards ON (cards_of_user.id_card=cards.id_card) WHERE (id_user=".$_REQUEST['id_user'].")";
		$result1=mysql_query($query1);
		  
		echo show_back_card($result1,$showelement);
    }
}          
?>