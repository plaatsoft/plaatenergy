/* 
**  ===========
**  PlaatEnergy
**  ===========
**
**  Created by wplaat
**
**  For more information visit the following website.
**  Website : www.plaatsoft.nl 
**
**  Or send an email to the following address.
**  Email   : info@plaatsoft.nl
**
**  All copyrights reserved (c) 2008-2016 PlaatSoft
*/

if (window.XMLHttpRequest) {
	// code for IE7+, Firefox, Chrome, Opera, Safari
	xmlhttp=new XMLHttpRequest();
} else {
	// code for IE6, IE5
	xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
}
	
xmlhttp.onreadystatechange=function() {
   if (xmlhttp.readyState==4 && xmlhttp.status==200) 
   {		
		var obj = JSON.parse(xmlhttp.responseText);
		var latest = parseFloat(obj.PlaatEnergy)
		console.log("latest version = ["+latest+"]");
		var current = parseFloat(document.getElementById("version").innerHTML);
		console.log("current version = ["+current+"]");
		if (current<latest) {
			document.getElementById("upgrade").innerHTML = 'PlaatEnergy v'+latest+' available!'; 
		}
   }
}
	
xmlhttp.open('POST',  'https://service.plaatsoft.nl', true);
xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded' );
xmlhttp.send('product=PlaatEnergy&version='+parseFloat(document.getElementById("version").innerHTML));

/*
** ---------------------
** THE END
** ---------------------
*/
