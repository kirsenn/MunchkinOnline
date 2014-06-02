<?php 
//Данный файл выводит информацию по классу пользователя, при нажатии  "класс"
session_start();
require_once("global.php");
?>

<?php
if ( (isset($_SESSION['id_user'])) && (isset($_SESSION['init'])) && (isset($_SESSION['id_gt'])) ){
      if ($_REQUEST['send_com']==0) {
          $DBLink=connectdb(); 
          if ($_REQUEST['id_user']==$_SESSION['id_user']){       
              $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
                      WHERE (id_user=".$_REQUEST['id_user']." AND place_card IN (40,41,42)) ORDER BY place_card";       
              $result=mysql_query($query);
              if (mysql_num_rows($result)!=0){          
                  while ($row=mysql_fetch_array($result)){
                      if ($row['place_card']==40){
                          $per1='<img id="id_card40" class="id_card_class" src="./picture/'.$row['pic'].'" value="40">';
                      }elseif ($row['place_card']==41){
                          $per2='<img id="id_card41" class="id_card_class" src="./picture/'.$row['pic'].'" value="41">';
                      }elseif ($row['place_card']==42){
                          $per3='<img id="id_card42" class="id_card_class" src="./picture/'.$row['pic'].'" value="42">';
                      }        
                  }
                  if (isset($per1)){
                      print '<div id="id_table40" class="u_race_place">'.$per1.'</div>' ;
                  }else{
                      print '<div id="id_table40"><center>Расовый коктейль</center></div>' ;
                  }  
                  if (isset($per2)){
                      print '<div id="id_table41" class="u_race_place">'.$per2.'</div>' ; 
                  }else{
                      print '<div id="id_table41"><center>Раса 1</center></div>' ; 
                  }               
                  if (isset($per3)){
                      print '<div id="id_table42" class="u_race_place">'.$per3.'</div>' ; 
                  }else{
                      print '<div id="id_table42"><center>Раса 2</center></div>' ; 
                  }                
              }else{
                  print '<div id="id_table40"><center>Расовый коктейль</center></div>' ;   
                  print '<div id="id_table41"><center>Раса 1</center></div>' ;     
                  print '<div id="id_table42"><center>Раса 2</center></div>' ;             
              }
          }else{
              $query="SELECT * FROM carried_items JOIN cards ON (carried_items.id_card=cards.id_card)
                      WHERE (id_user=".$_REQUEST['id_user']." AND place_card IN (40,41,42)) ORDER BY place_card";       
              $result=mysql_query($query);
              if (mysql_num_rows($result)!=0){          
                  while ($row=mysql_fetch_array($result)){
                      if ($row['place_card']==40){
                          $per1='<img id="id_card40" class="id_card_enemy" src="./picture/'.$row['pic'].'" value="40">';
                      }elseif ($row['place_card']==41){
                          $per2='<img id="id_card41" class="id_card_enemy" src="./picture/'.$row['pic'].'" value="41">';
                      }elseif ($row['place_card']==42){
                          $per3='<img id="id_card42" class="id_card_enemy" src="./picture/'.$row['pic'].'" value="42">';
                      }        
                  }
                  if (isset($per1)){
                      print '<div id="id_table40">'.$per1.'</div>' ;
                  }else{
                      print '<div id="id_table40"><center>Расовый коктейль</center></div>' ;
                  }  
                  if (isset($per2)){
                      print '<div id="id_table41">'.$per2.'</div>' ; 
                  }else{
                      print '<div id="id_table41"><center>Раса 1</center></div>' ; 
                  }               
                  if (isset($per3)){
                      print '<div id="id_table42">'.$per3.'</div>' ; 
                  }else{
                      print '<div id="id_table42"><center>Раса 2</center></div>' ; 
                  }                
              }else{
                  print '<div id="id_table40"><center>Расовый коктейль</center></div>' ;   
                  print '<div id="id_table41"><center>Раса 1</center></div>' ;     
                  print '<div id="id_table42"><center>Раса 2</center></div>' ;             
              }
          } 
          mysql_close ($DBLink);          
      }       
}
?>