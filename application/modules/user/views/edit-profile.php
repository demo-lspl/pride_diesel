<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Account Details</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<div class="row justify-content-center">
				<div class="col-sm-6">
				<div class="form-messages error">
					<div class=""><?php echo validation_errors(); ?></div>
					<?php if($this->session->flashdata("success_msg")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success_msg")?>
					</div>
					<?php } ?>
				</div>
				
				  <!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('user/edit_profile/').$this->uri->segment(3) ?>">
					<div class="">
					  <div class="form-group">
						<label for="InputFirstName">Comapany Name</label>
						<input type="text" name="company_name" class="form-control" id="InputFirstName" placeholder="First Name" value="<?php echo isset($getDetails->company_name)?$getDetails->company_name:'';?>">
					  </div>
					  <div class="form-group">
						<label for="InputLastName">Address</label>
						<input type="text" name="address" class="form-control" id="InputLastName" placeholder="Last Name" value="<?php echo isset($getDetails->address)?$getDetails->address:'';?>">
					  </div>				  
					  <div class="form-group">
						<label for="InputEmail">Email</label>
						<input type="email" name="company_email" class="form-control" id="InputEmail" placeholder="Enter email" value="<?php echo isset($getDetails->company_email)?$getDetails->company_email:'';?>">
					  </div>
					  <div class="form-group">
						<label for="InputPassword">Password</label>
						<input type="password" name="company_password" class="form-control" id="InputPassword" placeholder="Password" value="<?php echo set_value('company_password') ?>">
					  </div>
					  <div class="form-group">
						<label for="InputMatchPassword">Confirm Password</label>
						<input type="password" name="confirm_company_password" class="form-control" id="InputMatchPassword" placeholder="Confirm Password">
					  </div>				  
					  <!--div class="form-group">
						<label for="exampleInputFile">File input</label>
						<div class="input-group">
						  <div class="custom-file">
							<input type="file" class="custom-file-input" id="exampleInputFile">
							<label class="custom-file-label" for="exampleInputFile">Choose file</label>
						  </div>
						  <div class="input-group-append">
							<span class="input-group-text" id="">Upload</span>
						  </div>
						</div>
					  </div>
					  <div class="form-check">
						<input type="checkbox" class="form-check-input" id="exampleCheck1">
						<label class="form-check-label" for="exampleCheck1">Check me out</label>
					  </div-->
					<div class="form-group">
					  <button type="submit" class="btn btn-primary">Submit</button>
					</div>				  
					</div>
					<!-- /.card-body -->


				  </form>
				  </div>
				  </div>
			</div>
		</div>		
	</section>
</div>