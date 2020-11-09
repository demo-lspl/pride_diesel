<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Driver <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

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
					<?php if($this->session->flashdata("success_msg")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success_msg")?>
					</div>
					<?php } ?>
				</div>
				
				<!-- Driver Add/Edit form start -->
				<form role="form" method="post" action="<?php echo base_url('driver/edit/').$this->uri->segment(3) ?>">
					  <div class="form-group">
						<label for="InputName">Name</label>
						<input type="text" name="name" class="form-control" id="InputName" placeholder="Name" value="<?php echo set_value('name', $driver->name)?>">
						<div class="error-single"><?php echo form_error('name'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputAddress">Address</label>
						<input type="text" name="address" class="form-control" id="InputAddress" placeholder="Address" value="<?php echo set_value('address', $driver->address)?>">
						<div class="error-single"><?php echo form_error('address'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputState">State</label>
						<input type="text" name="state" class="form-control" id="InputState" placeholder="State" value="<?php echo set_value('state', $driver->state)?>">
						<div class="error-single"><?php echo form_error('state'); ?></div>
					  </div>					  
					  <div class="form-group">
						<label for="InputCountry">Country</label>
						<input type="text" name="country" class="form-control" id="InputCountry" placeholder="Country" value="<?php echo set_value('country', $driver->country)?>">
						<div class="error-single"><?php echo form_error('country'); ?></div>
					  </div>

					  <div class="form-group">
						<label for="InputPostalCode">Postal/Zip Code</label>
						<input type="text" name="postal_code" class="form-control" id="InputPostalCode" placeholder="Postal/Zip Code" value="<?php echo set_value('postal_code', $driver->postal_code)?>">
						<div class="error-single"><?php echo form_error('postal_code'); ?></div>
					  </div>					  
					  <div class="form-group">
						<label for="InputEmail">Email</label>
						<input type="text" name="email" class="form-control" id="InputEmail" placeholder="Email" value="<?php echo set_value('email', $driver->email)?>">
						<div class="error-single"><?php echo form_error('email'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputContactNumber">Phone Number</label>
						<input type="text" maxlength="11" onkeypress='return event.charCode >= 48 && event.charCode <= 57' name="phone" class="form-control" id="InputContactNumber" placeholder="Phone Number" value="<?php echo set_value('phone', $driver->phone)?>">
						<div class="error-single"><?php echo form_error('phone'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputUnitNumber">Unit Number</label>
						<input type="text" maxlength="15" onkeypress='' name="unit_number" class="form-control" id="InputUnitNumber" placeholder="Unit Number" value="<?php echo set_value('unit_number', $driver->unit_number)?>">
						<div class="error-single"><?php echo form_error('unit_number'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="LicenceNumber">Licence Number</label>
						<input type="text" maxlength="15" onkeypress='' name="licence_number" class="form-control" id="LicenceNumber" placeholder="Licence Number" value="<?php echo set_value('licence_number', $driver->licence_number)?>">
						<div class="error-single"><?php echo form_error('unit_number'); ?></div>
					  </div>					  
					<?php $userSessDetails = $this->session->userdata('userdata'); if($userSessDetails->role == 'admin'){ ?>  
					  <div class="form-group">
						<label for="InputSelectCompany">Assign Company</label>
						<select name="company_id" class="form-control select2" id="InputSelectCompany">
								<option value="">Select Company</option>
							<?php foreach($companies as $company): ?>
								<option <?php if($company->id == $driver->company_id){echo "selected";} ?> value="<?php echo $company->id; ?>"><?php echo ucwords($company->company_name); ?></option>
							<?php endforeach; ?>
						</select>
					  </div>
					<?php }else{?>
						<input type="hidden" name="company_id" value="<?php echo $userSessDetails->id ?>" />	
					<?php }?>	

					<div class="form-group">
					  <button type="submit" class="btn btn-primary">Submit</button>
					</div>				  
				  </form>
				  </div>
				  </div>
			</div>
		</div>		
	</section>
</div>