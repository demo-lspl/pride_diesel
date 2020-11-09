<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="addnew-user"><a href="<?php echo base_url('account/invoice_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Invoice</a></div>
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Invoice <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
			<?php //print_r($fetchInvoice) ?>
			<?php //foreach($fetchInvoice as $fetchInvoiceVal): $invoiceValues = $fetchInvoiceVal; endforeach;?>
			<div class="row">
				<div class="col-md-6 label-left" style=" padding:0px; margin-bottom:20px;">
				   <div class="col-md-12 col-sm-12 col-xs-12 form-group">
								 <label for="material">Party Name :</label>
								<div class="col-md-7 col-sm-7 col-xs-6 form-group">
									<div><?= $fetchInvoice->company_name?></div>
								</div>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12 form-group">
								 <label for="material">Contact :</label>
								<div class="col-md-7 col-sm-7 col-xs-6 form-group">
									<div><?= $fetchInvoice->company_email?></div>
								</div>
					</div>
				</div>
				
				<div class="col-md-6 label-left" style=" padding:0px; margin-bottom:20px;">
				   <div class="col-md-12 col-sm-12 col-xs-12 form-group">
								 <label for="material">Order Date :</label>
								<div class="col-md-7 col-sm-7 col-xs-6 form-group">
									<div><?= date_format(date_create($fetchInvoice->date_created), 'Y/m/d')?></div>
								</div>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12 form-group">
								 <label for="material">Dispatch Date :</label>
								<div class="col-md-7 col-sm-7 col-xs-6 form-group">
									<div><?= date_format(date_create($fetchInvoice->date_created), 'Y/m/d')?></div>
								</div>
					</div>
				</div>				
			</div>
			
			<h3 class="text-center">Invoice Details</h3>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro">
							<div class="col-md-12 input_descr_wrap label-box mobile-view2">
								
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product Name </label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Quantity</label>
								</div>	
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Price </label>
								</div>	
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">GST<span class="required">*</span></label>
								</div>	
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="rate">Sub Total<span class="required">*</span></label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="tax">Total </label>
								</div>		
	
							
								
							</div>

									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box">
												
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product Name <span class="required">*</span></label>
										<div class="field-val-div"><?= $fetchInvoice->product_type?></div>
										<!--select class="itemName  form-control selectAjaxOption select2 get_val matrial_details_id select2-hidden-accessible" id="add_matrial_onthe_spot" required="required" name="material_id[]" data-id="material" data-key="id" data-fieldname="material_name" data-where="created_by_cid=9 AND status=1" width="100%" tabindex="-1" aria-hidden="true"> 										
											<option value="">Select</option>			
											    
									</select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: 100px;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-add_matrial_onthe_spot-container"><span class="select2-selection__rendered" id="select2-add_matrial_onthe_spot-container"><span class="select2-selection__placeholder">Select And Begin Typing</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span> 
											<input type="hidden" name="mat_idd_name" id="matrial_Iddd"-->	
									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Quantity</label>														
										<div class="field-val-div"><?= $fetchInvoice->quantity?></div>
										<!--textarea required="required" name="descr_of_goods[]"  class="form-control col-md-12 col-xs-12" placeholder="Description Of Goods"></textarea-->												
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Price </label>														
										<div class="field-val-div"><?= $fetchInvoice->price_unit?></div>														
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">GST<span class="required">*</span></label>													
										<div class="field-val-div"><?= $fetchInvoice->GST?></div>														
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Sub Total<span class="required">*</span></label>									
										<div class="field-val-div"><?= $fetchInvoice->sub_total?></div>
									</div>
																			
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Total<span class="required">*</span></label>
										<div style="border-right: 1px solid #c1c1c1" class="field-val-div"><?= $fetchInvoice->final_total?></div>
									</div>
	
															
								</div>
                                <!--div class="col-sm-12 btn-row"><button class="btn btn-primary add_description_detail_button" type="button">Add</button></div-->								
															
						
						</div>
						</div>
					</div>
				</div>
			</div>
			<!--table class="table table-bordered ">
			<thead>
				<tr>
					<th>Sr No</th> 
					<th>Product Name</th>
					<th>Quantity</th>
					<th>Price</th>				
					<th>GST</th>				
					<th>Sub Total</th>				
					<th>Total</th>				
				</tr>
			</thead>
			<tbody>

			  <tr>
				<td><?= $invoiceValues->id?></td>
				<td><?= $invoiceValues->product_type?></td>
				<td><?= $invoiceValues->quantity?></td>
				<td><?= $invoiceValues->price_unit?></td>
				<td><?= $invoiceValues->GST?></td>
				<td><?= $invoiceValues->sub_total?></td>
				<td><?= $invoiceValues->final_total?></td>
			  </tr>

			  </tbody>

			</table-->
			<br />
			<?php //print_r($fetchInvoiceData = $this->account_model->export_invoice_pdf('3')) ?>
			<p class="text-center"><a href="<?php echo base_url('account/generate_invoice_pdf/').$this->uri->segment(3)?>" class="btn btn-info" target="_blank">Generate PDF</a></p>
			</div>
			</div>
	</section>
</div>	