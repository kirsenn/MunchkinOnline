function click_card(){
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                click_obj=$(this);
                $("#window_card").dialog({                       
                        position: ["center","center"],
                        width: 400,
                        height: 620,
                        title: 'Просмотр карты',
                        open: function(eve, ui) { 
                                  showelement="<img id=\"obj_card\" src=\""+click_obj.attr("src")+"\">";
                            $("#window_card").html(showelement);   
                        },   
                        close: function(eve, ui) { 
                            $("#window_card").dialog('destroy');
                        }                                        
                });   
}     

		
//*********************************MAIN FUNCTION***********************************************		
function main_body_game(){
			flagV=0;

			$.ajax({  
				  type: "POST", 
				  url:  "spectate/chat.php",
				  data: "send_com_chat="+"1",   
				  datatype: 'json_data',
				  cache:false, 
				  success: function(json_data){ 
						if (json_data.length!==0){
							var data = eval( '(' + json_data + ')' ); 
							$("#mess_place").html(data.chat_text); 
						}																																	  
				  }
			});                    
         
             $(".card_batch, #id_card3,").hover(
             function(){
                $(this).css("cursor","pointer");
             },
             function(){
             
             }) 
             
             $(".nick, .bonus, .curse, .race, .u_class").hover(
             function(){
                $(this).css("cursor","pointer");
                $(this).css("background","#D2691E");
             },
             function(){
                $(this).css("background","#FFCC99");
             }) 
         
     		 
           $(".nick").click(function() {
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                click_obj=$(this);
                $("#window_card").dialog({
                        position: ["left","center"],
                        width: 500,
                        height: 420,
                        title: 'Информация об игроке',
						zIndex: 150,
                        modal: true,
                        open: function(eve, ui) { 
                              $.ajax({  
                                    type: "POST", 
                                    url: "spectate/player.php",  
                                    data: "send_com="+"0"+"&id_user="+click_obj.attr("value"),                      
                                    cache: false,  
                                    success: function(html){                              
                                         $("#window_card").html(html);
										 
										 $("#change_female").bind("click",change_female_click);  											  
										 $("#change_male").bind("click",change_male_click);  
										  
										  $("#change_female, #change_male").hover(
											 function(){
												$(this).css("cursor","pointer");
												$(this).css("background","#D2691E");
											 },
											 function(){
												$(this).css("background","#FFCC99");
											 }) 
										 
                                    }
                              }); 
                        },
                        close: function(eve, ui) { 
                            $("#window_card").dialog('destroy');
                        }                                                                     
                })
                                                      
             })   
             
             $(".u_class").click(function() {
                      if ($("#other_window").dialog( 'isOpen' )){$("#other_window").dialog('destroy');}
                      if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                      click_obj=$(this);
                      $("#other_window").dialog({
                              position: ["left","center"],
                              width: 450,
                              height: 270,
                              title: 'Класс игрока',
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                          type: "POST", 
                                          url: "spectate/u_class.php",  
                                          data: "send_com="+"0"+"&id_user="+click_obj.attr("value"),                      
                                          cache: false,  
                                          success: function(html){                              
                                               $("#other_window").html(html);  
                                               $(".id_card_class").bind("click",click_card_easy); 
                                               $(".id_card_enemy").bind("click",click_card_enemy);                                        
                                          }
                                    }); 
                              }, 
                              close: function(eve, ui) { 
                                  $("#other_window").dialog('destroy');
                              }                                                                                 
                      });                
             })  

             $(".race").click(function() {
                      if ($("#other_window").dialog( 'isOpen' )){$("#other_window").dialog('destroy');}
                      if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                      click_obj=$(this);
                      $("#other_window").dialog({
                              position: ["left","center"],
                              width: 450,
                              height: 270,
                              title: 'Раса игрока',
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                          type: "POST", 
                                          url: "spectate/race.php",  
                                          data: "send_com="+"0"+"&id_user="+click_obj.attr("value"),                      
                                          cache: false,  
                                          success: function(html){                              
                                               $("#other_window").html(html);  
                                               $(".id_card_class").bind("click",click_card_easy);
                                               $(".id_card_enemy").bind("click",click_card_enemy);                                         
                                          }
                                    }); 
                              }, 
                              close: function(eve, ui) { 
                                  $("#other_window").dialog('destroy');
                              }                                                                                 
                      });                
             })   
			 
             $(".bonus").click(function() {
                      if ($("#other_window").dialog( 'isOpen' )){$("#other_window").dialog('destroy');}
                      if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                      click_obj=$(this);
                      $("#other_window").dialog({
                              position: ["left","center"],
                              width: 835,
                              height: 550,
                              title: 'Шмотки игрока',
							  zIndex: 150,
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                          type: "POST", 
                                          url: "spectate/bonus.php",  
                                          data: "send_com="+"0"+"&id_user="+click_obj.attr("value"),                      
                                          cache: false,  
                                          success: function(html){                            
                                               $("#other_window").html(html);  
                                               $(".id_card_enemy").bind("click",click_card_enemy);
                                               $(".id_card_bonus").draggable({
                                                    containment: 'document',
                                                    revert: 'invalid',
                                                    opacity: 0.5,
                                                    zIndex: 10
                                                });  
                                                action_droppable();
                                              $(".id_card_bonus").bind("click",click_card_easy); 

											  $("#u_bonus_minus").bind("click",u_bonus_minus_click);  											  
											  $("#u_bonus_plus").bind("click",u_bonus_plus_click);  
											  
											  $("#u_bonus_minus, #u_bonus_plus").hover(
												 function(){
													$(this).css("cursor","pointer");
													$(this).css("background","#D2691E");
												 },
												 function(){
													$(this).css("background","#FFCC99");
												 }) 
                                          }
                                    });  
                              }, 
                              close: function(eve, ui) { 
                                  $("#other_window").dialog('destroy');
                              }                                                                                 
                      });  
             })    

             $(".curse").click(function() {
                      if ($("#other_window").dialog( 'isOpen' )){$("#other_window").dialog('destroy');}
                      if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                      click_obj=$(this);
                      $("#other_window").dialog({
                              position: ["left","center"],
                              width: 820,
                              height: 280,
                              title: 'Проклятия наложенные на игрока',
							  zIndex: 150,
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                          type: "POST", 
                                          url: "spectate/curse.php",  
                                          data: "send_com="+"0"+"&id_user="+click_obj.attr("value"),                      
                                          cache: false,  
                                          success: function(html){                            
                                               $("#other_window").html(html);  
                                               $(".id_card_enemy").bind("click",click_card_enemy);
                                               $(".id_card_bonus").draggable({
                                                    containment: 'document',
                                                    revert: 'invalid',
                                                    opacity: 0.5,
                                                    zIndex: 10
                                                });  
                                                action_droppable();
                                              $(".id_card_bonus").bind("click",click_card_easy); 

											  $("#u_bonus_minus").bind("click",u_bonus_minus_click);  											  
											  $("#u_bonus_plus").bind("click",u_bonus_plus_click);  
											  
											  $("#u_bonus_minus, #u_bonus_plus").hover(
												 function(){
													$(this).css("cursor","pointer");
													$(this).css("background","#D2691E");
												 },
												 function(){
													$(this).css("background","#FFCC99");
												 }) 
                                          }
                                    });  
                              }, 
                              close: function(eve, ui) { 
                                  $("#other_window").dialog('destroy');
                              }                                                                                 
                      });  
             })    
			 
 			                                            
             $(".id_card").bind("click",click_card);
			 
