$(document).ready(function(){
	//删除弹窗
	var sc;
	$('.orders-infosc').click(function(){	
        delFn();
	})
	
	//出库弹窗
	$('.other_libr').click(function(){
		otherFn();
	});
	
    //关闭弹窗方法
	$(".orders-sct3 span").click(function(){
        sc.close();
    });
	
    //删除方法
    function delFn(){
    	$('.orders-sct1').text('提示').append('<i class="iconfont closepanels">&#xe608;</i>');	
    	$('.orders-sct2').text('您确定要删除这条记录吗？删除后不可恢复！');
    	$('.orders-sct3 .or_btn').text('删除');
    	$('.orders-sct3 .orders-sct3qx').text('取消');
    	sc = $(".orders-sc").bPopup();
    }
    
    //出库方法
    function otherFn(){
    	$('.orders-sct1').text('提示').append('<i class="iconfont closepanels">&#xe608;</i>');	
    	$('.orders-sct2').text('确定出库吗？');
    	$('.orders-sct3 .or_btn').text('确定');
    	$('.orders-sct3 .orders-sct3qx').text('取消');
    	sc = $(".orders-sc").bPopup();	
    }

	//日历切换改变中文
    $.datepicker.setDefaults( $.datepicker.regional[ "zh-TW" ] );
    $.datepicker.regional['zh-TW'] = {
	    //closeText: '关闭',
	    prevText: '<上月',
	    nextText: '下月>',
	    //currentText: '今天',
	    monthNames: ['一月','二月','三月','四月','五月','六月',
	    '七月','八月','九月','十月','十一月','十二月'],
	    monthNamesShort: ['一月','二月','三月','四月','五月','六月',
	    '七月','八月','九月','十月','十一月','十二月'],
	    dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
	    dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
	    dayNamesMin: ['日','一','二','三','四','五','六'],
	    weekHeader: '周',
	    dateFormat: 'yy/mm/dd',
	    firstDay: 1,
	    isRTL: false,
	    showMonthAfterYear: true,
	    yearSuffix: '年'};
    $.datepicker.setDefaults($.datepicker.regional['zh-TW']);
	


});

//年月输入框绑定下拉框
$(".start_rl").datepicker({
	dateFormat: 'yy-mm-dd',
    onSelect:function(dateText,inst){
       $(".end_rl").datepicker("option","minDate",dateText);
    }
});
	
$(".end_rl").datepicker({
	dateFormat: 'yy-mm-dd',
	onSelect:function(dateText,inst){
    	$(".start_rl").datepicker("option","maxDate",dateText);
	}
});