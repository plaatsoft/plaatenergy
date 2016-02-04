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
			
		var current = document.getElementById("version").innerHTML;
		if (current!=obj.PlaatEnergy) {
			document.getElementById("version").innerHTML = current + ' <div id="new" style="display:inline;color:#e0440e">('+obj.PlaatEnergy+' available)</div>'; 
		}
   }
}
	
xmlhttp.open('POST',  'http://www.plaatsoft.nl/service/version.php', true);
xmlhttp.setRequestHeader('Content-type','application/x-www-form-urlencoded' );
xmlhttp.send("ip="+ip);

/*
** ---------------------
** THE END
** ---------------------
*/
