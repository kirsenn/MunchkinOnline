var serverlag;

function showwaitdiv()
{
	$("#waitplease").css("display","inline");
	serverlag = setInterval("alert('Сервер не отвечает'); ",10000);
}

function hidewaitdiv()
{
	$("#waitplease").css("display","none");
	clearInterval(serverlag);
}

function my_drop(eve,ui,i)
{
	value_object=ui.draggable.attr("value");
	if ( (value_object==1) && ( (i>=10) && (i<=19) ) )
	{
		if ($("#mess_end").css("visibility")=="hidden")
		{
			alert('Вы не можете взять карту из колоды дверей и положить ее на игровой стол, сейчас не ваш ход!');
			return false;
		}
	}
	  
	if ((value_object==1)||(value_object==2))
	{			
		if (!($("#id_card"+i).length))
		{
			showwaitdiv();
				$.ajax({  
					type: "POST", 
					url:  "main.php",  
					data: "from_object="+ui.draggable.attr("value")+"&to_place="+i,                      
					cache: false,  
					success: function(html){ 
						   $("#id_table"+i).append(html);
						   $("#id_card"+i).width("100%");
						   $(".id_card").css("z-index","5");
						   $("#id_card"+i).draggable({
							  revert: 'invalid',
							  opacity: 0.5
						   });   
						   $("#id_card"+i).bind("click",click_card);
						   hidewaitdiv();
					}
				});
		}else{          
			return false;
		}                    
	}


	if ((value_object>=10)&&(value_object<=29)&&(value_object!=i))
	{
		if ($("#id_card"+i).length)
		{
			if (  (((value_object>=10)&&(value_object<=19))&&((i>=10)&&(i<=19))) || (((value_object>=20)&&(value_object<=29))&&((i>=20)&&(i<=29)))  ) 
			{
				showwaitdiv();
				$.ajax({  
					type: "POST", 
					url:  "main.php",  
					data: "from_object="+ui.draggable.attr("value")+"&to_place="+i,                      
					cache: false,  
					success: function(html)
					{  
								 $("#id_card"+ui.draggable.attr("value")).remove();
								 
								 $("#id_table"+ui.draggable.attr("value")).append("<img id=\"id_card"+ui.draggable.attr("value")+"\" class=\"id_card\"  src=\""+$("#id_card"+i).attr("src")+"\" value=\""+ui.draggable.attr("value")+"\">");               
								 $("#id_card"+ui.draggable.attr("value")).draggable({
								  revert: 'invalid',
								  opacity: 0.5,
								  zIndex: 5
								 });  
																	 
								 $("#id_card"+i).remove();  
											  
								 $("#id_table"+i).append("<img id=\"id_card"+i+"\" class=\"id_card\" src=\""+ui.draggable.attr("src")+"\" value=\""+i+"\">");               
								 $("#id_card"+i).draggable({
								  containment: 'document',
								  revert: 'invalid',
								  opacity: 0.5,
								  zIndex: 5
								 }); 
								 
								 $("#id_card"+i).unbind("click",click_card); 
								 $("#id_card"+i).bind("click",click_card); 
								 hidewaitdiv();
					}    
				})

			}else
			{
				$("#id_card"+ui.draggable.attr("value")).remove();
				 
				$("#id_table"+ui.draggable.attr("value")).append("<img id=\"id_card"+ui.draggable.attr("value")+"\" class=\"id_card\" src=\""+ui.draggable.attr("src")+"\" value=\""+ui.draggable.attr("value")+"\">");               
				$("#id_card"+ui.draggable.attr("value")).draggable({
					containment: 'document',
					revert: 'invalid',
					opacity: 0.5,
					zIndex: 5
				});                                                      
				return false;  
			}      
		 
		}
		else
		{
			showwaitdiv();
			$.ajax({  
				type: "POST", 
				url:  "main.php",  
				data: "from_object="+ui.draggable.attr("value")+"&to_place="+i,                      
				cache: false,  
				success: function(html){
					$("#id_card"+ui.draggable.attr("value")).remove();
					 
					$("#id_table"+i).append("<img id=\"id_card"+i+"\" class=\"id_card\" src=\""+ui.draggable.attr("src")+"\" value=\""+i+"\">");               
					$("#id_card"+i).draggable({
						containment: 'document',
						revert: 'invalid',
						opacity: 0.5,
						zIndex: 5
					}); 
					 
					$("#id_card"+i).bind("click",click_card);  
					hidewaitdiv();
				}
			})  
		}                 
	}else
	{
		if (value_object==i)
		{
			$("#id_card"+ui.draggable.attr("value")).remove();
				 
			$("#id_table"+i).append("<img id=\"id_card"+i+"\" class=\"id_card\" src=\""+ui.draggable.attr("src")+"\" value=\""+i+"\">");               
			$("#id_card"+i).draggable({
				containment: 'document',
				revert: 'invalid',
				opacity: 0.5,
				zIndex: 5
			}); 
		   
			$("#id_card"+i).unbind("click",click_card);
			$("#id_card"+i).bind("click",click_card);
		}
		else
		{
			return false;
		}  
	}	
}

