$(function(){
	$("ul.navigation > li:has(ul) > a").append('<div class="arrow-bottom"></div>');
	$("ul.navigation > li ul li:has(ul) > a").append('<div class="arrow-right"></div>');
});
			
function changeColor()
{
	document.getElementById("menu_top100").style.background = "#333";
}