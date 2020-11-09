<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="addnew-user"><a href="<?php echo base_url('account/transactions') ?>" class="btn btn-info"><i class="fa fa-arrow-left"></i> Back</a></div>
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Card <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">

			<div class="row">
				<div class="col-md-6 label-left" style=" padding:0px; margin-bottom:20px;">
				   <div class="col-md-12 col-sm-12 col-xs-12 form-group">
								 <label for="material">Company Name :</label>
								<div class="col-md-7 col-sm-7 col-xs-6 form-group">
									<div><?= $fetchInvoice->gas_station_name?></div>
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
									<div><input type="date" name="invoice_date" value="<?= set_value('invoice_date', $invoiceDetails->invoice_date) ?>" class="form-control" /></div>
								</div>
					</div>

				</div>				
			</div>
			
			<h3 class="text-center">Transaction Details</h3>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro">
							<div class="col-md-12 input_descr_wrap label-box mobile-view2">
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="tax">Driver Name</label>
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
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Fuel Taxes</label>
								</div>	
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="rate">Amount</label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="rate">Date & Time</label>
								</div>								
							</div>
								<?php foreach($cardDetails as $card){?>
								<?php $dResult = $this->db->where('id', $driverDetails->driver_id)->get('drivers')->row();?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box">
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Driver Name</label>
										<div class="field-val-div"><?= !empty($dResult->name)?$dResult->name:'&nbsp;' ?></div>
									</div>
									
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product Name</label>
										<div class="field-val-div"><?= $card->category?></div>

									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Quantity</label>														
										<div class="field-val-div"><?= $card->quantity?></div>
											
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Price </label>														
										<div class="field-val-div"><?= $card->unit_price?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Fuel Taxes</label>													
										<div class="field-val-div"><?= "&nbsp;" ?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Amount</label>									
										<div class="field-val-div"><?= $card->amount?></div>
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Date & Time</label>									
										<div style="border-right: 1px solid #c1c1c1" class="field-val-div"><?= $card->transaction_date?></div>
									</div>	
															
								</div>
								<?php }?>
                                <!--div class="col-sm-12 btn-row"><button class="btn btn-primary add_description_detail_button" type="button">Add</button></div-->								
															
						
						</div>
						</div>
					</div>
				</div>
			</div>
			<!--div class="col-md-12">
			<div class="col-sm-12 col-md-3 no-padding-right no-padding-left float-right" >
				<table class="table table-bordered">
					<tr><td>Sub Total</td><td><?//= $fetchInvoice->sub_total?></td></tr>
					<tr><td>G.S.T.</td><td><?//= $fetchInvoice->GST?></td></tr>
					<tr><td>P.S.T.</td><td>0.00</td></tr>
					<tr><td>Total</td><td><?//= $fetchInvoice->final_total?></td></tr>
				</table>
			</div>
			</div-->
			<div class="clearfix"></div>
			<br />
			<?php //print_r($fetchInvoiceData = $this->account_model->export_invoice_pdf('3')) ?>
			<p class="text-center"><a href="<?php echo base_url('account/generate_invoice/').$this->uri->segment(3)?>" class="btn btn-info" target="_blank">Generate PDF</a></p>
			</div>
			</div>
	</section>
</div>	