function my_drop1(eve,ui,i){
		value_object=ui.draggable.attr("value");                                    
		if ((value_object>=50)&&(value_object<=75)&&(value_object!=i)){
			   if ($("#id_card"+i).length){
					  if (  ((value_object>=50)&&(value_object<=75))&&((i>=50)&&(i<=75)) ) {
						showwaitdiv();
							$.ajax({  
								type: "POST", 
								url:  "bonus.php",  
								data: "send_com="+"1"+"&from_object="+ui.draggable.attr("value")+"&to_place="+i,                      
								cache: false,  
								success: function(html){  
									$("#id_card"+ui.draggable.attr("value")).remove();
									   
									$("#id_table"+ui.draggable.attr("value")).append("<img id=\"id_card"+ui.draggable.attr("value")+"\" class=\"id_card_bonus\"  src=\""+$("#id_card"+i).attr("src")+"\" value=\""+ui.draggable.attr("value")+"\">");               
									$("#id_card"+ui.draggable.attr("value")).draggable({
										revert: 'invalid',
										opacity: 0.5,
										zIndex: 5
									});  
																		   
									$("#id_card"+i).remove();  
													
									$("#id_table"+i).append("<img id=\"id_card"+i+"\" class=\"id_card_bonus\" src=\""+ui.draggable.attr("src")+"\" value=\""+i+"\">");               
									$("#id_card"+i).draggable({
													  revert: 'invalid',
													  opacity: 0.5,
													  zIndex: 5
													 }); 
													 
									$("#id_card"+i).bind("click",click_card_easy);
									$("#id_card"+ui.draggable.attr("value")).bind("click",click_card_easy);
									hidewaitdiv();								
								}    
							})

						}                                             
				}
				else{
					showwaitdiv();
					$.ajax({  
						type: "POST", 
						url:  "bonus.php",  
						data: "send_com="+"1"+"&from_object="+ui.draggable.attr("value")+"&to_place="+i,                      
						cache: false,  
						success: function(html){
			   
						    $("#id_card"+ui.draggable.attr("value")).remove();
							$("#mess_place").append(ui.draggable.attr("value"));
						   
							$("#id_table"+i).append("<img id=\"id_card"+i+"\" class=\"id_card_bonus\" src=\""+ui.draggable.attr("src")+"\" value=\""+i+"\">");               
							$("#id_card"+i).draggable({
								revert: 'invalid',
								opacity: 0.5,
								zIndex: 5
							}); 
						   
							$("#id_card"+i).bind("click",click_card_easy);
							hidewaitdiv();	
						}
				  })  
			   }                 
		}else{
			if (value_object==i)
			{
				$("#id_card"+ui.draggable.attr("value")).remove();
					 
				$("#id_table"+i).append("<img id=\"id_card"+i+"\" class=\"id_card_bonus\" src=\""+ui.draggable.attr("src")+"\" value=\""+i+"\">");               
				$("#id_card"+i).draggable({
					containment: 'document',
					revert: 'invalid',
					opacity: 0.5,
					zIndex: 5
				}); 
				
				$("#id_card"+i).unbind("click",click_card);
				$("#id_card"+i).bind("click",click_card);
			}
			else
			{
				return false;
			}  
		}   
}       

function action_droppable(){
			$("#id_table50").droppable({
									  accept: function(d) { 
											  if (d.hasClass("id_card_bonus")){ 
												  return true;
											  }
									  },
									  hoverClass: "hover",                              
									  drop: function(eve,ui){
											my_drop1(eve,ui,50);                                                              
									  }                        
                        });
            $("#id_table51").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,51);                                                              
                                      }                        
                        });
            $("#id_table52").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,52);                                                              
                                      }                        
                        });
            $("#id_table53").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,53);                                                              
                                      }                        
                        });
            $("#id_table54").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,54);                                                              
                                      }                        
                        });
            $("#id_table55").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,55);                                                              
                                      }                        
                        });
            $("#id_table56").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,56);                                                              
                                      }                        
                        });
            $("#id_table57").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,57);                                                              
                                      }                        
                        });
            $("#id_table58").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,58);                                                              
                                      }                        
                        });
            $("#id_table59").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,59);                                                              
                                      }                        
                        });
            $("#id_table60").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,60);                                                              
                                      }                        
                        });
            $("#id_table61").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,61);                                                              
                                      }                        
                        });
            $("#id_table62").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,62);                                                              
                                      }                        
                        });
            $("#id_table63").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,63);                                                              
                                      }                        
                        });
            $("#id_table64").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,64);                                                              
                                      }                        
                        });
            $("#id_table65").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,65);                                                              
                                      }                        
                        });
            $("#id_table66").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,66);                                                              
                                      }                        
                        });
            $("#id_table67").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,67);                                                              
                                      }                        
                        });
            $("#id_table68").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,68);                                                              
                                      }                        
                        });
            $("#id_table69").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,69);                                                              
                                      }                        
                        });
            $("#id_table70").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,70);                                                              
                                      }                        
                        });
            $("#id_table71").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,71);                                                              
                                      }                        
                        });
            $("#id_table72").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,72);                                                              
                                      }                        
                        });
            $("#id_table73").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,73);                                                              
                                      }                        
                        });
            $("#id_table74").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,74);                                                              
                                      }                        
                        });
            $("#id_table75").droppable({
                                      accept: function(d) { 
                                              if (d.hasClass("id_card_bonus")){ 
                                                  return true;
                                              }
                                      },
                                      hoverClass: "hover",                              
                                      drop: function(eve,ui){
                                            my_drop1(eve,ui,75);                                                              
                                      }                        
                        });
}; 			  
		  
function click_card(){
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('close');}
                click_obj=$(this);
				showwaitdiv();
                $("#window_card").dialog({                       
                        position: ["center","center"],
                        width: 600,
                        height: 620,
                        title: 'Управление картой',
                        open: function(eve, ui) { 							
							$.ajax({  
								type: "POST", 
								url: "window_card.php",  
								data: "send_com="+"6"+"&place_card="+click_obj.attr("value"), 
								zIndex: 150,		
								datatype: 'json_data',
								cache:false, 
								success: function(json_data){	
									var data = eval( '(' + json_data + ')' ); 									
									$("#window_card").html(data.content_window);
									
									$("#button_shmotki").bind("click",button_shmotki_click);  											  
									$("#button_apply").bind("click",button_apply); 
									$("#button_sell").bind("click",button_sell); 
									$("#button_sbros").bind("click",button_sbros_click);
									$("#button_kill").bind("click",button_kill_click);
									
									$("#button_shmotki, #button_apply,#button_sell, #button_sbros, #button_kill").hover(
									function(){
										$(this).css("cursor","pointer");
										$(this).css("background","#D2691E");
									},
									function(){
										$(this).css("background","#FFCC99");
									}) 
									hidewaitdiv();	
								}
							});   					
                        },   
                        close: function(eve, ui) { 
                            $("#window_card").dialog('destroy');

                        }                                        
                });   
}     

