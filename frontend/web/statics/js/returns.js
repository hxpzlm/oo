$(document).ready(function(){
	//点击确认入库
	$('.returns-afs').click(function(){
		if($(this).hasClass('icon-queren')){
				$(this).next().show();
		}
			//确定
			$('.returns-but1').click(function(){
				var now = new Date();
		        var year = now.getFullYear(); //获取年份
		        var month = now.getMonth() + 1;   //获取月份
		        var date = now.getDate();     //获取日期
		        var time =year+'-'+month+'-'+date;
				
				$(this).parents('td').siblings('.returns-va1').text('是');
				$(this).parents('td').siblings('.returns-va2').text(time);
				$(this).parents('.returns-box').hide();
				$(this).parents('.returns-af').find('.returns-afs').removeClass('icon-queren').addClass('icon-quxiao');
			})	
			//取消、关闭
			$('.returns-boxt1 i,.returns-but2').click(function(){
				$('.returns-box').hide();
			})
	})
})
