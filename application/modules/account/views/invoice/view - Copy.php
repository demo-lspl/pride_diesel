<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="addnew-user"><!--a href="<?php //echo base_url('account/invoice_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Invoice</a--></div>
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
								 <label for="material">Invoice Created :</label>
								<div class="col-md-7 col-sm-7 col-xs-6 form-group">
									<div><?= date_format(date_create($fetchInvoice->date_created), 'd/m/Y')?></div>
								</div>
					</div>
					<!--div class="col-md-12 col-sm-12 col-xs-12 form-group">
								 <label for="material">Dispatch Date :</label>
								<div class="col-md-7 col-sm-7 col-xs-6 form-group">
									<div><?= date_format(date_create($fetchInvoice->invoice_date), 'Y/m/d')?></div>
								</div>
					</div-->
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
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="tax">Driver Name</label>
								</div>								
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
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Fuel Taxes</label>
								</div>	
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="rate">Amount</label>
								</div>
							</div>
								<?php $fetchInvoices = json_decode($fetchInvoice->descr_of_products); foreach($fetchInvoices as $invoice){?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box">
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Driver Name<span class="required">*</span></label>
										<div style="border-right: 1px solid #c1c1c1" class="field-val-div"><?= $fetchInvoice->final_total?></div>
									</div>
									
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product Name <span class="required">*</span></label>
										<div class="field-val-div"><?= $invoice->product_id?></div>

									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Quantity</label>														
										<div class="field-val-div"><?= $invoice->quantity?></div>
											
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Price </label>														
										<div class="field-val-div"><?= $invoice->price_unit?></div>														
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Fuel Taxes<span class="required">*</span></label>													
										<div class="field-val-div"><?= $invoice->fuel_taxes?></div>														
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Amount<span class="required">*</span></label>									
										<div class="field-val-div"><?= $invoice->sub_total?></div>
									</div>														
	
															
								</div>
								<?php }?>
                                <!--div class="col-sm-12 btn-row"><button class="btn btn-primary add_description_detail_button" type="button">Add</button></div-->								
															
						
						</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
			<div class="col-sm-12 col-md-3 no-padding-right no-padding-left float-right" >
				<table class="table table-bordered">
					<tr><td>Sub Total</td><td><?= $fetchInvoice->sub_total?></td></tr>
					<tr><td>G.S.T.</td><td><?= $fetchInvoice->GST?></td></tr>
					<tr><td>P.S.T.</td><td>0.00</td></tr>
					<tr><td>Total</td><td><?= $fetchInvoice->final_total?></td></tr>
				</table>
			</div>
			</div>
			<div class="clearfix"></div>
			<br />
			<?php //print_r($fetchInvoiceData = $this->account_model->export_invoice_pdf('3')) ?>
			<!--p class="text-center"><a href="<?php //echo base_url('account/generate_invoice_pdf/').$this->uri->segment(3)?>" class="btn btn-info" target="_blank">Generate PDF</a></p-->
			</div>
			</div>
	</section>
</div>	