function change_female_click(){
	showwaitdiv();		
	$.ajax({  
		type: "POST", 
		url: "player.php",  
		data: "send_com="+"2"+"&id_user="+click_obj.attr("value"),                      
		cache: false,  
		success: function(html){                              
			$("#window_card").html(html); 
													  
			$("#change_male").bind("click",change_male_click);  
			  
			$("#change_male").hover(
				function(){
					$(this).css("cursor","pointer");
					$(this).css("background","#D2691E");
				},
				function(){
					$(this).css("background","#FFCC99");
				}
			) 
			hidewaitdiv();		
		}
	});      		 
}
			 
function change_male_click(){
	showwaitdiv();	
	$.ajax({  
		type: "POST", 
		url: "player.php",  
		data: "send_com="+"1"+"&id_user="+click_obj.attr("value"),                      
		cache: false,  
		success: function(html){                              
			$("#window_card").html(html);    
			   
	  	    $("#change_female").bind("click",change_female_click);  											  
				  
			$("#change_female").hover(
				function(){
					$(this).css("cursor","pointer");
					$(this).css("background","#D2691E");
				},
			function(){
					$(this).css("background","#FFCC99");
				}
			) 	
			hidewaitdiv();					
		}
	});       		 
}			 

function send_text_chat(){		    
		send_text=$("#mess_text").attr("value");
		if (send_text.length){
			showwaitdiv();
			$.ajax({  
				  type: "POST", 
				  url: "chat.php",  
				  data: "send_com_chat="+"1"+"&send_text="+send_text+"&send_user="+$("#mess_whom").text(),                      
				  datatype: 'json_data',
				  cache:false, 
				  success: function(json_data){ 			        
						if (json_data.length!==0){					
							var data = eval( '(' + json_data + ')' ); 
							$("#mess_place").html(data.chat_text); 
							$("#mess_text").attr("value",""); 
						}				
						hidewaitdiv();		
				  }
			});                 
		}
} 

function u_bonus_minus_click(){
	showwaitdiv();
	$.ajax({  
		type: "POST", 
		url: "bonus.php",  
		data: "send_com="+"2",                      
		cache: false,  
		success: function(html){                              
			$("#u_bonus_count").html(html);    
			hidewaitdiv();
		}
	});      			 
}
			 
function u_bonus_plus_click(){
	showwaitdiv();
	$.ajax({  
		type: "POST", 
		url: "bonus.php",  
		data: "send_com="+"3",                      
		cache: false,  
		success: function(html){                              
			$("#u_bonus_count").html(html);
			hidewaitdiv(); 
		}
	});   		 
}

function u_level_minus_click(){
	showwaitdiv();
	$.ajax({  
		type: "POST", 
		url: "level.php",  
		data: "send_com="+"1"+"&id_user="+click_obj.attr("value"),                      
		datatype: 'json_data',
		cache:false, 
		success: function(json_data){		
			var data = eval( '(' + json_data + ')' ); 					                               
			$("#blok_info_level").html(data.content_window);                 

			$("#u_level_minus").css("visibility",data.visible_minus); 
			$("#u_level_plus").css("visibility",data.visible_plus); 			
			
			hidewaitdiv();			   
		}
	});
}
			 
function u_level_plus_click(){
	showwaitdiv();
	$.ajax({  
		type: "POST", 
		url: "level.php",  
		data: "send_com="+"2"+"&id_user="+click_obj.attr("value"),                      
		datatype: 'json_data',
		cache:false, 
		success: function(json_data){		
			var data = eval( '(' + json_data + ')' ); 					                               
			$("#blok_info_level").html(data.content_window);                 

			$("#u_level_minus").css("visibility",data.visible_minus); 
			$("#u_level_plus").css("visibility",data.visible_plus); 						
			hidewaitdiv();			   
		}
	});  	 		 
}

function button_shmotki_click(){
	showwaitdiv();	
	$.ajax({  
		type: "POST", 
		url:  "window_card.php",  
		data: "send_com="+"3"+"&from_object="+click_obj.attr("value"),                      
		cache: false,  
		success: function(html){                                                
			if (html==1){
				  $(click_obj).remove(); 
				  $("#window_card").dialog("close");
			}
			hidewaitdiv();	
		}
	});      	 
}

function button_kill_click(){
	showwaitdiv();	
	$.ajax({  
		type: "POST", 
		url:  "window_card.php",  
		data: "send_com="+"5"+"&place_card="+click_obj.attr("value"),                      
		datatype: 'json_data',
		cache:false, 
		success: function(json_data){	
			var data = eval( '(' + json_data + ')' ); 	
			if (data.result_com==1){
				$(click_obj).remove(); 
				$("#window_card").dialog("destroy");
				
				$("#id_table20").html(data.id_card20);
				$("#id_table21").html(data.id_card21);
				$("#id_table22").html(data.id_card22);
				$("#id_table23").html(data.id_card23);
				$("#id_table24").html(data.id_card24);
				$("#id_table25").html(data.id_card25);
				$("#id_table26").html(data.id_card26);
				$("#id_table27").html(data.id_card27);
				$("#id_table28").html(data.id_card28);
				$("#id_table29").html(data.id_card29);
				
				$("#id_card20, #id_card21, #id_card22, #id_card23, #id_card24, #id_card25, #id_card26, #id_card27, #id_card28, #id_card29").unbind("click",click_card); 
				$("#id_card20, #id_card21, #id_card22, #id_card23, #id_card24, #id_card25, #id_card26, #id_card27, #id_card28, #id_card29").bind("click",click_card); 
				
				$("#id_card20, #id_card21, #id_card22, #id_card23, #id_card24, #id_card25, #id_card26, #id_card27, #id_card28, #id_card29").draggable({
                containment: 'document',
                revert: 'invalid',
                opacity: 0.5,
                zIndex: 5
				});  
			}else{
				$("#window_card").dialog("close");
			}
			hidewaitdiv();	
		}
	});             	 
}	
			 			 
