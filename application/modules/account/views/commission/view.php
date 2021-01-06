	<div class=" form-group">
					<input type="hidden" name="id" value="<?php //if(!empty($invoice_detail)) echo $invoice_detail->id; ?>">
					<div class=" form-group">	
						<div class=" form-group">
						
							<div class="panel panel-default">
							 <h3 class="Material-head"><?php if(!empty($comp_detail)){ echo $comp_detail->company_name;}?> </strong><hr></h3>
							    <div class="panel-body">
									<div class="col-md-6 col-sm-6 col-xs-12 form-group label-left">
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										    <div class="row">
										       <label class="col-md-4">Company Email</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group">
											   <?php if(!empty($comp_detail)) echo $comp_detail->company_email; ?>
												</div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										 <div class="row">
										       <label class="col-md-4">Postal Code</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php if(!empty($comp_detail)) echo $comp_detail->postal_code; ?></div>
											  </div> 
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										<div class="row">
										       <label class="col-md-4">Sate</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php if(!empty($comp_detail)) echo $comp_detail->province; ?></div>
										</div>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
											<div class="row">
										       <label class="col-md-4">City</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php if(!empty($comp_detail)) echo $comp_detail->city; ?></div>
										</div>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										    <div class="row">
										       <label class="col-md-4">USA Pricing</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php 
											   if(!empty($comp_detail)){
											    $USAPricing_husky = str_replace('_', ' ', $comp_detail->usa_pricing);
											   echo ucfirst($USAPricing_husky);
											 
											   }?></div>
											  </div> 
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										      <div class="row">
										       <label class="col-md-4">CAD Pricing</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php 
											   $cadPricing = str_replace('_', ' ', $comp_detail->cad_pricing);
											   echo ucfirst($cadPricing);
											   ?></div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										      <div class="row">
										       <label class="col-md-4">CAD Pricing Husky</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php 
											   $cadPricing_husky = str_replace('_', ' ', $comp_detail->cad_pricing_husky);
											   echo ucfirst($cadPricing_husky);
											  
											   ?></div>
											</div>
										</div>
										
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12 form-group label-left">
									    
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										     <div class="row">
										       <label class="col-md-4">Invoice Schedule</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php echo $comp_detail->invoice_schedule;?></div>
										</div>
										</div>
										 
										<?php 
										if($comp_detail->customer_id != ''){
										?>
										
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										 <div class="row">
										       <label class="col-md-4">Customer id</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php echo $comp_detail->customer_id;?></div>
										</div>
										</div>
										<?php  } else{?>
										
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										 <div class="row">
										       <label class="col-md-4">EFS Policy id</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php echo $comp_detail->efs_policy_id; ?></div>
										</div>
										</div>
										<?php } ?>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										     <div class="row">
										       <label class="col-md-4">Company Type</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php 
											    if(!empty($comp_detail->company_type)){
												$com_type =  getNameById('company_types',$comp_detail->company_type,'id');
												
													echo $com_type->company_type;
												}
											   ?></div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										    <div class="row">
										       <label class="col-md-4">Company Type CA</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php 
											   if(!empty($comp_detail->company_type_ca)){
											    $com_type =  getNameById('company_types',$comp_detail->company_type_ca,'id');
											   echo $com_type->company_type;
											   }
											  ?></div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12 col-xs-12 form-group">
										    <div class="row">
										       <label class="col-md-4">Company Type CA Husky</label>
											   <div class="col-md-7 col-sm-12 col-xs-6 form-group"><?php 
											    if(!empty($comp_detail->company_type_ca_husky)){
													$com_type =  getNameById('company_types',$comp_detail->company_type_ca_husky,'id');
													echo $com_type->company_type;
												}
											   ?></div>
											</div>
										</div>
										
									</div>
									
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="x_content">
										<div class="" role="tabpanel" data-example-id="togglable-tabs"></div>
									</div>
								</div>
		
						</div>
					</div>
				</div>
			</div>
		</div>
	

			