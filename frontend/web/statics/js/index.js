$(document).ready(function(){
	
	//tab切换
	$('.top_l_t span').click(function(){
		tabFn($(this));
	});
	
	$('.down_l_l span').click(function(){
		tabFn($(this));	
	});
	

	//点击显示弹窗
	
	$('.icon_cai').click(function(){
		showPanel('.panel_1');
	});
	
	$('.icon_pan').click(function(){
		showPanel('.panel_1');
		$('.panel_1 p').text('确认该盘点单？确认后将无法修改!');
	});
	
	$('.icon_chu').click(function(){
		showPanel('.panel_1');
		$('.panel_1 p').text('确认出库？');
	});
	
	$('.icon_tui').click(function(){
		showPanel('.panel_2');
	});
	
	$('.icon_xiao').click(function(){
		showPanel('.panel_3');
	});
	
	
	//点击取消，关闭弹窗
	$('.cancel').click(function(){
		closePanel('.panel_1,.panel_2,.panel_3,.panel_4,.panel_5');
		$('.panel_1 p').text('确认将该商品采购入库？');
	});
	
	$('.sure').click(function(){
	//点击确定，执行后台事件
		
		closePanel('.panel_1,.panel_2,.panel_3,.panel_4,.panel_5');
	});
	
	
	//tab切换公共方法
	function tabFn(obj){
		obj.parent('.list_js').find('span').removeClass('border_link');
		obj.addClass('border_link');
		obj.parents('.box_parent').find('.top_l_js').removeClass('show');
		obj.parents('.box_parent').find('.top_l_js').eq(obj.index()).addClass('show');
		
	};
	
	//弹窗方法
	function showPanel(obj){
		$('.iDiv').show();
		$(obj).show();
	}
	
	//关闭弹窗方法
	function closePanel(obj){
		$('.iDiv').hide();
		$(obj).hide();
	}
	
	//下拉菜单
	
	var  $Add= $('.top_r_box ul li:gt(6)')
	$Add.hide();
	$('.top_r_d').click(function(){
		$Add.show();
		$('.top_r_d').hide();
		$('.top_r_box').css('border-width','1px');
	});
	
//	$('.top_r_d').click(function(){
//		var $html = '<li><span class="iconfont top_r_icon">&#xe614;</span><div class="top_r_main">'+
//					'<p>2016-05-25 预警：</p>库存不足：商品中英文名称 规格（仓库名称一中库存数量：X件） ！</div><div class="clear"></div></li>';
//			for(var i=0;i<5;i++){
//				$('.top_r ul').append($html);	
//			};	
//			$('.top_r_d').hide();
//			$('.top_r_box').css('border-width','1px');
//	});
	
	//自动补全
	$('.autocomplete_bind').autocomplete({
		minLength:0,
		source:['java','javascript','c++','c#','asp','jsp','php','jquery'],
	});
	
	$('.autocomplete_bind').focus(function(){
		$(this).autocomplete("search", "");
	});
	
	
});