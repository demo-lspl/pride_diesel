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
				<div class="col-sm-10">
				<div class="form-messages error">
					<?php //echo validation_errors(); ?>
					<?php if($this->session->flashdata("success_msg")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success_msg")?>
					</div>
					<?php } ?>
				</div>

				<!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('user/edit/').$this->uri->segment(3) ?>" onkeydown="return event.key != 'Enter';">
				<div class="row">
					<div class="col-lg-6 col-6 ">
					  <div class="form-group">
						<label for="InputCompanyType">US Account Type</label>
						<select name="company_type" class="form-control select2" style="width: 100%;">
								<option value="">--Account Type--</option>
							<?php foreach($companyType as $companyTypes):?>
								<option <?php echo ($company->company_type == $companyTypes->id) ? 'selected':''?> value="<?= $companyTypes->id ?>"><?= $companyTypes->company_type ?></option>
							<?php endforeach;?>
						</select>
						<div class="error-single"><?php echo form_error('company_type'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputCompanyType">CA Account Type</label>
						<select name="company_type_ca" class="form-control select2" style="width: 100%;">
								<option value="">--Account Type--</option>
							<?php foreach($companyType as $companyTypes):?>
								<option <?php echo ($company->company_type_ca == $companyTypes->id) ? 'selected':''?> value="<?= $companyTypes->id ?>"><?= $companyTypes->company_type ?></option>
							<?php endforeach;?>
						</select>
						<div class="error-single"><?php echo form_error('company_type_ca'); ?></div>
					  </div>					  
					  <!-- 	**********Sales Executive *****-->
					  <div class="form-group">
						<label for="InputCompanyType">Sales Executive</label>
						<select name="sales_person" class="form-control select2" style="width: 100%;">
								<option value="">--Sales Executive--</option>
								<?php foreach($salesPersons as $salesPersonsRows):?>
								<option <?php echo ($company->sales_person == $salesPersonsRows->id) ? 'selected':''?> value="<?= $salesPersonsRows->id ?>"><?= $salesPersonsRows->name ?></option>
								<?php endforeach;?>
						</select>
						<div class="error-single"><?php echo form_error('sales_person'); ?></div>
					  </div>					  
					  <div class="form-group">
						<label for="InputCompanyName">Company Name</label>
						<input type="text" name="company_name" class="form-control" id="InputCompanyName" placeholder="Company Name" value="<?php echo set_value('company_name', $company->company_name)?>">
						<div class="error-single"><?php echo form_error('company_name'); ?></div>
					  </div>
					  <div class="form-group">
						<label for="InputCustomerId">Customer Id<sup>(Required only for Husky Customer)</sup></label>
						<input type="text" name="customer_id" class="form-control" id="InputCustomerId" placeholder="Customer Id" value="<?php echo set_value('customer_id', $company->customer_id)?>">
					  </div>					  
					  <div class="form-group">
						<label for="InputCardLimit">Fix Price Items: </label>

						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="checkbox" class="form-check-input fix_price" name="fix_price" value="1" <?php 
								echo set_value('fix_price', $company->fix_price) == 1 ? "checked" : ""; ?> />
						  </label>
						</div>
					
					  </div>
					<div class="form-group fix-product-div" style="display:none;">
						<div class="invoice-edit-block">
							<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap no-padding-left no-padding-right">
								
								<div class="panel panel-default">

									<div class="panel-body goods_descr_wrapper">			
										<div class="item form-group add-ro" >
											<div class="col-md-12 input_descr_wrap label-box mobile-view2 no-padding-left no-padding-right">
												<div class="col-md-6 col-xs-12 item form-group">
													<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product</label>
												</div>		
												<div class="col-md-6 col-xs-12 item form-group">
													<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12">Amount</label>
												</div>	
											</div>
											<?php if(empty($company->fix_cost_data)){ ?>
											<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box no-padding-left no-padding-right">		
												<div class="col-md-6 col-sm-12 col-xs-12 form-group">															
													<label class="col-md-6 col-sm-12 col-xs-12" for="matrialname">Product<span class="required">*</span></label>
													<select name="product_id[]" class="select2 form-control product-select2" width="100%" >
														<option value="">Select Product</option>
														<?php 
														if(!empty($products)){ ?>
														<?php foreach($products as $productRows): ?>
															<option <?php /*echo $driverNames->id ."==". $invoiceDetails->driver_id;*///if($driverNames->id == $invoiceDetails->driver_id){echo "selected";} ?> value="<?php echo $productRows->product_name; ?>"><?php echo ucwords($productRows->product_name); ?></option>
														<?php endforeach; ?>	
														<?php }?>										
													</select>
												</div>

												<div class="col-md-6 col-sm-12 col-xs-12 form-group input_descr_wrap">
													<label class="col-md-12 col-sm-12 col-xs-12" for="tax">Amount<span class="required">*</span></label>
													<input type="text" name="amount[]" onkeypress='return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46' class="form-control col-md-1 fix-product_amt" placeholder="Amount" >
												</div>									
											</div>
											
											<div class="col-sm-12 btn-row"><button class="btn btn-primary add_more_fix_price_product" type="button">Add</button></div>															
							<?php  } ?>
							<?php if($company->fix_cost_data != ''){
								$fixCostProduct = json_decode($company->fix_cost_data);
								if(!empty($fixCostProduct)){
									$i = 0;
									foreach($fixCostProduct as $fixCostProductDetails){ 
								?>
												<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box no-padding-left no-padding-right <?php if($i==0){ echo 'btn-3 ';}if($i==1){ echo 'scend-tr ';}else{ echo 'edit-row1 scend-tr';}?>">
															
												<div class="col-md-6 col-sm-12 col-xs-12 form-group">															
													<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Driver Name <span class="required">*</span></label>
													<select name="product_id[]" class="select2 form-control product-select2" width="100%" >
														<option value="">Select Product</option>
														<?php 
														if(!empty($products)){ ?>
														<?php foreach($products as $productRows): ?>
															<option <?php /*echo $driverNames->id ."==". $invoiceDetails->driver_id;*/if($fixCostProductDetails->fix_cost_product == $productRows->product_name){echo "selected";} ?> value="<?php echo $productRows->product_name; ?>"><?php echo ucwords($productRows->product_name); ?></option>
														<?php endforeach; ?>	
														<?php }?>										
													</select>										
													
														
												</div>

												<div class="col-md-6 col-sm-12 col-xs-12 form-group input_descr_wrap">
													<label class="col-md-12 col-sm-12 col-xs-12" for="tax">Amount<span class="required">*</span></label>
													<input type="text" name="amount[]" class="form-control col-md-1 fix-product_amt" onkeypress='return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46' placeholder="Amount" value="<?php if(!empty($fixCostProductDetails->fix_cost_product_amt)) echo $fixCostProductDetails->fix_cost_product_amt; ?>">
												</div>									

													<?php if($i==0){
															echo '<div class="col-sm-12 btn-row"><button class="btn btn-primary add_more_fix_price_product" type="button">Add</button></div>';
														}else{	
															echo '<button class="btn btn-danger remove_descr_field" type="button"> <i class="fa fa-minus"></i></button>';
														} ?>					
											</div>
								<?php $i++; } } }?>								
								</div>
						</div>		
					</div>		
				</div>		
				</div>						
					</div>	
					  <div class="form-group">
						<label for="InputCompanyType">Schedule Invoice</label>
						<select name="invoice_schedule" class="form-control select2" style="width: 100%;">
								<option value="">--Schedule Invoice--</option>
								<option <?php echo ($company->invoice_schedule == 'daily') ? 'selected':''?> value="daily">Daily</option>
								<option <?php echo ($company->invoice_schedule == 'weekly') ? 'selected':''?> value="weekly">Weekly</option>
								<option <?php echo ($company->invoice_schedule == 'manual') ? 'selected':''?> value="manual">Manual</option>								
						</select>
					  </div>
					  <!--div class="form-group">
						<label for="InputCompanyType">Select Pricing</label>
						<select name="pricing_type" class="form-control " style="width: 100%;">
								<option value="">--Pricing--</option>
								<option <?php echo ($company->pricing_type == 'retail_price') ? 'selected':''?> value="retail_price">Retail</option>
								<option <?php echo ($company->pricing_type == 'retail_cost_percent') ? 'selected':''?> value="retail_cost_percent">Retail+Cost %age</option>
								<option <?php echo ($company->pricing_type == 'add_on_efs') ? 'selected':''?> value="add_on_efs">According to EFS</option>
								<option <?php echo ($company->pricing_type == 'fix_price') ? 'selected':''?> value="fix_price">Fix Price</option>	
						</select>
					  </div-->
					  <div class="form-group">
						<label for="InputCardLimit">USA Pricing: </label>
						<div class="form-check-inline">
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="usa_pricing" value="no" <?php 
								echo set_value('usa_pricing', $company->usa_pricing) == 'no' ? "checked" : ""; ?> />No
						  </label>
						</div>						
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="usa_pricing" value="retail_price" <?php 
								echo set_value('usa_pricing', $company->usa_pricing) == 'retail_price' ? "checked" : ""; ?> />Retail Price
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="usa_pricing" value="retail_cost_percent" <?php 
								echo set_value('usa_pricing', $company->usa_pricing) == 'retail_cost_percent' ? "checked" : ""; ?> />retail_cost_percent
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="usa_pricing" value="add_on_efs" <?php 
								echo set_value('usa_pricing', $company->usa_pricing) == "add_on_efs" ? "checked" : ""; ?> />Add on EFS
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="usa_pricing" value="fix_price" <?php 
								echo set_value('usa_pricing', $company->usa_pricing) == 'fix_price' ? "checked" : ""; ?> />Fix Price
						  </label>
						</div>						
					  </div>
					  <div class="form-group">
						<label for="InputCardLimit">CAD Pricing: </label>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="cad_pricing" value="no" <?php 
								echo set_value('cad_pricing', $company->cad_pricing) == "no" ? "checked" : ""; ?> />No
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="cad_pricing" value="add_on_efs" <?php 
								echo set_value('cad_pricing', $company->cad_pricing) == "add_on_efs" ? "checked" : ""; ?> />Add on EFS
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="cad_pricing" value="fix_price" <?php 
								echo set_value('cad_pricing', $company->cad_pricing) == "fix_price" ? "checked" : ""; ?> />Fix Price
						  </label>
						</div>						
					  </div>					  
					  <div class="form-group">
						<label for="InputCardLimit">SMS Notifications: </label>

						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="sms_notification" value="1" <?php 
								echo set_value('sms_notification', $company->sms_notification) == 1 ? "checked" : ""; ?> />Yes
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="sms_notification" value="0" <?php 
								echo set_value('sms_notification', $company->sms_notification) == 0 ? "checked" : ""; ?> />No
						  </label>
						</div>						
					  </div>
					  <div class="form-group">
						<label for="InputCardLimit">Allow Money Code: </label>

						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="allowMoneyCode" value="1" <?php 
								echo set_value('allowMoneyCode', $company->allowMoneyCode) == 1 ? "checked" : ""; ?> />Yes
						  </label>
						</div>
						<div class="form-check-inline">
						  <label class="form-check-label">
							<input type="radio" class="form-check-input" name="allowMoneyCode" value="0" <?php 
								echo set_value('allowMoneyCode', $company->allowMoneyCode) == 0 ? "checked" : ""; ?> />No
						  </label>
						</div>						
					  </div>
					  </div>
					  <div class="col-lg-6 col-6">
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
						<input type="email" name="company_email" class="form-control" id="InputEmail" placeholder="Enter email" value="<?php echo set_value('company_email', $company->company_email)?>"><div class="multiselect"></div>
							<!-- Multi Select Tag --->
							<input type="text" id="exist-values" class="tagged form-control " data-removeBtn="true" name="moreEmails" value="<?php echo set_value('moreEmails', $company->moreEmails)?>" placeholder="Add more emails">
							<!-- Multi Select Tag --->	
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
						<input type="hidden" name="role" value="company" />

					<div class="form-group">
					  <button type="submit" class="btn btn-primary">Submit</button>
					</div>				  
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