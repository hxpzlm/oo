$(document).ready(function(){
	//根据实际情况锁定高度
	$('.left_box').height($('.lb_box').height()).css({
		'line-height':$('.lb_box').height()+'px',
	});
	
	
	//控制开关按钮
	$('.openshut_btn').change(function(){
		showHidePanel();
	});
	
	//默认加载时，判断当前预警按钮是否开启
	showHidePanel();
	function showHidePanel(){
		var Bools = $(".openshut_btn").attr('w');
		if (Bools == '1') {
			$('.iDivs').show();
			$(".openshut_btn").attr('w',0);
		} else{
			$('.iDivs').hide();
			$(".openshut_btn").attr('w',1);
		}	
	}
	


	// $('.warning_btn').change(function(){
    //
	// 	var $this = $(this).parent('td').nextAll('td');
	// 	var Bools = $(this).attr('w');
	// 	if(Bools == '1'){
	// 		$this.find('input').attr('disabled',true);
	// 		$this.find('select').attr('disabled',true);
	// 		$(this).attr('w',0);
	// 	}else{
	// 		$this.find('input').attr('disabled',false);
	// 		$this.find('select').attr('disabled',false);
	// 		$(this).attr('w',1);
	// 	}
	// });
	
	//默认加载时，判断是否为预警状态
	// disabledBtn();
	// function disabledBtn(){
	// 	for(var i=0;i<$('.warning_btn').length;i++){
	// 		var $this = $('.warning_btn').eq(i).parent('td').nextAll('td');
	// 		var Bools = $('.warning_btn').eq(i).attr('w');
	// 		if(Bools == '1'){
	// 			$this.find('input').attr('disabled',false);
	// 			$this.find('select').attr('disabled',false);
    //
	// 		}else{
	// 			$this.find('input').attr('disabled',true);
	// 			$this.find('select').attr('disabled',true);
	// 		}
	// 	}
	// }
	
});