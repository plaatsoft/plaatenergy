function link(value)
{
	var form = document.forms['plaatenergy'];
	var newInput = document.createElement('input');
	newInput.setAttribute('type','hidden');
	newInput.setAttribute('name','token');
	newInput.setAttribute('value',value);
	form.appendChild(newInput);		
		
	form.submit();
}

function show_confirm(question, token) 
{ 
	if (confirm(question)==true) 
	{
		link(token); 
	} 
}