function button_apply(){
	showwaitdiv();		
	$.ajax({  
			type: "POST", 
			url:  "window_card.php",  
			data: "send_com="+"1"+"&from_object="+click_obj.attr("value"),                      
			cache: false,  
			success: function(html){    

				$("#mess_place").append(html);
				if (html==1){
					$(click_obj).remove(); 
					$("#window_card").dialog("close");
				}else{
					$("#window_card").dialog("close");
				}
				hidewaitdiv();	
			}
	});          	 
}	

function button_sell(){
	showwaitdiv();	
				  $.ajax({  
						type: "POST", 
						url:  "window_card.php",  
						data: "send_com="+"2"+"&from_object="+click_obj.attr("value"),                      
						cache: false,  
						success: function(html){ 
							if (html==1){
								$(click_obj).remove(); 
								$("#window_card").dialog("close");
							}
							hidewaitdiv();	
						}
				  });          	 
}	

function change_gold(){
		$.ajax({  
			type: "POST", 
			url: "change_gold.php",  
			data: "send_com_gold="+"1",                      
			datatype: 'json_data',
			cache:false, 
			success: function(json_data){		
					var data = eval( '(' + json_data + ')' ); 					  

					$("#other_window").html(data.content_window);  

					$("#button_change_gold").bind("click",change_gold);  											  
				  
					$("#button_change_gold").hover(
						function(){
							$(this).css("cursor","pointer");
							$(this).css("background","#D2691E");
						},
						function(){
							$(this).css("background","#FFCC99");
						}
					) 	 
			}
		});    
}

function button_sbros_click(){
				  $.ajax({  
						type: "POST", 
						url:  "window_card.php",  
						data: "send_com="+"0"+"&from_object="+click_obj.attr("value"),                      
						cache: false,  
						success: function(html){                                                     
							$("#mess_place").append(html);
							$(click_obj).remove(); 
							$("#window_card").dialog("close");
						}
				  });             	 
}		

function button_na_stol_click(){
				  $.ajax({  
						type: "POST", 
						url:  "window_card.php",  
						data: "send_com="+"4"+"&from_object="+click_obj.attr("value"),                      
						cache: false,  
						success: function(html){      
							if (html==1){
								$(click_obj).remove(); 								
							}
							$("#window_card").dialog("close");
						}
				  });             	 
}		
			 
function click_card_easy(){
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                click_obj=$(this);
                $("#window_card").dialog({                       
                        position: ["center","center"],
                        width: 600,
                        height: 510,
                        title: 'Управление картой',
                        open: function(eve, ui) { 
						    showelement="<img align=\"left\" id=\"obj_card_class\" src=\""+click_obj.attr("src")+"\">";
							showelement=showelement+"<div align=\"center\" id=\"button_sbros\">В сброс</div>";
							if ((click_obj.attr("value")>=50) && (click_obj.attr("value")<=69)){
								showelement=showelement+"<div align=\"center\" id=\"button_na_stol\">Бросить на стол</div>";
								showelement=showelement+"<div align=\"center\" id=\"button_sell\">Продать</div>";
							}
							
                            $("#window_card").html(showelement);   
							
							$("#button_sell").bind("click",button_sell);		
							$("#button_sbros").bind("click",button_sbros_click);
							$("#button_na_stol").bind("click",button_na_stol_click);
							
							$("#button_sbros, #button_na_stol, #button_sell").hover(
							function(){
								$(this).css("cursor","pointer");
								$(this).css("background","#D2691E");
							 },
							 function(){
								$(this).css("background","#FFCC99");
							 }) 							
											
                                  
                        },   
                        close: function(eve, ui) { 
                            $("#window_card").dialog('destroy');

                        }                                        
                });   
}  

function click_card_enemy(){
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                click_obj=$(this);
                $("#window_card").dialog({                       
                        position: ["center","center"],
                        width: 420,
                        height: 510,
                        title: 'Просмотр карты',                        
                        open: function(eve, ui) { 
                            $(this).html("<img align=\"left\" id=\"obj_card_class\" src=\""+click_obj.attr("src")+"\">");      
                        },   
                        close: function(eve, ui) { 
                            $("#window_card").dialog('destroy');
                        }                                        
                });   
}  

function button_up_click(){
	showwaitdiv();	
	$.ajax({  
		type: "POST", 
		url:  "discard.php",
		data: "send_com="+"1",                        
		cache: false,  
		success: function(html){ 
			$("#window_card").html(html);
			
			$("#button_up").bind("click",button_up_click);
			$("#button_down").bind("click",button_down_click);
			$("#button_vruku").bind("click",button_vruku_click);
			
			$("#button_up, #button_down, #button_vruku").hover(
			function(){
				$(this).css("cursor","pointer");
				$(this).css("background","#D2691E");
			},
			function(){
				$(this).css("background","#FFCC99");
			}) 	
			hidewaitdiv();	 
		}
	});          	 
}	

