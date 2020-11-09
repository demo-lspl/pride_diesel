<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Company <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<div class="row justify-content-center">
				<div class="col-sm-6">
				<div class="form-messages error">
					<?php echo validation_errors(); ?>
					<?php if($this->session->flashdata("success")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success")?>
					</div>
					<?php } ?>
				</div>
				
				<?php //print_r($company);//foreach($userdata as $userdatas){$values = $userdatas;}// ?>
				  <!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('user/money_code_issue/').$this->uri->segment(3).'/'.$this->uri->segment(4) ?>">
					<input type="hidden" name="companyId" id="companyId" value="<?php echo $this->uri->segment(3) ?>" />
					<div class="form-group">
						<label for="InputContractId">Contract ID</label>
						<input type="text" name="contractId" class="form-control" id="InputContractId" placeholder="Contract ID" value="416142" readonly />
					</div>
					<!--div class="form-group">
						<label for="masterContractId">Master Contract Id</label>
						<input type="text" name="masterContractId" class="form-control" id="masterContractId" placeholder="Master Contract Id" value="1" readonly />
					</div-->
					<input type="hidden" name="masterContractId" id="masterContractId" value="-1" />					
					<div class="form-group">
						<label for="amount">Amount</label>
						<input type="text" name="amount" class="form-control" id="amount" placeholder="0.00" value="<?php echo set_value('amount', $moneyCode->amount)?>" maxlength="7" />
					</div>
					<!--div class="form-group">
						<label for="feeType">Fee Type</label>
						<input type="text" name="feeType" class="form-control" id="feeType" placeholder="Fee Type" value="0" />
					</div-->
					<input type="hidden" name="feeType" id="feeType" value="0" />					
					<div class="form-group">
						<label for="issuedTo">Issued To</label>
						<input type="text" name="issuedTo" class="form-control" id="issuedTo" placeholder="Issued To" value="<?php echo set_value('cardNumber', $getCompanyName->company_name)?>" readonly />
					</div>
					<div class="form-group">
						<label for="notes">Notes</label>
						<input type="text" name="notes" class="form-control" id="notes" placeholder="Notes" value="<?php echo set_value('notes', $moneyCode->notes)?>">
					</div>
					<div class="form-group">
						<label for="currency">Currency</label>
						<input type="text" name="currency" class="form-control" id="currency" placeholder="Currency" value="USD" readonly />
					</div>						

					<div class="form-group">
					  <button type="submit" class="btn btn-primary">Submit</button>
					</div>				  
					<!-- /.card-body -->
				  </form>
				  </div>
				  </div>
			</div>
		</div>		
	</section>
</div>