//Кусок кода отвечающии за повторяющиеся действия - каждые 4 секунд 
	    setInterval(function()
        {
		   if (flagV!==1){	//Этот кусок кода выполняем если только не открыта голосовалка 
				   $.ajax({  
						  type: "POST", 
						  url:  "spectate/chat.php",
						  data: "send_com_chat="+"2",   
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){
								if (json_data.length!==0){
									var data = eval( '(' + json_data + ')' ); 
									$("#mess_place").html(data.chat_text); 
							    }																																	  
						  }
					}); 
					
					$.ajax({ 
						  type:'POST', 
						  url: 'spectate/reload_table.php',
						  data: "send_com="+"1",
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){ 
								if (json_data.length!==0){
										var data = eval( '(' + json_data + ')' ); 

										$('#id_table10').html(data.id_table10);
										$('#id_table11').html(data.id_table11);
										$('#id_table12').html(data.id_table12);
										$('#id_table13').html(data.id_table13);
										$('#id_table14').html(data.id_table14);						
										$('#id_table15').html(data.id_table15);
										$('#id_table16').html(data.id_table16);
										$('#id_table17').html(data.id_table17);
										$('#id_table18').html(data.id_table18);
										$('#id_table19').html(data.id_table19);

										$("#id_card10, #id_card11, #id_card12, #id_card13, #id_card14, #id_card15, #id_card16, #id_card17, #id_card18, #id_card19").draggable({
											containment: 'document',
											revert: 'invalid',
											opacity: 0.5,
											zIndex: 5
										}); 
										
										$("#id_card10, #id_card11, #id_card12, #id_card13, #id_card14, #id_card15, #id_card16, #id_card17, #id_card18, #id_card19").css("z-index","5");
										$("#id_card10, #id_card11, #id_card12, #id_card13, #id_card14, #id_card15, #id_card16, #id_card17, #id_card18, #id_card19").bind("click",click_card); 
								}
						  }
					});         
				
						  $.ajax({ 
						  type:'POST', 
						  url: 'spectate/reload_user.php',
						  data: "send_com_reload_user="+"1",
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){					  
									var data = eval( '(' + json_data + ')' ); 
									if (data.flag_game==0){
										  $('#player1').css("visibility",data.win_user1);
										  $('#player2').css("visibility",data.win_user2);
										  $('#player3').css("visibility",data.win_user3);
										  $('#player4').css("visibility",data.win_user4);
										  $('#player5').css("visibility",data.win_user5);
										  $('#player6').css("visibility",data.win_user6);   
										  
										  $('#nick1').html(data.nick1);
										  $('#nick2').html(data.nick2);
										  $('#nick3').html(data.nick3);
										  $('#nick4').html(data.nick4);
										  $('#nick5').html(data.nick5);
										  $('#nick6').html(data.nick6);
										  
										  $('#level1').html(data.level1);
										  $('#level2').html(data.level2);
										  $('#level3').html(data.level3);
										  $('#level4').html(data.level4);
										  $('#level5').html(data.level5);
										  $('#level6').html(data.level6);   
										  
										  $('#bonus1').html(data.bonus1);
										  $('#bonus2').html(data.bonus2);
										  $('#bonus3').html(data.bonus3);
										  $('#bonus4').html(data.bonus4);
										  $('#bonus5').html(data.bonus5);
										  $('#bonus6').html(data.bonus6); 

										  $('#curse1').html(data.curse1);
										  $('#curse2').html(data.curse2);
										  $('#curse3').html(data.curse3);
										  $('#curse4').html(data.curse4);
										  $('#curse5').html(data.curse5);
										  $('#curse6').html(data.curse6); 

										  $('#u_gold1').html(data.u_gold1);
										  $('#u_gold2').html(data.u_gold2);
										  $('#u_gold3').html(data.u_gold3);
										  $('#u_gold4').html(data.u_gold4);
										  $('#u_gold5').html(data.u_gold5);
										  $('#u_gold6').html(data.u_gold6); 
										  
										  $('#race1').html(data.race1);
										  $('#race2').html(data.race2);
										  $('#race3').html(data.race3);
										  $('#race4').html(data.race4);
										  $('#race5').html(data.race5);
										  $('#race6').html(data.race6);
										  
										  $('#u_class1').html(data.u_class1);
										  $('#u_class2').html(data.u_class2);
										  $('#u_class3').html(data.u_class3);
										  $('#u_class4').html(data.u_class4);
										  $('#u_class5').html(data.u_class5);
										  $('#u_class6').html(data.u_class6);
										  
										  $('#player1').css("border",data.active1);
										  $('#player2').css("border",data.active2);
										  $('#player3').css("border",data.active3);
										  $('#player4').css("border",data.active4);
										  $('#player5').css("border",data.active5);
										  $('#player6').css("border",data.active6);     
										
										  $("#mess_end").css("visibility",data.button_mess_boi); 
									}else if (data.flag_game==1){
										  location.href="index.php?profile=statgame&idgt=spectate";
									}else if (data.flag_game==2){
										  location.href="gamemenu.php"; 
									}		
			
						  }
					}); 
            }         
        },6000);
		;
 }                     
