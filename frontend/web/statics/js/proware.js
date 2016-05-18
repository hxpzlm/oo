$(document).ready(function(){
	//点击确认入库
	$('.sellDe-deli').click(function(){
		if($(this).hasClass('icon-queren')){
			$(this).next().show();	
		}
			//确定
			$('.delbox-but').click(function(){
				$(this).parents('.sellDe-delbox').hide();
				$(this).parents('.sellDe-deli').removeClass('icon-queren').addClass('icon-quxiao');
			})	
			//取消、关闭
			$('.delboxt1 i,.delbox-but,.delbox-but2').click(function(){
				$('.sellDe-delbox').hide();
			})

	})
	
})