function button_down_click(){
	showwaitdiv();	
	$.ajax({  
		type: "POST", 
		url: "discard.php",
		data: "send_com="+"2",   
		cache: false,  
		success: function(html){ 
			$("#window_card").html(html);
			$("#button_up").bind("click",button_up_click);
			$("#button_down").bind("click",button_down_click);
			$("#button_vruku").bind("click",button_vruku_click);
			
			$("#button_up, #button_down, #button_vruku").hover(
			function(){
				$(this).css("cursor","pointer");
				$(this).css("background","#D2691E");
			},
			function(){
				$(this).css("background","#FFCC99");
			}) 

			hidewaitdiv();	
		}
	});        	 
}		

function button_vruku_click(){
	showwaitdiv();	
	$.ajax({  
			type: "POST", 
			url:  "discard.php",
			data: "send_com="+"3",   
			cache: false,  
			success: function(html){ 
				for (i=20;i<=29;i++){
						 if (!($("#id_card"+i).length)) { 
								   $("#id_table"+i).html(html);   
																							  
								   $("#id_card"+i).draggable({
									  containment: 'document',
									  revert: 'invalid',
									  opacity: 0.5                                                              
								   }); 
								   
								   $("#id_card"+i).css("z-index","5");
										   
								   $("#id_card"+i).bind("click",click_card); 
																																																												   
								   $("#window_card").dialog("close"); 
								   break;
						 }                                                                
				} 
				hidewaitdiv();	
			}
	});   	 
}		
		
function vote1(){
					$.ajax({  
						  type: "POST", 
						  url:  "victory.php",
						  data: "send_com_victory="+"2",                       
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){ 
							  var data = eval( '(' + json_data + ')' );   
							  $("#vote_window").html(data.text_window);                         
						  }
					});                 
} 
			 
function vote2(){
					$.ajax({  
						  type: "POST", 
						  url:  "victory.php",
						  data: "send_com_victory="+"3",                        
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){ 
							  var data = eval( '(' + json_data + ')' );   
							  $("#vote_window").html(data.text_window);                         
						  }
					});                 
} 

