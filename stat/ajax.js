//Создание объекта
function getXmlHttp(){
 var xmlhttp;
 try {
 xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
 } catch (e) {
 try {
 xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
 } catch (E) {
 xmlhttp = false;
 }
 }
 if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
 xmlhttp = new XMLHttpRequest();
 }
 return xmlhttp;
}

//Обработка запроса POST
function postajax(page,params,content_id,wait_id) {
	
	loadElem = document.getElementById(wait_id);
	loadElem.style.display = 'inline'; // показать бар "выполнение запроса"
    //Объект для запроса к серверу
    var req = getXmlHttp() 
	//Имя запрашиваемой странички
	var docum = page+'?rnd='+Math.random()+'&'+params;
	//В какое место странички будем загружат
	var contentElem = document.getElementById(content_id);
	contentElem.style.display = 'inline';
	//Открываем соединение	
    req.open('POST', docum, true);
	req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	// onreadystatechange активируется при получении ответа сервера
    req.onreadystatechange = function() {  
 
        if (req.readyState == 4) { 
		
		// если запрос закончил выполняться
            if(req.status == 200) { // если статус 200 (ОК) - выдать ответ пользователю
				var resText = req.responseText;
				//Для того чтобы все это работало в ебучем FireFox'e
				var ua = navigator.userAgent.toLowerCase();
				if (ua.indexOf('gecko') != -1) {  // Если браузер Mozilla, или Firefox, или Netscape
					
				  var range = contentElem.ownerDocument.createRange();
						 range.selectNodeContents(contentElem);	 // Очистим внутренности нашего блока
						 range.deleteContents();
				  var fragment = range.createContextualFragment(resText); //<– dies here	// Теперь наполним необходимым контентом
						contentElem.appendChild(fragment);
				}  else  {		 // Для остальных браузеров
				  contentElem.innerHTML = resText;
				}
				loadElem.style.display = 'none';
            }
            else
			{
				content.innerHTML = 'Невозможно выполнить запрос!';
			}
        }
 
    }
 
    // объект запроса подготовлен: указан адрес и создана функция onreadystatechange
    // для обработки ответа сервера
 
    req.send(params);  // отослать запрос
}
