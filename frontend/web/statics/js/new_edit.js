$(document).ready(function(){
	calcHeight();
	delBtn();
	//点击插入一条数据
	$('.add_btn').click(function(){	
		var $html = $('<div class="input_addbox"><input type="text" class="autocomplete_s"/>'+
		'<p class="speci">规格</p><p class="brand">品牌</p>'+
		'<input type="text" class="autocomplete_n number_n" placeholder="采购单号"/>'+
		'<input type="text" class="autocomplete_d number_d" placeholder="数量"/>'+
		'<p class="dw_dom">单位</p><img src="img/dj_btn.png" class="dj_btn" /><div class="clear"></div></div>');
		$('.input_box').append($html);
		
		autoComplete($html.find('.autocomplete_s'));
		delBtn();
		calcHeight();
	});
	
	//点击删除按钮删除数据方法
	function delBtn(){
		$('.dj_btn').on('click',function(){
			$(this).parents('.input_addbox').remove();
			calcHeight();
		});	
	}
	
	//动态计算“商品元素”高度
	function calcHeight(){
		$('.left_h').height($('.input_box').height()).css('line-height',$('.input_box').height()+'px');	
	}
	
	//绑定自动补全
	autoComplete($('.autocomplete_s'));
	function autoComplete($input){
		$input.autocomplete({
			minLength:0,
			source:["c++", "java", "php", "coldfusion", "javascript", "asp"],
			select:function(event,ui){
				//此处虚拟数据填充,后台据实际情况而定
				$(this).nextAll('.speci').text('100/g');
				$(this).nextAll('.brand').text('泰山牌');
				$(this).nextAll('.number_n').val('123456');
				$(this).nextAll('.number_d').val('5');
				$(this).nextAll('.dw_dom').text('100');
			}
		});
		
		$input.focus(function(){
            if($(this).val() == ""){
                $input.autocomplete("search", "");
            }
    	});
	}
});