function button_click_help() {
	click_obj=$(this);
	if (click_obj.attr("id")=="button_help2"){
		s_com=2;
	}else{
		s_com=1;
	}
	if ($("#window_message").dialog( 'isOpen' )){$("#window_message").dialog('destroy');}				  
	$("#window_message").dialog({
			  position: ["left","center"],
			  width: 900,
			  height: 500,
			  title: 'Помощник',
			  modal: true,
			  open: function(eve, ui) { 
					$.ajax({  
						type: "POST", 
						url: "help.php", 
						data: "send_com_help="+s_com, 										  
						cache: false,  
						success: function(html){                              
							$("#window_message").html(html); 
							
							$("#button_help1, #button_help2").hover(
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
				  $("#window_message").dialog('destroy');
			  }  
	})     
}
//*********************************MAIN FUNCTION***********************************************		
function main_body_game(){
			flagV=0;

			$.ajax({  
				  type: "POST", 
				  url:  "chat.php",
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
         
             $("#id_card1, #id_card2").draggable({
                containment: 'document',
                revert: 'invalid',
                opacity: 0.5,
                helper: 'clone'
             });

             $(".id_card").draggable({
                containment: 'document',
                revert: 'invalid',
                opacity: 0.5,
                zIndex: 5
             });  
                          
             <!-- Курсор указателем над колодой-->          
             $(".card_batch, #id_card3,").hover(
             function(){
                $(this).css("cursor","pointer");
             },
             function(){
             
             }) 
             //m&m


 
             $("#mess_com").bind("click",send_text_chat);    
             
             $('#mess_text').keyup(function(e) { 
                    if (e.keyCode == 13) {
                          send_text_chat()
                    }
             }) 
        

             $("#button_clear").click(function() {
                 if (($("#id_card10").length) ||($("#id_card11").length) ||($("#id_card12").length) ||($("#id_card13").length) || ($("#id_card14").length) ||($("#id_card15").length) || ($("#id_card16").length) || ($("#id_card17").length) || ($("#id_card18").length) || ($("#id_card19").length)) { 
				 			showwaitdiv();								
                            $.ajax({  
                                type: "POST", 
                                url: "clear_table.php",  
                                data: "send_com_clear="+"1",                      
                                cache: false,  
                                success: function(html){  
									$("#id_card10, #id_card11, #id_card12, #id_card13, #id_card14, #id_card15, #id_card16, #id_card17, #id_card18, #id_card19").remove(); 
									hidewaitdiv();	   
                                }
                            }); 
                    }             
             })      

             $(".pencil").click(function() {
					send_user=$(this).attr("value");
                    $("#mess_text").attr("value"," => "+send_user + ": ");
					$("#mess_text").focus();
             })   			 

             $("#mess_pas, #mess_cube,#mess_boi").click(function() {
                    send_com=$(this).attr("id");
					showwaitdiv();		
                    $.ajax({  
                          type: "POST", 
                          url: "chat.php",  
                          data: "send_com_chat="+send_com,                      
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){ 
							if (json_data.length!==0){
								var data = eval( '(' + json_data + ')' ); 
								$("#mess_place").html(data.chat_text); 
							}		
							hidewaitdiv();			
						  }
                    });              
             })  
	
			$("#mess_end").click(function() {
					showwaitdiv();	
                    send_com=$(this).attr("id");
                    $.ajax({  
                          type: "POST", 
                          url: "next_step.php",  
                          data: "send_com_step="+"1",                      
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){       
								var data = eval( '(' + json_data + ')' );   
								
                                $("#mess_end").css("visibility",data.button_mess_boi);           
								hidewaitdiv();	
                          }
                    });              
             })  
			
			$("#next_step").click(function() {
					showwaitdiv();	
                    send_com=$(this).attr("id");
                    $.ajax({  
                          type: "POST", 
                          url: "next_step.php",  
                          data: "send_com_step="+"2",                      
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){       
								var data = eval( '(' + json_data + ')' );   
								
                                $("#mess_end").css("visibility",data.button_mess_boi);  
								hidewaitdiv();			
                          }
                    });              
             })   
			 
			$("#close_table").click(function() {
					showwaitdiv();	
                    $.ajax({  
                          type: "POST", 
                          url: "close_table.php",  
                          data: "send_com_close="+"1",                      
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){       
								var data = eval( '(' + json_data + ')' );   
								
                                $("#close_table").html(data.text_frame);    
								hidewaitdiv();			
                          }
                    });              
             })   		
			 
			$(".kick").click(function() {
				showwaitdiv();		
				click_obj=$(this);
				$.ajax({  
					type: "POST", 
					url: "kick_user.php",  
					data: "send_com_kick="+"1"+"&id_user="+click_obj.attr("value"),                      
					cache: false,  
					success: function(html){                              
						$("body").append(html);
						hidewaitdiv();	
					}
				});                                                      
             })   
			 //m&m
			$(".help_user_fight").click(function() {						
				click_obj=$(this);
				//user_suggest="1";		
				if ($("#mess_end").css("visibility")=="hidden"){
					if ($("#window_message").dialog( 'isOpen' )){$("#window_message").dialog('destroy');}
                      
                    $("#window_message").dialog({
                        position: ["left","center"],
                        width: 600,
                        height: 160,
                        title: 'Стоимость ваших услуг за помощь в бою',
                        modal: true,
                        open: function(eve, ui) 
						{ 
							$("#window_message").html('Укажите что вы хотите за помощь в бою против монстра: <input id="user_suggest" type="text" size="80" maxlength="150" value="1 сокровище, которое я выбираю первым">');
                        },
						buttons: 
						{
							'Предложить': function(){								
								var user_suggest=$("#user_suggest").attr("value");
								if (user_suggest!==""){
									showwaitdiv();
									$.ajax({  
										type: "POST", 
										url: "help_fight.php",  
										data: "send_com_help_fight="+"1"+"&id_user="+click_obj.attr("value")+"&user_suggest="+user_suggest,                      
										cache: false,  
										success: function(html){
											hidewaitdiv();	
											$("#window_message").dialog('close');
										}
									});	
								}else{
									alert("Строка предложения не может быть пустой");
								}								
							}
						}
					})	
							
				}else{
					showwaitdiv();
					$.ajax({  
						type: "POST", 
						url: "help_fight.php",  
						data: "send_com_help_fight="+"1"+"&id_user="+click_obj.attr("value")+"&user_suggest=0",                      
						cache: false,  
						success: function(html){
							hidewaitdiv();	
						}
					});
				}			
            })  
			//m&m
			$(".change_creator").click(function() {
				showwaitdiv();	
                click_obj=$(this);
				$.ajax({  
						type: "POST", 
						url: "change_creator.php",  
						data: "send_com_change="+"1"+"&id_user="+click_obj.attr("value"),                      
						datatype: 'json_data',
						cache:false, 
						success: function(json_data){					  
							var data = eval( '(' + json_data + ')' ); 					  		  									  
							$('#next_step').css("visibility",data.next_step);
							$('#close_table').css("visibility",data.close_table);
								
							$('.kick').css("visibility",data.kick);
							$('.change_creator').css("visibility",data.change_creator);
							hidewaitdiv();		
						}
				});                                                      
             })    
 
             $("#mess_history").click(function() {
                      if ($("#window_message").dialog( 'isOpen' )){$("#window_message").dialog('destroy');}
                      
                      $("#window_message").dialog({
                              position: ["left","center"],
                              width: 600,
                              height: 400,
                              title: 'История переписки',
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                        type: "POST", 
                                        url: "chat.php",  
                                        data: "send_com_chat="+"3",                      
										datatype: 'json_data',
										cache:false, 
										success: function(json_data){ 
											if (json_data.length!==0){
												var data = eval( '(' + json_data + ')' );                          
                                                $("#window_message").html(data.chat_text_all);                                
											}
										}	
                                    });    
                              },
                              close: function(eve, ui) { 
                                  $("#window_message").dialog('destroy');
                              }  
                      })                                                                   
             })             

			$("#button_help, #button_help1, #button_help2").live("click",button_click_help);
			 
             $(".nick").click(function() {
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                click_obj=$(this);
                $("#window_card").dialog({
                        position: ["center","center"],
                        width: 500,
                        height: 420,
                        title: 'Информация об игроке',
						zIndex: 150,
                        modal: true,
                        open: function(eve, ui) { 
                              $.ajax({  
                                    type: "POST", 
                                    url: "player.php",  
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
             
            $(".level").click(function() {
				if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
				click_obj=$(this);
				$("#window_card").dialog({
					position: ["center","center"],
					width: 200,
					height: 120,
					title: 'Уровень игрока',
					zIndex: 50,
					modal: true,
					open: function(eve, ui) { 
						$.ajax({  
							type: "POST", 
							url: "level.php",  
						    data: "send_com="+"0"+"&id_user="+click_obj.attr("value"),                      
							datatype: 'json_data',
							cache:false, 
							success: function(json_data){		
								var data = eval( '(' + json_data + ')' ); 					                               
								$("#window_card").html(data.content_window);  
								
								$("#u_level_minus").bind("click",u_level_minus_click);  											  
								$("#u_level_plus").bind("click",u_level_plus_click);  
							}
						}); 
                    }, 
                    close: function(eve, ui) { 
                        $("#window_card").dialog('destroy');
                    }
                });              
             })  

             $(".u_class").click(function() {
                      if ($("#other_window").dialog( 'isOpen' )){$("#other_window").dialog('destroy');}
                      if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                      click_obj=$(this);
                      $("#other_window").dialog({
                              position: ["center","center"],
                              width: 450,
                              height: 270,
                              title: 'Класс игрока',
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                          type: "POST", 
                                          url: "u_class.php",  
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
                              position: ["center","center"],
                              width: 450,
                              height: 270,
                              title: 'Раса игрока',
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                          type: "POST", 
                                          url: "race.php",  
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
                              position: ["center","center"],
                              width: 835,
                              height: 550,
                              title: 'Шмотки игрока',
							  zIndex: 150,
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                          type: "POST", 
                                          url: "bonus.php",  
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
                              position: ["center","center"],
                              width: 900,
                              height: 280,
                              title: 'Проклятия наложенные на игрока',
							  zIndex: 150,
                              modal: true,
                              open: function(eve, ui) { 
                                    $.ajax({  
                                          type: "POST", 
                                          url: "curse.php",  
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
			 
            $(".u_gold").click(function() {
				if ($("#other_window").dialog( 'isOpen' )){$("#other_window").dialog('destroy');}
				click_obj=$(this);
				$("#other_window").dialog({
					position: ["center","center"],
					width: 350,
					height: 200,
					title: 'Кошелек игрока',
					zIndex: 150,
					modal: true,
					open: function(eve, ui) { 
							$.ajax({  
								type: "POST", 
								url: "change_gold.php",  
								data: "send_com_gold="+"0"+"&id_user="+click_obj.attr("value"),                      
								datatype: 'json_data',
								cache:false, 
								success: function(json_data){					  
										var data = eval( '(' + json_data + ')' ); 					  
				
										$("#other_window").html(data.content_window);  

										$("#button_change_gold").bind("click",change_gold);  											  
									  
										$("#button_change_gold").hover(
											function(){
												$(this).css("cursor","pointer");
												$(this).css("background","#D2691E");
											},
											function(){
												$(this).css("background","#FFCC99");
											}
										) 	 
								}
							});  
					}, 
					close: function(eve, ui) { 
						$("#other_window").dialog('destroy');
					}                                                                                 
				});  
             })    			 
			 			                                            
             $(".id_card").bind("click",click_card);   

             $(".id_card_class").bind("click",click_card_easy);    
              
             $(".id_card_enemy").bind("click",click_card_enemy);    

             $("#id_card1").click(function() {
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                $("#window_card").dialog({                       
                        position: ["center","center"],
                        width: 400,
                        height: 300,
						zIndex: 150,
                        title: 'Карты дверей', 
						modal: true,						
                        open: function(eve, ui) { 
                              $.ajax({  
                                    type: "POST", 
                                    url: "door.php",  
                                    data: "send_com="+"0",                      
                                    cache: false,  
                                    success: function(html){                              
                                         $("#window_card").html(html);
                                      
                                    }
                              });                                                             
                        },    
                        buttons: {"Перетасовать": function() {
									showwaitdiv();
									$.ajax({  
										type: "POST", 
										url: "door.php", 
										data: "send_com="+"1",                        
										cache: false,  
										success: function(html){ 
											$("#window_card").html(html);
											hidewaitdiv();
										}
									});                                                                                                             
                                 }                                  
                        },
                        close: function(eve, ui) { 
                            $("#window_card").dialog('destroy');
                        }                             
                                 
                });   
             })
             
             $("#id_card2").click(function() {
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                $("#window_card").dialog({                       
                        position: ["center","center"],
                        width: 400,
                        height: 300,
						zIndex: 150,
                        title: 'Карты сокровищ',
						modal: true,
                        open: function(eve, ui) { 							
							$.ajax({  
								type: "POST", 
								url: "loot.php",  
								data: "send_com="+"0",                      
								cache: false,  
								success: function(html){                              
									$("#window_card").html(html); 									
								}
							});                                                             
                        },    
                        buttons: {"Перетасовать": function() {
								showwaitdiv();		
								$.ajax({  
									type: "POST", 
									url: "loot.php", 
									data: "send_com="+"1",                        
									cache: false,  
									success: function(html){ 
										$("#window_card").html(html);
										hidewaitdiv();
									}
								});                                                                                                             
							}                                  
                        },
                        close: function(eve, ui) { 
                            $("#window_card").dialog('destroy');
                        }                             
                                 
                });   
             })
			 					
             $("#box_dump").click(function() {
                if ($("#window_card").dialog( 'isOpen' )){$("#window_card").dialog('destroy');}
                click_obj=$(this);
                $("#window_card").dialog({                       
                        position: ["center","center"],
                        width: 600,
                        height: 620,
						zIndex: 150,
                        title: 'Сброс',
						modal: true,
                        open: function(eve, ui) { 
                              $.ajax({  
                                    type: "POST", 
                                    url:  "discard.php",  
                                    data: "send_com="+"0",                      
                                    cache: false,  
                                    success: function(html){                              
                                        $("#window_card").html(html);
										
										$("#button_up").bind("click",button_up_click);
										$("#button_down").bind("click",button_down_click);
										$("#button_vruku").bind("click",button_vruku_click);
										
										$("#button_up, #button_down, #button_vruku").hover(
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
                                 
                });   
             })
				 
//Ужаснейший кусок кода, описывающий все ячейки на игровом столе	   	            
      $("#id_table10").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,10);
                  }   
        });
      $("#id_table11").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,11);
                  }   
        });
      $("#id_table12").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,12);
                  }   
        });
      $("#id_table13").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,13);
                  }   
        });
      $("#id_table14").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,14);
                  }   
        });
      $("#id_table15").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,15);
                  }   
        });
      $("#id_table16").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,16);
                  }   
        });
      $("#id_table17").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,17);
                  }   
        });
      $("#id_table18").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,18);
                  }   
        });
      $("#id_table19").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,19);
                  }   
        });
      $("#id_table20").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,20);
                  }   
        });
      $("#id_table21").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,21);
                  }   
        });
      $("#id_table22").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,22);
                  }   
        });
      $("#id_table23").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,23);
                  }   
        });
      $("#id_table24").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,24);
                  }   
        });
      $("#id_table25").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,25);
                  }   
        });
      $("#id_table26").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,26);
                  }   
        });
      $("#id_table27").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,27);
                  }   
        });
      $("#id_table28").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,28);
                  }   
        });
      $("#id_table29").droppable({
                  accept: function(d) { 
                          if (d.hasClass("id_card")||(d.attr("id")=="id_card1")||(d.attr("id")=="id_card2")){ 
                              return true;
                          }
                  },
                  hoverClass: "hover",                              
                  drop: function(eve,ui){
                      my_drop(eve,ui,29);
                  }   
        });
					 
