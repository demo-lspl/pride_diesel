<?php $userSessDetails = $this->session->userdata('userdata'); ?>
<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Card <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<div class="row justify-content-center">
				<div class="col-md-6">
				<div class="form-messages error">
					<?php //echo validation_errors(); ?>
					<?php if($this->session->flashdata("success_msg")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success_msg")?>
					</div>
					<?php } ?>
				</div>
				
				<?php //foreach($carddata as $carddatas){$values = $carddatas;}//print_r($values); ?>
				  <!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('card/edit/').$this->uri->segment(3) ?>">
					<div class="">
					  <div class="form-group">
						<label for="InputCardNumber">Card Number</label>
						<input type="text" name="card_number" class="form-control" id="InputCardNumber" placeholder="Card Number" value="<?php echo set_value('card_number', $card->card_number);?>" readonly />
						<div class="error-single"><?php echo form_error('card_number'); ?></div>
					  </div>
					  <input type="hidden" name="cardToken" class="form-control" id="InputCardToken" placeholder="Card Token" value="<?php echo set_value('cardToken', $card->cardToken);?>" readonly />
					  <?php if(empty($card->cardToken)){ ?>
					  <div class="form-group">
						<!--label for="InputCardLimit">Card Limit</label>
						<!--input type="text" name="card_limit" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" id="InputCardLimit" placeholder="Card Limit" value="<?php echo set_value('card_limit', $card->card_limit);?>"-->
						<!-- Trigger the modal with a button -->
						<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal">Add Limit</button>
					  </div>
					  <?php } ?>
						<?php if(!empty($this->uri->segment(3)) && $userSessDetails->role == 'admin'){ ?>
						
					  <div class="form-group">
						<label for="InputCardLimit"><?php echo (empty($card->cardToken))? "EFS Policy Number" : "Account Id"; ?></label>
						<input type="text" name="policy_number" class="form-control" id="InputPolicyNumber" placeholder="Policy Number" value="<?php echo set_value('policy_number', $card->policy_number);?>" readonly />
					  </div>
						<?php }else{ ?>
							<input type="hidden" name="policy_number" value="<?= $card->policy_number ?>" />
						<?php } ?>					  
					  <div class="form-group">
						<label for="InputCardLimit">Card Status: </label>
						<?php //if(empty($card->cardToken)){ ?>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="card_status" value="1" <?php 
								echo set_value('card_status', $card->card_status) == 1 ? "checked" : ""; ?> />Active
						  </label>
						</div>
						<?php if(empty($card->cardToken)){ ?>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="card_status" value="0" <?php 
								echo set_value('card_status', $card->card_status) == 0 ? "checked" : ""; ?> />Inactive
						  </label>
						</div>
						<div class="form-check-inline disabled">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="card_status" value="2" <?php 
								echo set_value('card_status', $card->card_status) == 2 ? "checked" : ""; ?> />Hold
						  </label>
						</div>
						<?php }else{ ?>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="card_status" value="3" <?php 
								echo set_value('card_status', $card->card_status) == 3 ? "checked" : ""; ?> />Block
						  </label>
						</div>
						<div class="form-check-inline disabled">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="card_status" value="4" <?php 
								echo set_value('card_status', $card->card_status) == 4 ? "checked" : ""; ?> />Clear
						  </label>
						</div>
						<?php } ?>						
						<?php //}else{ ?>
						<!--div class="form-check-inline disabled">
						  <label class="form-check-label">
						  <strong class="text-success">
							<?php if($card->card_status == 1){echo "Active";}
									elseif($card->card_status == 3){echo "Blocked";} 
									elseif($card->card_status == 4){echo "Clear";} 
									elseif($card->card_status == 5){echo "Fraud";} 
									elseif($card->card_status == 6){echo "Lost";} 
									elseif($card->card_status == 7){echo "Stolen";} 
									elseif($card->card_status == 8){echo "Permanent Blocked";} 
								?>
							</strong>		
						  </label>
						</div-->						
						<?php //} ?>						
					  </div>					  
					  <?php if(!empty($this->uri->segment(3)) && $userSessDetails->role == 'admin'): ?>
					  <div class="form-group">
						<label for="InputCardLimit">Assign Card to user</label>

						<select name="card_assign" class="form-control selectledgergroup" style="width: 100%;">
							<option value="">-- Select Company --</option>
							<?php foreach($getuserdata as $usernames): ?>
								<option <?php if($card->company_id == $usernames->id){echo "selected";} ?> value="<?php echo $usernames->id; ?>"><?php echo ucwords($usernames->company_name); ?></option>
							<?php endforeach; ?>
						</select>							
					  </div>
					  <?php endif; ?>

					  <?php if(!empty($companydrivers) && $userSessDetails->role == 'company'): ?>
					  <div class="form-group">
						<label for="InputCardLimit">Assign Card to Driver</label>

						<select name="card_assign_driver" class="form-control selectledgergroup" style="width: 100%;">
							<option value="">-- Select Driver --</option>
							<?php foreach($companydrivers as $usernames): ?>
								<option <?php if(!empty($card->driver_id) && $card->driver_id == $usernames->id){echo "selected";} ?> value="<?php echo $usernames->id; ?>"><?php echo ucwords($usernames->name); ?></option>
							<?php endforeach; ?>
						</select>							
					  </div>
					  <?php endif; ?>					  
					  <?php if(empty($card->cardToken)){ ?>					  
					  <div class="form-group">
						<label for="InputCardPIN">Card PIN</label>
						<input type="text" name="card_pin" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" id="InputCardPIN" placeholder="Card PIN" value="<?php echo set_value('card_pin', $card->card_pin);?>">
					  </div>
					  <div class="form-group">
						<label for="InputCardPIN">Unit Number</label>
						<input type="text" name="unit_number" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" id="InputUnitNumber" placeholder="Unit Number" value="<?php echo set_value('unit_number', $card->unit_number);?>" />
					  </div>
					  <div class="form-group">
						<label for="InputCardPIN">Odometer</label>
						<!--input type="text" maxlength="5" name="odometer" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control" id="InputOdometer" placeholder="Odometer" value="<?php //echo set_value('odometer', $card->odometer);?>" /-->
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="odometer" value="1" <?php 
								echo set_value('odometer', $card->odometer) == 1 ? "checked" : ""; ?> />On
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="odometer" value="0" <?php 
								echo set_value('odometer', $card->odometer) == 0 ? "checked" : ""; ?> />Off
						  </label>
						</div>						
					  </div>					  
					<?php } ?>	
					<div class="form-group">
					  <button type="submit" class="btn btn-primary">Submit</button>
					</div>				  
					</div>
					<!-- /.card-body -->

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Card Limit</h4>	  
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
		  <div class="form-group">
			<label>Limit ID:</label>
			<select class="form-control" name="limit_id">
				<option value="">--Select Product--</option>
				<option value="ULSD">ULSD</option>
				<option value="ULSD">ULSR</option>
				<option value="DEF">DEFD</option>
			</select>
		  </div>
		  <div class="form-group">
			<label>Amount:</label>
			<input type="text" name="limit_amount" class="form-control" />
		  </div>
		  <div class="form-group">
			<label>Auto Rollover:</label>
			 <div class="form-check">
			  <label class="form-check-label">
				<input type="checkbox" class="form-check-input" value="true" name="sunday">Sunday
			  </label>
			</div>
			<div class="form-check">
			  <label class="form-check-label">
				<input type="checkbox" class="form-check-input" value="true" name="monday">Monday
			  </label>
			</div>
			<div class="form-check">
			  <label class="form-check-label">
				<input type="checkbox" class="form-check-input" value="">Tuesday
			  </label>
			</div>
			<div class="form-check">
			  <label class="form-check-label">
				<input type="checkbox" class="form-check-input" value="">Wednesday
			  </label>
			</div>
			<div class="form-check">
			  <label class="form-check-label">
				<input type="checkbox" class="form-check-input" value="">Thursday
			  </label>
			</div>
			<div class="form-check">
			  <label class="form-check-label">
				<input type="checkbox" class="form-check-input" value="">Friday
			  </label>
			</div>
			<div class="form-check">
			  <label class="form-check-label">
				<input type="checkbox" class="form-check-input" value="">Saturday
			  </label>
			</div> 
		  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Finish</button>
      </div>
    </div>

  </div>
</div>
				  </form>
				  </div>
				  </div>
			</div>
		</div>		
	</section>
</div>
