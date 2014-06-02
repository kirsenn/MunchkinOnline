<?php 
session_start();
require_once("global.php");
require_once("my_function.php");

if ((isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
      if ($_REQUEST['send_com']==1) {
            $DBLink=connectdb(); 
            $query="SELECT * FROM cards_of_table JOIN cards ON (cards_of_table.id_card=cards.id_card) WHERE (id_gt=".$_SESSION['id_gt'].")";
            $result=mysql_query($query); 
			$json_data['id_table10']='';	
			$json_data['id_table11']='';
			$json_data['id_table12']='';	
			$json_data['id_table13']='';	
			$json_data['id_table14']='';				
			$json_data['id_table15']='';	
			$json_data['id_table16']='';
			$json_data['id_table17']='';	
			$json_data['id_table18']='';	
			$json_data['id_table19']='';				
			
            if (mysql_num_rows($result)!=0){
              while ($row=mysql_fetch_array($result)){
				  $json_data['id_table'.$row['place_card']]='<img id=\'id_card'.$row['place_card'].'\' class=\'id_card\' src=\'./picture/'.$row['pic'].'\' value=\''.$row['place_card'].'\'>';	
              }
            }            
            mysql_close ($DBLink); 
			
			echo json_encode($json_data);			
      } 
}

?>