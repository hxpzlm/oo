$(document).ready(function(){
	
	//采购入库点击删除
	$('.more-dian').click(function(){
		$(this).next().show();
	})
	$('.moreboxt1 i').click(function(){
		$('.stocksD-morebox').hide();
	})
})