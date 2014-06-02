<?php 
session_start();
require_once("../global.php");

if (isset($_SESSION['id_gt_spec'])){
		//Открыть окно со шмотками игрока
        if ($_REQUEST['send_com']==0) {
            $DBLink=connectdb(); 
		  print '<div id="id_str1"><center>Активные шмотки</center></div>' ; 
		  for ($i=50;$i<=59;$i++) {
			$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_REQUEST['id_user']." AND place_card=".$i.")";
			$result=mysql_query($query); 
			if (mysql_num_rows($result)==0){
			  print '<div id="id_table'.$i.'"></div>' ;   
			}
			else{
			  $row=mysql_fetch_array($result);
			  print '<div id="id_table'.$i.'">';
			  print '<img id="id_card'.$i.'" class="id_card_enemy" src="./picture/'.$row['pic'].'" value="'.$i.'">';
			  print '</div>' ;  
			}               
		  }
	  
		  print '<div id="id_str2"><center>Неактивные  шмотки</center></div>' ;     
		  for ($i=60;$i<=69;$i++) {
			$query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card) WHERE (id_user=".$_REQUEST['id_user']." AND place_card=".$i.")";
			$result=mysql_query($query); 
			if (mysql_num_rows($result)==0){
			  print '<div id="id_table'.$i.'"></div>' ;   
			}
			else{
			  $row=mysql_fetch_array($result);
			  print '<div id="id_table'.$i.'">';
			  print '<img id="id_card'.$i.'" class="id_card_enemy" src="./picture/'.$row['pic'].'" value="'.$i.'">';
			  print '</div>';  
			}               
		  }  
		  
		  $query="SELECT * FROM users WHERE (id_user=".$_REQUEST['id_user'].")";
		  $result=mysql_query($query); 
		  if (mysql_num_rows($result)!=0){
				$row=mysql_fetch_array($result);    
				print '<div align="center" id="u_bonus_count">'.$row['u_bonus'].'</div>' ; 
		  }               
       }
}
?>  