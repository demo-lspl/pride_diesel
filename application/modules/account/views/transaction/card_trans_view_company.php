<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="addnew-user"><a href="<?php echo base_url('account/company_transactions') ?>" class="btn btn-info"><i class="fa fa-arrow-left"></i> Back</a></div>
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Card <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
			
			<h3 class="text-center">Transaction Details</h3>
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro">
							<div class="col-md-12 input_descr_wrap label-box mobile-view2 for-label">
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="driverName">Driver Name</label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="province">City/Province</label>
								</div>								
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="productName">Product Name</label>
								</div>
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Quantity</label>
								</div>	
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="price">Price</label>
								</div>	
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="gstPstQst">GST/PST/QST</label>
								</div>	
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="amount">Amount</label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="dateTime">Date & Time</label>
								</div>								
							</div>
							<?php 
								$userSessDetails = $this->session->userdata('userdata');
								$this->db->join('company_types', 'company_types.id=users.company_type');
								$this->db->where('users.id', $userSessDetails->id);
								$get_user_type = $this->db->get('users')->row();
								$companyType = strtolower($get_user_type->company_type);
								$pricingTypeUS = $get_user_type->usa_pricing;
								$pricingTypeCA = $get_user_type->cad_pricing;
								
								
								if(!empty($cardDetails)){
									foreach($cardDetails as $card){
									$decCategory = json_decode($card->category);
									$decUnit_price = json_decode($card->unit_price);
									$decUnitPride_price = json_decode($card->pride_price);
									$decQuantity = json_decode($card->quantity);
									$totalTaxAmount=0;
									for($transrow=0; $transrow<count($decCategory); $transrow++){
									$productName = $decCategory[$transrow];
									$productQuantity = $decQuantity[$transrow];	
									$prideDieselPrice = $decUnitPride_price[$transrow];
									$transactionQuantity = $decQuantity[$transrow];
									$calcAmount = $prideDieselPrice * $transactionQuantity;
									$amount = floor($calcAmount*100)/100;
									if($card->billing_currency == 'CAD'){
										$getTaxRate = $this->db->select('tax_type, tax_rate')->where('state', $card->gas_station_state)->get('tax')->result();
										$finalGST=0;$finalPST=0;$finalQST=0;
										foreach($getTaxRate as $taxTypeRows){

											if($taxTypeRows->tax_type == 'gst'){
												$gstRate = str_replace('%', '', $taxTypeRows->tax_rate);
												$finalGST = $amount * $gstRate / 100;
											}
											if($taxTypeRows->tax_type == 'pst'){
												$pstRate = str_replace('%', '', $taxTypeRows->tax_rate);
												$finalPST = $amount * $pstRate / 100;
											}
											if($taxTypeRows->tax_type == 'qst'){
												$qstRate = str_replace('%', '', $taxTypeRows->tax_rate);
												$finalQST = $amount * $qstRate / 100;
											}
											$combineTaxes = $finalGST + $finalPST + $finalQST;	
											$totalTaxAmount = floor($combineTaxes*100)/100;
										}
									}								
							
								if(!empty($driverDetails->driver_id)){
										$dResult = $this->db->where('id', $driverDetails->driver_id)->get('drivers')->row();
									}
								?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box for-border">
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Driver Name</label>
										<div class="field-val-div"><?= !empty($dResult->name)?$dResult->name:'&nbsp;' ?></div>
									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="province">City/Province</label>														
										<div class="field-val-div"><?= $card->gas_station_city .', '.$card->gas_station_state?></div>
											
									</div>									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product Name</label>
										<div class="field-val-div"><?= $productName?></div>

									</div>
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Quantity</label>														
										<div class="field-val-div"><?= $productQuantity ?></div>
											
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Price </label>														
										<div class="field-val-div"><?php echo $prideDieselPrice;//implode(', ', $amtAfterDec)?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">GST/PST/QST</label>													
										<div class="field-val-div"><?= $totalTaxAmount ?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Amount</label>									
										<div class="field-val-div"><?php echo $amount; ?></div>
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group" style="border-right: 1px solid #c1c1c1;">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Date & Time</label>									
										<div style="border-right: 1px solid #c1c1c1" class="field-val-div"><?= $card->transaction_date?></div>
									</div>	
															
								</div>
									<?php  
									}
									}
									}else{?>
									<div class="input_descr_wrap middle-box mobile-view mailing-box">
									<div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Driver Name</label>
										<div style="border-right: 1px solid #c1c1c1;border-left: 1px solid #c1c1c1;border-bottom: 1px solid #c1c1c1;padding: 5px;" class="field-val-div">No record found.</div>
									</div>
									</div>	
							
							<?php  }?>
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
			<!--p class="text-center"><a href="<?php echo base_url('account/generate_invoice/').$this->uri->segment(3)?>" class="btn btn-info" target="_blank">Generate PDF</a></p-->
			</div>
			</div>
	</section>
</div>	