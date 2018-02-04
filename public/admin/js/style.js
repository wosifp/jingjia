$(function(){


	//弹窗
	$(".tc").height($(document).height());
	$(".tc , .qi_con_xx img").click(function(){
		$(".tc_con , .tc").hide();
	});




});

//账户选择
$(document).ready(function(){

	$("#firstpane .menu_body:eq(0)").show();
	$("#firstpane h3.menu_head").click(function(){
		$(this).addClass("current").next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow");
		$(this).siblings().removeClass("current");
	});

	$("#firstpane .menu_body a").click(function(){
		$(this).addClass("current2").next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow");
		$(this).siblings().removeClass("current2");
	});
	
	$("#secondpane .menu_body:eq(0)").show();
	$("#secondpane h3.menu_head").mouseover(function(){
		$(this).addClass("current").next("div.menu_body").slideDown(500).siblings("div.menu_body").slideUp("slow");
		$(this).siblings().removeClass("current");
	});
	

	$(".qi_p_2").click(function(){
		$(this).addClass("current").next("div.menu_list").slideToggle(300).siblings("div.menu_list").slideUp("slow");
	});

	






});
