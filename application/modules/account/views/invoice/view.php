<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="addnew-user"><a href="<?php echo base_url('account/ledgers') ?>" class="btn btn-info"><i class="fa fa-arrow-left"></i> Back</a></div>
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Company <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
			<?php if(!empty($cardsTransData)){ 
			foreach($cardsTransData as $cardDetails){$cardDetail = $cardDetails;} ?>

			<form method="post" action="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>">
			<div class="row">
				<div class="col-md-6 label-left" style=" padding:0px; margin-bottom:20px;">
				   <div class="col-md-12 col-sm-12 col-xs-12 form-group">
						<label for="material">Company Name :</label>
						<div class="col-md-7 col-sm-7 col-xs-6 form-group">
							<div><?= $cardDetail->company_name?></div>
						</div>
					</div>
				</div>
				
				<div class="col-md-6 label-left" style=" padding:0px; margin-bottom:20px;">
					<div class="col-md-12 col-sm-12 col-xs-12 form-group">
						<label for="material">Address :</label>
						<div class="col-md-7 col-sm-7 col-xs-6 form-group">
							<div><?= $cardDetail->address?></div>
						</div>
					</div>

				</div>				
			</div>

			<h3 class="text-center">Invoiced Transaction Details</h3>
			<form method="post" action="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>" >
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro">
							<div class="col-md-12 input_descr_wrap label-box mobile-view2">
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="tax">Invoice #</label>
								</div>							
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="tax">Card Number</label>
								</div>								
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product Name </label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Gas Station</label>
								</div>
								<div class="col-md-1 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Quantity</label>
								</div>									
								<div class="col-md-1 col-xs-12 item form-group">
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
								<?php 
								$gstAmount = 0; $pstAmount = 0; $qstAmount = 0; $grandTotal = 0; $taxCalc = 0;
							
								foreach($cardsTransData as $card){
								//$gstAmount = 0; $pstAmount = 0; $qstAmount = 0; $grandTotal = 0; $taxCalc = 0;
								$decodeTransData = json_decode($card->trans_data);
								$decodeInvoiceData = json_decode($card->invoice_data);
								$multi_trans = 0;
								foreach($decodeInvoiceData as $decodeInvoiceDataRows){
									$transactionDate = $decodeInvoiceDataRows->transaction_date;
									$transactionId = $decodeInvoiceDataRows->transaction_id;
									$getTransactionsTable = $this->db->select('gas_station_name, gas_station_state, billing_currency')->where('transaction_id=',$transactionId)->get('transactions')->row();
									//pre($getTransactionsTable);
									$gasStationName = $getTransactionsTable->gas_station_name;
									$gasStationState = $getTransactionsTable->gas_station_state;
									$billingCurrency = $getTransactionsTable->billing_currency;
									//pre($decodeInvoiceDataRows->transaction_date);
								}
								foreach($decodeTransData as $decodeTransDataRows){
									foreach($decodeTransDataRows as $key=>$cat_valsRows){
										$cardNumber = $key;
										$productName = $cat_valsRows->product_name;
										$quantity = $cat_valsRows->quantity;
										$unitPrice = $cat_valsRows->unit_price;
										
										if(!empty($cat_valsRows->GST)){
											$gstAmount = $cat_valsRows->GST;
										}
										if(!empty($cat_valsRows->PST)){
											$pstAmount = $cat_valsRows->PST;
										}
										if(!empty($cat_valsRows->QST)){
											$qstAmount = $cat_valsRows->QST;
										}
										if(!empty($cat_valsRows->grand_total)){
											$grandTotal = $cat_valsRows->grand_total;
										}
										$taxCalc = $gstAmount + $pstAmount + $qstAmount;
									//}										
										$amountWithoutTax = $cat_valsRows->amountwithouttax;
										//pre($key);
									}
									$totalAmount = $unitPrice * $quantity;
								//die;
								?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box">
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Invoice #</label>
										<div class="field-val-div"><?= $card->invoice_id ?></div>
									</div>									
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Card Number</label>
										<div class="field-val-div"><?= $cardNumber ?></div>
									</div>
									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product Name</label>
										<div class="field-val-div"><?= $productName?></div>

									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Gas Station</label>														
										<div class="field-val-div"><?= $gasStationName?></div>
											
									</div>
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Quantity</label>														
										<div class="field-val-div"><?= $quantity?></div>
											
									</div>									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Price </label>														
										<div class="field-val-div"><?php echo $unitPrice?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Fuel Taxes</label>													
										<div class="field-val-div"><?php if($billingCurrency == 'CAD'){echo floor($taxCalc*100)/100;}else{echo "0";} ?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Amount</label>									
										<div class="field-val-div"><?php echo floor($totalAmount*100)/100;?></div>
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Date & Time</label>	
										<div style="border-right: 1px solid #c1c1c1" class="field-val-div"><?= $transactionDate?></div>
									</div>	
															
								</div>
								<?php
								
								unset($taxCalc);								
								}
								$taxCalc=0;
								//unset($taxOutput, $ppfTot, $gst, $pst);
								}
								?>

						</div>
						</div>
					</div>
				</div>
			</div>
			<!--div class="col-md-12">
			<div class="col-sm-12 col-md-3 no-padding-right no-padding-left float-right" >
				<table class="table table-bordered">
					<tr><td>Sub Total</td><td><input type="text" class="trans-inv-input" name="sub_total" value="<?= number_format($subTotal, 2)?>" readonly /></td></tr>
					<tr><td>G.S.T.</td><td><input type="text" class="trans-inv-input" name="gst_total" value="<?= number_format($gstCount, 2)?>" readonly /></td></tr>
					<tr><td>P.S.T.</td><td><input type="text" class="trans-inv-input" name="pst_total" value="<?= number_format($pstCount, 2)?>" readonly /></td></tr>
					<tr><td>Total</td><td><input type="text" class="trans-inv-input" name="grand_total" value="<?= number_format($subTotal + $gstCount + $pstCount, 2)?>" readonly /></td></tr>
				</table>
			</div>
			</div-->
			<input type="hidden" name="company_id" value="<?= $this->uri->segment(3)?>" />
			<div class="clearfix"></div>
			</form>
			<?php }else{echo "<p class='alert alert-info'>No record found</p>";}?>
			</div>
			</div>
	</section>
</div>	