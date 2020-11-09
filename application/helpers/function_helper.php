<?php
/**/
function is_loggedin(){
	if(isset($_SESSION['loggedin'])){
		return true;
	}else{
		redirect(base_url('auth'), 'refresh');
	}
}