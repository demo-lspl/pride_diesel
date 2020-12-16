<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Tax</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
		<div class="row justify-content-center">
			<div class="col-md-6">
				<div class="form-messages error"><?php //echo validation_errors();//echo($messages['errors']); ?>
					<?php if($this->session->flashdata("success")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success")?>
					</div>
					<?php } ?>
				</div>	
				<form method="post" class="form-horizontal" action="<?php echo base_url('account/edit_tax/').$this->uri->segment(3) ?>">
					<div class="form-group">
						<label for="federal_tax">Federal Tax</label>
						<br />
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="checkbox" class="form-check-input federaltax-checkbox" name="isfederaltax"   <?= ($tax->isfederaltax == 1)?'checked':'';?> value="1" <?php //echo $tax->isfederaltax//echo set_value('isfederaltax', $card->isfederaltax) == 1 ? "checked" : ""; ?> />Is Federal Tax <?php //echo $tax->isfederaltax ?>
						  </label>
						</div>						
					</div>	
					
					<!--div class="form-group">
						<input type="text" name="federal_tax" class="form-control" id="federal_tax" value="<?= set_value('federal_tax', $tax->federal_tax) ?>" placeholder="Federal Tax Rate" <?= (set_checkbox('isfederaltax', '1')) ? '':'readonly';?> />
					</div-->				
					<div class="form-group">
						<label for="state">Province/State</label>
						<select name="state" class="form-control selectledgergroup " style="width: 100%;" >
							<option value="">Select Province/State</option>
							<?php $company_names = array('ON'=>'Ontario', 'QC'=>'Quebec', 'AB'=>'Alberta', 'BC'=>'British Columbia', 'MB'=>'Manitoba', 'NB'=>'New Brunswick', 'NL'=>'Newfoundland and Labrador', 'NS'=>'Nova Scotia', 'PE'=>'Prince Edward Island', 'SK'=>'Saskatchewan', 'AL'=>'Alabama', 'AK'=>'Alaska', 'AZ'=>'Arizona', 'AR'=>' Arkansas', 'CA'=>'California', 'CO'=>'Colorado', 'CT'=>'Connecticut', 'DE'=>'Delaware', 'FL'=>'Florida', 'GA'=>'Georgia', 'HI'=>'Hawaii', 'ID'=>'Idaho', 'IL'=>'Illinois', 'IN'=>'Indiana', 'IA'=>'Iowa', 'KS'=>'Kansas', 'KY'=>'Kentucky[E]', 'LA'=>'Louisiana', 'ME'=>'Maine', 'MD'=>'Maryland', 'MA'=>'Massachusetts[E]	', 'MI'=>'Michigan', 'MN'=>'Minnesota', 'MS'=>'Mississippi', 'MO'=>'Missouri', 'MT'=>'Montana', 'NE'=>'Nebraska', 'NV'=>'Nevada', 'NH'=>'New Hampshire', 'NJ'=>'New Jersey', 'NM'=>'New Mexico', 'NY'=>'New York', 'NC'=>'North Carolina', 'ND'=>'North Dakota', 'OH'=>'Ohio', 'OK'=>'Oklahoma', 'OR'=>'Oregon', 'PA'=>'Pennsylvania[E]', 'PQ'=>'PQ', 'RI'=>'Rhode Island[F]', 'SC'=>'South Carolina', 'SD'=>'South Dakota', 'TN'=>'Tennessee', 'TX'=>'Texas', 'UT'=>'Utah', 'VT'=>'Vermont', 'VA'=>'Virginia[E]', 'WA'=>'Washington', 'WV'=>'West Virginia', 'WI'=>'Wisconsin', 'WY'=>'Wyoming'); ?>	
							<?php foreach($company_names as $key=>$company_name_val): ?>
								<option <?php if($key == $tax->state){echo "selected";} ?> value="<?php echo $key; ?>"><?php echo $company_name_val;//echo set_value('state', $tax->state); ?></option>
							<?php endforeach; ?>
						</select>
						<div class="error-single"><?php echo form_error('state'); ?></div>
					</div>
					<div class="form-group">
						<label for="tax_type">Tax Type</label>
						<select name="tax_type" class="form-control selectledgergroup " style="width: 100%;" >
							<option value="">Select Tax Type</option>
							<?php 
							$taxTypes = array('pct'=>'PCT', 'pft'=>'PFT', 'fet'=>'FET', 'gst'=>'GST', 'pst'=>'PST', 'qst'=>'QST');
							?>	
							<?php foreach($taxTypes as $key=>$taxType): ?>
								<option <?php if($key == $tax->tax_type){echo "selected";} ?> value="<?php echo $key; ?>"><?php echo $taxType;//echo set_value('state', $tax->state); ?></option>
							<?php endforeach; ?>
						</select>
						<div class="error-single"><?php echo form_error('tax_type'); ?></div>
					</div>
					<div class="form-group">
						<label for="InputCardLimit">Percentage/Value </label>

						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input tax-percent" name="is_per_is_val" value="1" <?= ($tax->is_per_is_val == 1)?'checked':'';?> />Percentage
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input tax-value" name="is_per_is_val" value="2" <?= ($tax->is_per_is_val == 2)?'checked':'';?> />Amount
						  </label>
						</div>
						<div class="error-single"><?php echo form_error('is_per_is_val'); ?></div>
					</div>					
					<div class="form-group">
						<label for="tax_rate">Tax</label>
						<input type="text" name="tax_rate" class="form-control" id="tax_rate" value="<?= set_value('tax_rate', $tax->tax_rate) ?>" placeholder="Tax Rate"  />
						<div class="error-single"><?php echo form_error('tax_rate'); ?></div>
					</div>

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