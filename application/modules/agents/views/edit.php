<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Agent <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<div class="row justify-content-center">
				<div class="col-sm-6">
				<div class="form-messages error">
					<?php //echo validation_errors(); ?>
					<?php if($this->session->flashdata("success")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success")?>
					</div>
					<?php } ?>
				</div>

				<!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('agents/edit/').$this->uri->segment(3) ?>" onkeydown="return event.key != 'Enter';">
					<div class="">					  
					  <div class="form-group">
						<label for="InputCompanyName">Name</label>
						<input type="text" name="company_name" class="form-control" id="InputCompanyName" placeholder="Name" value="<?php echo set_value('company_name', $company->company_name)?>">
						<div class="error-single"><?php echo form_error('company_name'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputAddress">Address</label>
						<input type="text" name="address" class="form-control" id="InputAddress" placeholder="Address" value="<?php echo set_value('address', $company->address)?>">
						<div class="error-single"><?php echo form_error('address'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputCity">City</label>
						<input type="text" name="city" class="form-control" id="InputCity" placeholder="City" value="<?php echo set_value('city', $company->city)?>">
					  </div>
					  <div class="form-group">
						<label for="InputProvince">Province</label>
						<input type="text" name="province" class="form-control" id="InputProvince" placeholder="Province" value="<?php echo set_value('province', $company->province)?>">
					  </div>
					  <div class="form-group">
						<label for="InputPostalCode">Postal Code</label>
						<input type="text" name="postal_code" class="form-control" id="InputPostalCode" placeholder="Postal Code" value="<?php echo set_value('postal_code', $company->postal_code)?>">
					  </div>					  
					  <div class="form-group">
						<label for="InputEmail">Email Address</label>
						<input type="email" name="company_email" class="form-control" id="InputEmail" placeholder="Enter email" value="<?php echo set_value('company_email', $company->company_email)?>">
					  </div>					  					  
					  <div class="form-group">
						<label for="InputPassword">Password</label>
						<input type="password" name="company_password" class="form-control" id="InputPassword" placeholder="Password" value="" >
						<div class="error-single"><?php echo form_error('company_password'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputMatchPassword">Confirm Password</label>
						<input type="password" name="confirm_company_password" class="form-control" id="InputMatchPassword" placeholder="Confirm Password">
					  </div>
						<input type="hidden" name="role" value="admin" />

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