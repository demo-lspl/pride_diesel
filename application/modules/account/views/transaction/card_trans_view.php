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
			<?php if(!empty($cardsTransData)){ foreach($cardsTransData as $cardDetails){$cardDetail = $cardDetails;} ?>

			
			<h3 class="text-center">Transaction Details</h3>
			<form method="post" action="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>" >
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro">
							<div class="col-md-12 input_descr_wrap label-box mobile-view2 for-label">
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="tax">Card Number</label>
								</div>	
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="tax">Unit Number</label>
								</div>									
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="productName">Product Name </label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="gasStationName">Gas Station</label>
								</div>
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="province">Province</label>
								</div>								
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Quantity</label>
								</div>									
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="price">Price </label>
								</div>	
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="fuelTax">Fuel Taxes</label>
								</div>	
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="amount">Amount</label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="rate">Date & Time</label>
								</div>								
							</div>
								<?php 
								$subTotal = 0; $gstCount = 0; $pstCount = 0; $qstCount = 0; $grandTotal = 0;
								
								foreach($cardsTransData as $card){
								$decodeCardCat = json_decode($card->category);
								$decUnit_price = json_decode($card->unit_price);
								$decUnitPride_price = json_decode($card->pride_price);
								$decQuantity = json_decode($card->quantity);								
								$multi_trans = 0;
								$totalTaxAmount=0;	$finalGST=0;$finalPST=0;$finalQST=0;							
								foreach($decodeCardCat as $cat_vals){
									$productName = $decodeCardCat[$multi_trans];
									$prideDieselPrice = $decUnitPride_price[$multi_trans];
									$productQuantity = $decQuantity[$multi_trans];
									$calcProductAmount = $prideDieselPrice * $productQuantity;
									$amount = floor($calcProductAmount*100)/100;
									if($card->billing_currency == 'CAD'){
										$getTaxRate = $this->db->select('tax_type, tax_rate')->where('state', $card->gas_station_state)->get('tax')->result();
										
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
									$subTotal += $amount - $totalTaxAmount;
									$gstCount += floor($finalGST*100)/100;
									$pstCount += floor($finalPST*100)/100;
									$qstCount += floor($finalQST*100)/100;
									$grandTotal += floor($amount*100)/100;
								?>								
								<?php $cResult = $this->db->where('id', $card->company_id)->get('users')->row();?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box for-border">
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="cardNumber">Card Number</label>
										<div class="field-val-div"><?= $card->card_number ?></div>
										<input type="hidden" class="" name="card_number[]" value="<?= $card->card_number?>" />
									</div>
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="cardNumber">Unit Number</label>
										<div class="field-val-div"><?= $card->unit_number ?></div>
										
									</div>									
									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="productName">Product Name</label>
										<div class="field-val-div"><?= $productName?></div>

									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="gasStationName">Gas Station</label>														
										<div class="field-val-div"><?= $card->gas_station_name?></div>
											
									</div>
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="province">Province</label>
										<div class="field-val-div"><?= $card->gas_station_state?></div>
									</div>									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Quantity</label>														
										<div class="field-val-div"><?= $productQuantity?></div>
											
									</div>									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="price">Price </label>														
										<div class="field-val-div"><?php echo $prideDieselPrice;?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="fuelTax">Fuel Taxes</label>													
										<div class="field-val-div"><?php echo $totalTaxAmount ?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="amount">Amount</label>									
										<div class="field-val-div"><?php echo $amount?></div>
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group" style="border-right: 1px solid #c1c1c1;">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Date & Time</label>									
										<div style="border-right: 1px solid #c1c1c1" class="field-val-div"><?= $card->transaction_date?></div>
										<input type="hidden" class="" name="transaction_date[]" value="<?= $card->transaction_date?>" />
										
									</div>	
															
								</div>
								<?php 
								$multi_trans++;	//$subTotal += $amtAfterTax;							
								}
		
								unset($taxOutput, $ppfTot, $gst, $pst);
								}
								?>

						</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
			<div class="col-sm-12 col-md-3 no-padding-right no-padding-left float-right" >
				<table class="table table-bordered">
					<tr><td>Sub Total</td><td><input type="text" class="trans-inv-input" name="sub_total" value="<?= $subTotal?>" readonly /></td></tr>
					<tr><td>G.S.T.</td><td><input type="text" class="trans-inv-input" name="gst_total" value="<?= $gstCount?>" readonly /></td></tr>
					<tr><td>P.S.T.</td><td><input type="text" class="trans-inv-input" name="pst_total" value="<?= $pstCount?>" readonly /></td></tr>
					<tr><td>Q.S.T.</td><td><input type="text" class="trans-inv-input" name="pst_total" value="<?= $qstCount?>" readonly /></td></tr>					
					<tr><td>Total</td><td><input type="text" class="trans-inv-input" name="grand_total" value="<?= $grandTotal?>" readonly /></td></tr>
				</table>
			</div>
			</div>
			<input type="hidden" name="company_id" value="<?= $this->uri->segment(3)?>" />
			<div class="clearfix"></div>
			<br />
			<?php //print_r($fetchInvoiceData = $this->account_model->export_invoice_pdf('3')) ?>
			<!--p class="text-center"><a href="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>" class="btn btn-info" target="_blank">Generate PDF</a></p-->
			</form>
			<?php }else{echo "<p class='alert alert-info'>No transaction.</p>";}?>
						

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