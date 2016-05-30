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

function link(value) {
  var form = document.forms['plaatenergy'];
  var newInput = document.createElement('input');
  newInput.setAttribute('type','hidden');
  newInput.setAttribute('name','token');
  newInput.setAttribute('value',value);
  form.appendChild(newInput);		
		
  form.submit();
}

/*
** ---------------------
** THE END
** ---------------------
*/


