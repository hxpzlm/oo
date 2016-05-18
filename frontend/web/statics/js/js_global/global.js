$(document).ready(function(){
	bot();
	//下拉菜单
	$('.one_md').each(function(index){
		$(this).mouseover(function(){
			$('.navboxs').hide();
			$('.navboxs').eq(index).show();
			$('.nav').css('margin-bottom','40px');
		}).mouseout(function(){
			$('.navboxs').hide();
			$('.shows').show();
			bot()
		});
	});
	
	//判断当前页是否已有二级菜单
	function bot(){
		if($('.navboxs').hasClass('shows')){
			$('.nav').css('margin-bottom','40px');
		}else{
//			alert()
			$('.nav').css('margin-bottom','0');
		}
	}
	//更多搜索
	// $('.seeks-xl').click(function(){
	// 	if($('.seeks-xl label').text()=='▼'){
	// 		$('.seeks-xl label').text('▲');
	// 		$('.seeks-box').append("<input type='hidden' name='ison' value='1'>");
	// 	}else{
	// 		$('.seeks-xl label').text('▼');
	// 		$('input[name="ison"]').attr("value","0");
	// 	}
	// 	$('.seeks-box').stop().toggle('slow');
	// })
	$('.seeks-box').hide();
	if($.cookie('state')=="show"){
		$('.seeks-box').show();
		$('.seeks-xl label').text('▲');
	}else{
		$('.seeks-box').hide();
		$('.seeks-xl label').text('▼');
	}

	$('.seeks-xl').click(function(){
		if($.cookie('state')=="show"){
			$.cookie('state','hide');
			$('.seeks-box').hide();
			$('.seeks-xl label').text('▼');
		}else{
			$.cookie('state','show');
			$('.seeks-box').show();
			$('.seeks-xl label').text('▲');
		}
	});

})
