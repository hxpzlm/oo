$(document).ready(function(){
	//点击确认入库
	$('.returns-afs').click(function(){
		if($(this).hasClass('icon-queren')){
				$(this).next().show();
		}
		if($(this).hasClass('icon-quxiao')){
				$(this).next().show();
		}
			//确定
			$('.returns-but1').click(function(){
				$(this).parents('.returns-box').hide();
			})	
			//取消、关闭
			$('.returns-boxt1 i,.returns-but2').click(function(){
				$('.returns-box').hide();
			})
	})
})
