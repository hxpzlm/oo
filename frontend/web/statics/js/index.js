/**
 * Created by Administrator on 2016/5/6.
 */
$(document).ready(function(){
    $(".subject_top_rightbox_btn").each(function(){
            $(this).click(function(){
                $(this).parent().find('.subject_top_rightbox').show();
            });
    });

    $(".subject_top_right li ul li input[type = 'button'],.subject_top_rightbox li a").click(function(){
        $(this).parents(".subject_top_rightbox").hide();
    })

    $(".subject_flaotBtn").each(function(){
        $(this).click(function(){
            $(this).parents(".subject_top_rightbox").prevAll(".subject_top_rightbox_btn").html("&#xe611;");
        })
    })
})