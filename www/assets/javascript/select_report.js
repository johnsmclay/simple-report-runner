$(document).ready(function()
{
	
	$('#reportList li').each(function() 
	{
		$(this).hover(function() 
		{
			$(this).stop(false,true).animate(
				{
					marginLeft	: "10px",
					marginRight	: "-10px",
					borderRight : "3px solid #000000",
					borderLeft : "3px solid #000000"
				}, 200);
		}, function ()
		{
			$(this).stop(false,true).animate(
				{
					marginLeft	: "0",
					marginRight	: "0",
					border		: "none"
				}, 200);
		});
		
		$(this).click(function() 
		{
			console.log($(this).children().first().attr('id'));
		});
	});

});
