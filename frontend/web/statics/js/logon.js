$(document).ready(function(){
	$('.boxlf-but').click(function(){
	var names=$('.name').val();
	var pasds=$('.pswd').val();
	var yzm=$('.logon-yzm').val()
		if(names==''){
			$('.boxlfts1').text('用户名不能为空')
		}else if(pasds==''){
			$('.boxlfts2').text('密码不能为空')
		}else if(yzm ==''){
			$('.boxlfts3').text('验证码不能为空')
		}
	})
})
