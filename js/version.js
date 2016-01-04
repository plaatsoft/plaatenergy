function check_version(current_version) {

   if (window.XMLHttpRequest) {
	// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
   } else {
	// code for IE6, IE5
	xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
   }
	
   xmlhttp.onreadystatechange=function() {
      //if (xmlhttp.readyState==4 && xmlhttp.status==200) 
      {
		
        console.log(xmlhttp.responseText.length);
        var obj = JSON.parse(xmlhttp.responseText);
	document.getElementById("version").innerHTML = obj.PlaatSolar; 
      }

   }
	
   xmlhttp.open('GET',  'http://www.plaatsoft.nl/service/version');
   //xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded' );
   console.log("send");
   xmlhttp.send();
}
