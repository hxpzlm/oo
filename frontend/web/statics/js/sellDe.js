$(document).ready(function(){
	//点击确认入库
	$('.sellDe-deli').click(function(){
		if($(this).hasClass('icon-queren')){
			$(this).siblings('.sellDe-delbox').show();	
			
//			$('.icon-queren').each(function(index){
				//确定

				//取消
				$('.sd-delboxt3-s,.sd-delboxt1_1 i').click(function(){
					$(this).parents('.sellDe-delbox').hide();
				})
//			});
		}else{
			$(this).siblings('.sellDe-delbox2').show();
			
				//确定
				$('.delbox2t2-x').click(function(){
					$(this).css('background','#000000')
					$(this).parents('td').siblings('.sellDe-va1').text('');
					$(this).parents('td').prev('.sellDe-va2').text('');
					$(this).parents('.sellDe-delbox2').hide();
					$(this).parents('.sellDe-del').find('.sellDe-deli').removeClass('icon-quxiao').addClass('icon-queren');
				})	
				//取消
				$('.delbox2t2-s,.sd-delboxt1_1 i').click(function(){
					$(this).parents('.sellDe-delbox2').hide();
				})
		}
		
	})
	
})