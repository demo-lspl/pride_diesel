<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Ledger</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div class="form-messages error"><?php //echo validation_errors();//echo($messages['errors']); ?></div>
					<?php if($this->session->flashdata("success")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success")?>
					</div>
					<?php } ?>				
				<form method="post" action="<?php echo base_url('account/ledger_edit/').$this->uri->segment(3) ?>">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="ledgerName">Ledger Name</label>
								<!--input type="text" name="name" class="form-control" id="ledgerName" value="<?= set_value('name', $ledgerDetails->name); ?>" placeholder="Ledger Name" /-->
								<?php //print_r($regCompanies) ?>
								<select name="name" class="form-control selectledgergroup">
									<?php foreach($regCompanies as $company){ ?>
									<option <?php if($ledgerDetails->name == $company->id){echo 'selected';}?>  value="<?= $company->id ?>"><?= $company->company_name ?></option>
									<?php } ?>
								</select>								
							</div>

							<div class="form-group">
								<label for="phone">Phone</label>
								<input type="text" name="phone" class="form-control" id="phone" value="<?= set_value('phone', $ledgerDetails->phone); ?>" placeholder="Phone" />
								<div class="error-single"><?php echo form_error('phone'); ?></div>
							</div>	


							<!--div class="form-group">
								<label for="registrationType">Registration Type</label>
								<input type="text" name="registration_type" class="form-control" id="registrationType" value="<?= set_value('registration_type', $ledgerDetails->registration_type); ?>" placeholder="Registration Type" />
							</div-->													
						</div>
						<div class="col-md-6">
							<!--div class="form-group">
								<label for="ledgerGroup">Account Group</label>
								<select name="account_group_id" class="form-control selectledgergroup">
									<?php foreach($ledgerGroups as $ledgerGroup){ ?>
									<option <?php if($ledgerDetails->account_group_id == $ledgerGroup->id){echo 'selected';}?> <?php if(!empty($ledgerField->account_group_id) && $ledgerField->account_group_id == $ledgerGroup->id): echo 'selected'; endif; ?> value="<?= $ledgerGroup->id ?>"><?= $ledgerGroup->name ?></option>
									<?php } ?>
								</select>
							</div-->
							
							<div class="form-group">
								<label for="email">Email</label>
								<input type="text" name="email" class="form-control" id="email" value="<?= set_value('email', $ledgerDetails->email); ?>" placeholder="Email" />
								<div class="error-single"><?php echo form_error('email'); ?></div>
							</div>

							<div class="form-group">
								<label for="openingBalance">Opening Balance</label>
								<input type="text" name="opening_balance" class="form-control" id="openingBalance" value="<?= set_value('opening_balance', $ledgerDetails->opening_balance); ?>" placeholder="Opening Balance" />
								<div class="error-single"><?php echo form_error('opening_balance'); ?></div>
							</div>							
						</div>
						
						<div class="col-md-12">
							<div class="form-group">
								<input type="submit" name="" class="btn btn-primary" value="Create" />
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