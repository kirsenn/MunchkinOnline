opacitymax = 1;
opacitystep = 0;

function showan()
{
	element = document.getElementById('writemsgform');
	if(element.style.opacity<1)
	{
		opacitystep = opacitystep + 0.1;
		if(element.style.display=='none'){element.style.display='inline';}
		element.style.opacity = opacitystep;
		var opacitysetFunc = 'showan()';
		if(opacitystep<1){setTimeout(opacitysetFunc,20)};
		opacitymax = 1;
	}
}
	
	function hidean()
{
	element = document.getElementById('writemsgform');
	if(element.style.opacity>0)
	{
		var opacityis = element.style.opacity;
		opacitymax = opacitymax - 0.2;
	
		element.style.opacity = opacitymax;
		if(opacitymax>0){setTimeout('hidean()',25)};
		if(opacitymax<0){element.style.display='none';opacitystep = 0.1;}
	}
}
