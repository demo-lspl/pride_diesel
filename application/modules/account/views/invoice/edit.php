<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Invoice</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div class="form-messages error"><?php echo validation_errors();//echo($messages['errors']); ?></div>
					<?php if($this->session->flashdata("success")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success")?>
					</div>
					<?php } ?>
					
				<form method="post" class="form-horizontal" action="<?php echo base_url('account/invoice_edit/').$this->uri->segment(3) ?>">
				<?php //echo form_open('account/invoice_edit/'.$this->uri->segment(3)) ?>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="party_name">Company Name</label>
								<select name="party_name" class="form-control selectledgergroup party_name" style="width: 100%;">
									<option value="">Select Company</option>								
									<?php foreach($company_names as $company_name_val): ?>
										<option <?php if($company_name_val->id == $invoiceDetails->party_name){echo "selected";} ?> value="<?php echo $company_name_val->id; ?>"><?php echo ucwords($company_name_val->company_name); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<label for="party_address">Company Address</label>
								<input type="text" name="party_address" class="form-control" id="party_address" value="<?= set_value('party_address', $invoiceDetails->party_address) ?>" placeholder="Company Address" />
							</div>								
							
						
							</div>
							<div class="col-md-6">
							<div class="form-group">
								<label for="invoice_number">Invoice Number</label><?php $inv_id = $maxId->id + '1';?>
								<input type="text" name="invoice_number" class="form-control" id="invoice_number" value="<?php if(empty($invoiceDetails->invoice_number)){echo "CL".$inv_id;}else{echo set_value('invoice_number', $invoiceDetails->invoice_number);} ?>" placeholder="Invoice Number" />
							</div>
							<div class="form-group">
								<label for="invoice_number">Invoice Date</label>							
                    <!--div class="input-group date" id="reservationdate" data-target-input="nearest">
                        <input type="text" class="form-control datetimepicker-input" data-target="#reservationdate"/>
                        <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div-->
							<input type="date" name="invoice_date" value="<?= set_value('invoice_date', $invoiceDetails->invoice_date) ?>" class="form-control" />
							</div>
						</div>
					</div>
					
			<div class="invoice-edit-block">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro" >
							<div class="col-md-12 input_descr_wrap label-box mobile-view2">
								
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Driver Name </label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Unit</label>
								</div>	
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Product Type</label>
								</div>	
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">QTY<span class="required">*</span></label>
								</div>	
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="rate">Fuel Taxes<span class="required">*</span></label>
								</div>
								<div class="col-md-1 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="tax">Price Unit </label>
								</div>		
								<div class="col-md-1 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="tax">Total</label>
								</div>	
							
								
							</div>
							<?php /* print_r($invoiceDetails->descr_of_products); */ if(empty($invoiceDetails->descr_of_products)){ ?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box">
												
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Driver Name <span class="required">*</span></label>
										<select id="driver_id" name="driver_id[]" class="select2 form-control">
											<option value="">Select Driver</option>
											<?php $this->db->where('company_id', $invoiceDetails->party_name); 
												$query = $this->db->get('drivers')->result();
											if(!empty($invoiceDetails->party_name)){ ?>
											<?php foreach($query as $driverNames): ?>
												<option <?php /*echo $driverNames->id ."==". $invoiceDetails->driver_id;*/if($driverNames->id == $invoiceDetails->driver_id){echo "selected";} ?> value="<?php echo $driverNames->id; ?>"><?php echo ucwords($driverNames->name); ?></option>
											<?php endforeach; ?>	
											<?php }?>										
										</select>										
										<!--select class="itemName  form-control selectAjaxOption select2 get_val matrial_details_id select2-hidden-accessible" id="add_matrial_onthe_spot" required="required" name="material_id[]" data-id="material" data-key="id" data-fieldname="material_name" data-where="created_by_cid=9 AND status=1" width="100%" tabindex="-1" aria-hidden="true"> 										
											<option value="">Select</option>	    
										</select>
									<span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100px;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-add_matrial_onthe_spot-container"><span class="select2-selection__rendered" id="select2-add_matrial_onthe_spot-container"><span class="select2-selection__placeholder">Select And Begin Typing</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span--> 
											
									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Unit</label>
										<input type="text" name="unit[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Unit" value="">
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Product Type</label>
									  <!--input type="text" name="product[]" required="required" class="form-control col-md-1 year goods_descr_section keyup_event qty add_qty" placeholder="Product" value=""-->
									  
								<select id="product_id" name="product_id[]" class="select2 form-control get_product_val">
									<option value="">Choose Product</option>
									<?php 
									if(!empty($products)){ ?>
									<?php foreach($products as $product): ?>
										<option <?php if($product->id == $invoiceDetails->product_id){echo "selected";} ?> value="<?php echo $product->id; ?>"><?php echo ucwords($product->product_name); ?></option>
									<?php endforeach; ?>	
									<?php }?>										
								</select>									  
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group ">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">QTY<span class="required">*</span></label>
									<input id="inv_quantity" type="text" name="quantity[]" required="required" class="form-control col-md-1 year goods_descr_section keyup_event qty add_qty" placeholder="Quantity" >									
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group input_descr_wrap">
                                    <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Fuel Taxes<span class="required">*</span></label>
										
									<input type="text" name="fuel_taxes[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Fuel Taxes" readonly />

									</div>
																			
									<div class="col-md-1 col-sm-12 col-xs-12 form-group input_descr_wrap">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Price Unit<span class="required">*</span></label>
										<input id="" type="text" name="price_unit[]" required="required" class="form-control col-md-1 " placeholder="Price Unit" >
									</div>
									<div class="col-md-1 col-sm-12 col-xs-12 form-group input_descr_wrap">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Amount<span class="required">*</span></label>
										<input type="text" name="sub_total[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Total" >
									</div>									
	
															
								
								</div>
                                <div class="col-sm-12 btn-row"><button class="btn btn-primary add_description_detail_button_invoice" type="button">Add</button></div>															
						<?php  } ?>
						<!--/div-->
						
						

				<?php $partyID = $invoiceDetails->party_name; ?>
				<?php /* print_r($invoiceDetails->descr_of_products); */if(!empty($invoiceDetails) && $invoiceDetails->descr_of_products != ''){
					$invoiceDetail = json_decode($invoiceDetails->descr_of_products);
					if(!empty($invoiceDetail)){
						$i = 0;
						foreach($invoiceDetail as $invoiceDetails){
					?>


				<?php //print_r($invoiceDetails); ?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box <?php if($i==0){ echo 'btn-3 ';}if($i==1){ echo 'scend-tr ';}else{ echo 'edit-row1 scend-tr';}?>">
												
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Driver Name <span class="required">*</span></label>
										<select  name="driver_id[]" class="select2 form-control">
											<option value="">Select Driver</option>
											<?php $this->db->where('company_id', $partyID); 
												$query = $this->db->get('drivers')->result();
											if(!empty($partyID)){ ?>
											<?php foreach($query as $driverNames): ?>
												<option <?php /*echo $driverNames->id ."==". $invoiceDetails->driver_id;*/if($driverNames->id == $invoiceDetails->driver_id){echo "selected";} ?> value="<?php echo $driverNames->id; ?>"><?php echo ucwords($driverNames->name); ?></option>
											<?php endforeach; ?>	
											<?php }?>										
										</select>										
										
											
									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Unit</label>
										<input type="text" name="unit[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Unit" value="<?php if(!empty($invoiceDetails)) echo $invoiceDetails->unit; ?>">
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="product_id">Product Type</label>
									
									  
								<select  name="product_id[]" class="select2 form-control get_product_val">
									<option value="">Choose Product</option>
									<?php 
									if(!empty($products)){ ?>
									<?php foreach($products as $product): ?>
										<option <?php if($product->id == $invoiceDetails->product_id){echo "selected";} ?> value="<?php echo $product->id; ?>"><?php echo ucwords($product->product_name); ?></option>
									<?php endforeach; ?>	
									<?php }?>										
								</select>									  
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group ">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">QTY<span class="required">*</span></label>
									<input  type="text" name="quantity[]" required="required" class="form-control col-md-1 year goods_descr_section keyup_event qty add_qty" placeholder="Quantity" value="<?php if(!empty($invoiceDetails)) echo $invoiceDetails->quantity; ?>">									
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group input_descr_wrap">
                                    <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Fuel Taxes<span class="required">*</span></label>
										
									<input type="text" name="fuel_taxes[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Fuel Taxes" value="<?php if(!empty($invoiceDetails)) echo $invoiceDetails->fuel_taxes; ?>" readonly />

									</div>
																			
									<div class="col-md-1 col-sm-12 col-xs-12 form-group input_descr_wrap">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Price Unit<span class="required">*</span></label>
										<input  type="text" name="price_unit[]" required="required" class="form-control col-md-1 " placeholder="Price Unit" value="<?php if(!empty($invoiceDetails)) echo $invoiceDetails->price_unit; ?>">
									</div>
									<div class="col-md-1 col-sm-12 col-xs-12 form-group input_descr_wrap">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Amount<span class="required">*</span></label>
										<input type="text" name="sub_total[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Total" value="<?php if(!empty($invoiceDetails)) echo $invoiceDetails->sub_total; ?>">
									</div>									
	
															

								
                                <!--div class="col-sm-12 btn-row"><button class="btn btn-primary add_description_detail_button_invoice" type="button">Add</button></div-->															
						

										<?php if($i==0){
												echo '<div class="col-sm-12 btn-row"><button class="btn btn-primary add_description_detail_button_invoice" type="button">Add</button></div>';
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
			<div class="clearfix"></div><br />
						<div class="row">
						<div class="col-md-9"></div>
						<div class="col-md-3 ">
							<table class="table table-bordered">
							<tr>
								<td><p><span>Sub Total:</span></td><td><span class="sub-total">0.00</span></p></td>
							</tr>
							<tr>
								<td><p><span>G.S.T.:</span></td><td> <span class="gst-amount">0.00</span></p></td>
							</tr>
							<tr>
								<td><p><span>P.S.T.:</span></td><td> <span class="pst-amount">0.00</span></p></td>
							</tr>
							<tr>
								<td><p><span>Total:</span></td><td> <span class="grand_total_txt">0.00</span></p></td>
							</tr>
							</table>
							
							<input type="hidden" name="sub_total1" id="sub_total1" value="0.00" />
							<input type="hidden" name="gst" id="gst" value="0.00" />
							<input type="hidden" class="grand_total" name="final_total" id="final_total" value="0.00" />					
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<input type="submit" name="" class="btn btn-primary" value="Save" />
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