<?php 
//Данный файл выводит информацию по классу пользователя, при нажатии  "класс"
session_start();
require_once("global.php");?>

<?php
if (isset($_SESSION['id_gt_spec'])){
      if ($_REQUEST['send_com']==0) {
          $DBLink=connectdb(); 
              $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
                      WHERE (id_user=".$_REQUEST['id_user']." AND place_card IN (30,31,32)) ORDER BY place_card";       
              $result=mysql_query($query);
              if (mysql_num_rows($result)!=0){          
                  while ($row=mysql_fetch_array($result)){
                      if ($row['place_card']==30){
                          $per1='<img id="id_card30" class="id_card_enemy" src="./picture/'.$row['pic'].'" value="30">';
                      }elseif ($row['place_card']==31){
                          $per2='<img id="id_card31" class="id_card_enemy" src="./picture/'.$row['pic'].'" value="31">';
                      }elseif ($row['place_card']==32){
                          $per3='<img id="id_card32" class="id_card_enemy" src="./picture/'.$row['pic'].'" value="32">';
                      }        
                  }
                  if (isset($per1)){
                      print '<div id="id_table30">'.$per1.'</div>' ;
                  }else{
                      print '<div id="id_table30"><center>Суперманчкин</center></div>' ;
                  }  
                  if (isset($per2)){
                      print '<div id="id_table31">'.$per2.'</div>' ; 
                  }else{
                      print '<div id="id_table31"><center>Класс 1</center></div>' ; 
                  }               
                  if (isset($per3)){
                      print '<div id="id_table32">'.$per3.'</div>' ; 
                  }else{
                      print '<div id="id_table32"><center>Класс 2</center></div>' ; 
                  }                
              }else{
                  print '<div id="id_table30"><center>Суперманчкин</center></div>' ;   
                  print '<div id="id_table31"><center>Класс 1</center></div>' ;     
                  print '<div id="id_table32"><center>Класс 2</center></div>' ;             
              }
        
      } 
}      
?>