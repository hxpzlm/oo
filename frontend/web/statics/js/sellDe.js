$(document).ready(function(){
    //点击确认入库
    $('.sellDe-deli').click(function(){
        if($(this).hasClass('icon-queren')){
            $(this).siblings('.sellDe-delbox').show();

//			$('.icon-queren').each(function(index){
            //确定

            //取消
            $('.sd-delboxt3-s,.sd-delboxt1_1 i').click(function(){
                $(this).parents('.sellDe-delbox').hide();
            })
//			});
        }else{
            $(this).siblings('.sellDe-delbox2').show();

            //确定
            $('.delbox2t2-x').click(function(){
                $(this).css('background','#000000')
                $(this).parents('td').siblings('.sellDe-va1').text('');
                $(this).parents('td').prev('.sellDe-va2').text('');
                $(this).parents('.sellDe-delbox2').hide();
                $(this).parents('.sellDe-del').find('.sellDe-deli').removeClass('icon-quxiao').addClass('icon-queren');
            })
            //取消
            $('.delbox2t2-s,.sd-delboxt1_1 i').click(function(){
                $(this).parents('.sellDe-delbox2').hide();
            })
        }

    })

//批量出库弹窗交互
    //点击批量出库按钮弹窗
    $('.seeks-x2').click(function(){
        showWindow($('.window_1'));
    });
    //批量出库弹窗获取路径
    $('.window_1_file').change(function(){
        $('.window_1_text').val($(this).val());
    });
    //批量出库导入按钮点击交互
    /*$('.button_1').click(function(){
        closeWindow($('.window_1'));
        showWindow($('.window_2'));
        var animate_1 = setTimeout(function(){
            $('.window_2').hide();
        },1000);
        var animate_2 = setTimeout(function(){
            $('.window_3').show();
        },1000);
    });*/
    //关闭批量出库弹窗
    $('.icon,.window_3 button').click(function(){
        closeWindow($('.window_1,.window_3'));
    });

})