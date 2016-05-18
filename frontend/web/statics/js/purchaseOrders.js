$(document).ready(function(){
	//删除弹窗
	var sc;
	$('.orders-infosc').click(function(){	
        sc = $(".orders-sc").bPopup();
	})
	$(".orders-sct1 i,.orders-sct3 span").click(function(){
        sc.close();
    });
})
