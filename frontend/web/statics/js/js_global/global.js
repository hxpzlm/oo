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
	$('.seeks-xl').click(function(){
		if($('.seeks-xl label').text()=='▼'){
			$('.seeks-xl label').text('▲');
		}else{
			$('.seeks-xl label').text('▼');
		}
		$('.seeks-box').stop().toggle('slow');
	})
})


//2016/7/6新增

	//弹窗方法
	function showWindow(obj){
		$('.iDiv').show();
		$(obj).show();
	}
	
	//关闭弹窗方法
	function closeWindow(obj){
		$('.iDiv').hide();
		$(obj).hide();
	}
