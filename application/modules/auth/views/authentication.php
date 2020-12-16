<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Pride Diesel | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/fontawesome-free/css/all.min.css">
  
  <link rel="stylesheet" href="<?php echo base_url()?>assets/css/style.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <!--link rel="stylesheet" href="<?php echo base_url()?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css"-->
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url()?>assets/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo base_url() ?>">Pride Diesel</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">You are only one step away from your dashboard, enter the key below.</p>
	  <div class="alert alert-danger error_msg"></div>
	  <div class="alert alert-success error_msg"></div>
	<?php if($this->session->flashdata("error")){?>
	<div class="alert alert-danger">      
		<?php echo $this->session->flashdata("error")?>
	</div>
	<?php } ?>
	<?php if($this->session->flashdata("success")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success")?>
	</div>
	<?php } ?>
      <form method="post">
		<input type="hidden" name="uid" value="<?= $userId ?>" />
		<input type="hidden" name="uemail" value="<?= $userEmail ?>" />
		<input type="hidden" name="uip" value="<?= $userIP ?>" />
        <div class="input-group mb-3">
          <input type="text" name="verification_code" class="form-control" placeholder="Verification Code" value="<?php if(!empty($verificationCode)){echo $verificationCode;} ?>" required autocomplete="off" />
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <input type="submit" class="btn btn-primary btn-block verification-continue" value="Continue">
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mt-3 mb-1">
        <a href="<?php echo base_url('auth') ?>">Login</a> | <a class="resend-code code-resend-btn">Resend</a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
<script>		var site_url = '<?php echo base_url(); ?>';
					
	</script>
<!-- jQuery -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo base_url()?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url()?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url()?>assets/dist/js/adminlte.min.js"></script>
<script src="<?php echo base_url()?>assets/modules/auth/js/script.js"></script>
</body>
</html>
