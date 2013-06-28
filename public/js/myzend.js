function showUp()
{
	$('#up_button').animate({opacity:0.6},200);
}

function hideUp()
{
	$('#up_button').animate({opacity:0},200);
}

function goUp()
{
	jQuery('html, body').animate({scrollTop: "0px"});
}