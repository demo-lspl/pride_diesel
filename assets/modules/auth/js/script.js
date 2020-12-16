// Regenarate verification Code	 
$(document).ready(function () {

$(document).on('click','.verification-continue',function(e){	
		e.preventDefault();
		
		var uid = $('input[name=uid]').val();
		var uemail = $('input[name=uemail]').val();			
		var uip = $('input[name=uip]').val();			
		var verification_code = $('input[name=verification_code]').val();			
		$.ajax({
		url: site_url + 'auth/validate_ver_code/',
		type: 'post',
		beforeSend: function(){},
		data: {uid:uid, verification_code:verification_code, uip:uip},
		success: function (response) {
			//console.log(response);
			
			if(response == 'timeexpire'){
				//alert('Token Expired');
				$(".alert-danger.error_msg").html('Verification code expired, kindly resend.').show().delay(2000).hide(1000);
				return false;
			}else if(response == 'dontmatch'){
				//alert("Doesn't match");
				//$('.card-body').append('<div class="alert alert-danger">Verification code does not match.</div>');
				$(".alert-danger.error_msg").html('Verification code does not match.').show().delay(2000).hide(1000);
				return false;
			}else if(response == 'loggedin'){
				location.href = site_url+'dashboard';
			}
		}
	});			

});

$(document).on('click','.resend-code',function(e){
		e.preventDefault();
		var uid = $('input[name=uid]').val();
		var uemail = $('input[name=uemail]').val();
		$.ajax({
		url: site_url + 'auth/resend_verification_email/',
		type: 'post',
		beforeSend: function(){},
		data: {uid:uid, uemail:uemail},
		success: function (response) {
			if(response == 'newtokensent'){
				$(".alert-success.error_msg").html('Verification code sent successfully.').show().delay(2000).hide(1000);
			}				
		}
	});			

});
});
