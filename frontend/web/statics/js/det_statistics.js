$(document).ready(function(){
	//初始化日历，只显示年月
	$('.year_rl').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-MM',
        showButtonPanel: true,
        onClose: function(dateText, inst) {
             // var month = $("#ui-datepicker-div .ui-datepicker-month option:selected").val();//得到选中的月份值
             // var year = $("#ui-datepicker-div .ui-datepicker-year option:selected").val();//得到选中的年份值
             // $('.year_rl').val(year+'-'+(parseInt(month)+1));//给input赋值，其中要对月值加1才是实际的月份
            var year = inst.selectedYear;
            var month = parseInt(inst.selectedMonth+1);
            if(month >9 ){
                $('.year_rl').val(year+''+month);
            }else{
                $('.year_rl').val(year+'0'+month);
            }
        }
   });
	
	//日历切换改变中文
    $.datepicker.setDefaults( $.datepicker.regional[ "zh-TW" ] );
    $.datepicker.regional['zh-TW'] = {
	    closeText: '确定',
	    prevText: '<上月',
	    nextText: '下月>',
	    currentText: '今天',
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
    
    //动态计算合并多少列
    $('.tt_width th').eq(0).prop('colspan',$('.k_class').length);
    $('.tt_width th').eq(1).prop('colspan',$('.c_class').length);
    $('.tt_width th').eq(2).prop('colspan',$('.x_class').length);
    $('.tt_width th').eq(3).prop('colspan',$('.m_class').length);
    
    
    //动态赋表格宽度
    var sum=0;
    for(var i=0;i<$('.jh_width th').length;i++){
    	sum=i;
    }
	$('.table_right').width(sum*150);
});