//Кусок кода отвечающии за повторяющиеся действия - каждые 4 секунд 
	    setInterval(function()
        {               
			$.ajax({  
				  type: "POST", 
				  url:  "victory.php",
				  data: "send_com_victory="+flagV, 
                  datatype: 'json_data',
                  cache:false, 
                  success: function(json_data){
						var data = eval( '(' + json_data + ')' ); 
						
						if (data.flag_victory==0){//Игра продалжается
							if (flagV==1){										
							    $("#vote_window").html(data.text_window);											 								
							}
						}else if (data.flag_victory==1){//игра прервана для голосования
							  if (flagV==1){										
							        $("#vote_window").html(data.text_window);											 								
							  }else{	
									$("#window_card").dialog('close');
									$("#vote_window").dialog({
											  position: ["left","center"],
											  width: 650,
											  height: 430,
											  title: 'Голосовалка',
											  modal: true,
											  open: function(eve, ui) { 
												    $("#vote_window").html(data.text_window);																										  
											  }, 
											  close: function(eve, ui) { 
											      flagV=0;
												  $("#vote_window").dialog('destroy');
											  }        								  
									});  
									flagV=1;											  
						      }	
							  //Делаем активными кнопки голосовалки
							  $("#vote_pro, #vote_con").hover(
							  function(){
								$(this).css("cursor","pointer");
								$(this).css("background","#D2691E");
							  },
							  function(){
								$(this).css("background","#FFCC99");
							  }) 
							
							  $("#vote_pro").bind("click",vote1); 
							  $("#vote_con").bind("click",vote2); 								  
						}else if (data.flag_victory==2){//Голосование завершенно
							$("#vote_window").html(data.text_window);
						}							
				  }
			});           
           
		   if (flagV!==1){	//Этот кусок кода выполняем если только не открыта голосовалка 
				   $.ajax({  
						  type: "POST", 
						  url:  "chat.php",
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
						  url: 'reload_table.php',
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
						  url: 'reload_user.php',
						  data: "send_com_reload_user="+"1",
						  datatype: 'json_data',
						  cache:false, 
						  success: function(json_data){	
									var data = eval( '(' + json_data + ')' ); 					  
									if (data.flag_game==0){
										$('#next_step').css("visibility",data.next_step);
										$('#close_table').css("visibility",data.close_table);
			
										$('#helper_name1').html(data.helper_name1);
										$('#helper_name2').html(data.helper_name2);
										$('#helper_name3').html(data.helper_name3);
										$('#helper_name4').html(data.helper_name4);
										$('#helper_name5').html(data.helper_name5);
										$('#helper_name6').html(data.helper_name6);
										
										$('#help_user_fight1').css("visibility",data.help_user_fight_vis1);
										$('#help_user_fight2').css("visibility",data.help_user_fight_vis2);
										$('#help_user_fight3').css("visibility",data.help_user_fight_vis3);
										$('#help_user_fight4').css("visibility",data.help_user_fight_vis4);
										$('#help_user_fight5').css("visibility",data.help_user_fight_vis5);
										$('#help_user_fight6').css("visibility",data.help_user_fight_vis6);
																		
										$('#kick2').css("visibility",data.kick_vis2);
										$('#kick3').css("visibility",data.kick_vis3);
										$('#kick4').css("visibility",data.kick_vis4);
										$('#kick5').css("visibility",data.kick_vis5);
										$('#kick6').css("visibility",data.kick_vis6);
									  
										$('#kick2').attr("value",data.kick2);
										$('#kick3').attr("value",data.kick3);
										$('#kick4').attr("value",data.kick4);
										$('#kick5').attr("value",data.kick5);
										$('#kick6').attr("value",data.kick6);
									  
										$('#change_creator2').css("visibility",data.change_creator_vis2);
										$('#change_creator3').css("visibility",data.change_creator_vis3);
										$('#change_creator4').css("visibility",data.change_creator_vis4);
										$('#change_creator5').css("visibility",data.change_creator_vis5);
										$('#change_creator6').css("visibility",data.change_creator_vis6);
									  
										$('#change_creator2').attr("value",data.change_creator2);
										$('#change_creator3').attr("value",data.change_creator3);
										$('#change_creator4').attr("value",data.change_creator4);
										$('#change_creator5').attr("value",data.change_creator5);
										$('#change_creator6').attr("value",data.change_creator6); 	
								
										$('#pencil2').attr("value",data.pencil2);
										$('#pencil3').attr("value",data.pencil3);
										$('#pencil4').attr("value",data.pencil4);
										$('#pencil5').attr("value",data.pencil5);
										$('#pencil6').attr("value",data.pencil6);
									  
										$('#kick2').attr("value",data.kick2);
										$('#kick3').attr("value",data.kick3);
										$('#kick4').attr("value",data.kick4);
										$('#kick5').attr("value",data.kick5);
										$('#kick6').attr("value",data.kick6);					  
									  
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
										location.href="statistic_game.php"; 
									}else if (data.flag_game==2){
										location.href="gamemenu.php"; 
									}		
			
						  }
					}); 
            }         
        },10000);
		
		//Выполняется каждые 30 секунд
		setInterval(function()
		{
			newmessagecheck();
		},30000);
		
		hidewaitdiv();
 }                     
