<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Gas Station <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

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
				<form role="form" method="post" action="<?php echo base_url('gas_station/edit/').$this->uri->segment(3) ?>">
					<div class="">
					  <div class="form-group">
						<label for="InputName">Name</label>
						<input type="text" name="name" class="form-control" id="InputName" placeholder="Name" value="<?php echo set_value('name', $gas_stations->name)?>">
						<div class="error-single"><?php echo form_error('name'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputAddress">Address</label>
						<input type="text" name="address" class="form-control" id="InputAddress" placeholder="Address" value="<?php echo set_value('address', $gas_stations->address)?>">
						<div class="error-single"><?php echo form_error('address'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputCity">City</label>
						<input type="text" name="city" class="form-control" id="InputCity" placeholder="City" value="<?php echo set_value('city', $gas_stations->city)?>">
						<div class="error-single"><?php echo form_error('city'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputState">State</label>
						<input type="text" name="state" class="form-control" id="InputState" placeholder="State" value="<?php echo set_value('state', $gas_stations->state)?>">
						<div class="error-single"><?php echo form_error('state'); ?></div>
					  </div>
					  <div class="form-group">
						<div class="row">
						<div class="col-md-6">
						<label for="InputLatitude">Latitude</label>
						<input type="text" name="latitude" class="form-control" id="InputLatitude" placeholder="Latitude" value="<?php echo set_value('latitude', $gas_stations->latitude);?>">
						</div>
						<div class="col-md-6">
						<label for="InputLongitude">Longitude</label>
						<input type="text" name="longitude" class="form-control" id="InputLongitude" placeholder="Longitude" value="<?php echo set_value('longitude', $gas_stations->longitude);?>">
						</div>						
						</div>						
					  </div>					  
					  <div class="form-group">
						<label>Services Available</label><br />
						<?php !empty($gas_stations->services)?$service = json_decode($gas_stations->services):$service = '';//isset($values->state)?$service = json_decode($values->services):''; //print_r($service[0]);?>
						<label for="InputShower" class="checkbox-inline"><input type="checkbox" name="services[]" class="" id="InputShower" <?php if(!empty($service) && in_array('shower', $service) == true):echo 'checked';endif;?> value="shower"> Shower</label>
						<label for="InputDinner" class="checkbox-inline"><input type="checkbox" name="services[]" class="" id="InputDinner" <?php if(!empty($service) && in_array('dinner', $service) == true):echo 'checked';endif;?> value="dinner"> Dinner</label>
						<label for="InputLunch" class="checkbox-inline"><input type="checkbox" name="services[]" class="" id="InputLunch" <?php if(!empty($service) && in_array('lunch', $service) == true):echo 'checked';endif;?> value="lunch"> Lunch</label>						
					  </div>
					  <div class="form-group">
						<label>Package Price Exclude</label><br />
						<?php //isset($values->state)?$service = json_decode($values->services):''; //print_r($service[0]);?>
						<label for="ExcludeYes" class="checkbox-inline"><input type="radio" name="exclude_pack_price" class="" id="ExcludeYes" <?php echo set_value('exclude_pack_price', $gas_stations->exclude_pack_price) == 1 ? "checked" : "";//if(!empty($service) && in_array('shower', $service) == true):echo 'checked';endif;?> value="1"> Yes</label>
						<label for="ExcludeNo" class="checkbox-inline"><input type="radio" name="exclude_pack_price" class="" id="ExcludeNo" <?php echo set_value('exclude_pack_price', $gas_stations->exclude_pack_price) == 0 ? "checked" : "";//if(!empty($service) && in_array('dinner', $service) == true):echo 'checked';endif;?> value="0"> No</label>						
					  </div>					  
					  <div class="form-group">
						<label for="InputContactNumber">Contact Number</label>
						<input type="text" name="contact_number" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" id="InputContactNumber" placeholder="Contact Number" value="<?php echo set_value('contact_number', $gas_stations->contact_number)?>">
					  </div>					  

					<div class="form-group">
					  <button type="submit" class="btn btn-primary">Submit</button>
					</div>				  
					</div>

				  </form>
				  </div>
				  </div>
			</div>
		</div>		
	</section>
</div>