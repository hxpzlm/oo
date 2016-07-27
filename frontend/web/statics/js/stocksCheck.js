$(document).ready(function(){
	//仓库盘点页面
	//点击确认(图标)弹窗
	$('.sure_icon').click(function(){
		showWindow($('.window_1'));
	});

	//点击删除(图标)弹窗
	$('.del_icon').click(function(){
		showWindow($('.window_1'));
	});


	//点击确认、取消(按钮)关闭弹窗
	$('.window_1 #button_c').click(function(){
		closeWindow($('.window_1'));
		$('.window_1 p').text('确认该盘点单？确认后将无法修改！');
	});

	//仓库盘点新建页面










});








