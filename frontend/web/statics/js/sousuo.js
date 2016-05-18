/**
 * Created by Administrator on 2016/4/13.
 */

function searchNew(keywords,url,tag){
    if(keywords==null || keywords=="null" || keywords=="undefined" || keywords==""){
        keywords = "&";
    }
    $.ajax({
        url: url,
        dataType: "json",
        type: "POST",
        async: false,
        data:{keyword:keywords},
        contentType: "application/json; charset=utf-8",
        success: function(result){
            var html = "";
            var name = result.goods;
            for(var i=0; i< name.length; i++){
                var searchVo = name[i];
            html+="<li class='seeks01_float_main'>"+searchVo.name+"</li>";
            }
            $('"'+tag+'"').html(html);
        }
    });
}

function searchKong(url,tag){
    $.ajax({
        url: url,
        dataType: "json",
        type: "POST",
        async: false,
        data:{keyword:$('input[name="goods_name"]').val()},
        contentType: "application/json; charset=utf-8",
        success: function(result){
            var html = "";
            var name = result.goods;
            for(var i=0; i< name.length; i++){
                var searchVo = name[i];
                html+="<li class='seeks01_float_main'>"+searchVo.name+"</li>";
            }
            $('"'+tag+'"').html(html);
        }
    });
}



