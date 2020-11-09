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
			<?php if(!empty($cardsTransData)){ foreach($cardsTransData as $cardDetails){$cardDetail = $cardDetails;} ?>

			<form method="post" action="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>">
			<div class="row">
				<div class="col-md-6 label-left" style=" padding:0px; margin-bottom:20px;">
				   <div class="col-md-12 col-sm-12 col-xs-12 form-group">
						<label for="material">Company Name :</label>
						<div class="col-md-7 col-sm-7 col-xs-6 form-group">
							<div><?= $cardDetail->company_name?></div>
						</div>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12 form-group">
						<label for="material">Address :</label>
						<div class="col-md-7 col-sm-7 col-xs-6 form-group">
							<div><?= $cardDetail->address?></div>
						</div>
					</div>
				</div>
				
				<div class="col-md-6 label-left" style=" padding:0px; margin-bottom:20px;">
				   <div class="col-md-12 col-sm-12 col-xs-12 form-group">
						<label for="material">Invoice Id :</label>
						<div class="col-md-7 col-sm-7 col-xs-6 form-group">
							<div><input type="text" name="invoice_id" value="<?php echo ($maxInvId->id == 0)?'CL1':"CL".$maxInvId->id;// set_value('invoice_date', $cardDetail->invoice_date) ?>" class="form-control" required /></div>
						</div>
					</div>				
				   <div class="col-md-12 col-sm-12 col-xs-12 form-group">
						<label for="material">Invoice Date :</label>
						<div class="col-md-7 col-sm-7 col-xs-6 form-group">
							<div><input type="date" name="invoice_date" value="<?php echo date('Y-m-d') ?>" class="form-control" required /></div>
						</div>
					</div>

				</div>				
			</div>

			<h3 class="text-center">Non Invoiced Transaction Details</h3>
			<form method="post" action="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>" >
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro">
							<div class="col-md-12 input_descr_wrap label-box mobile-view2">
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="cardNumber">Card Number</label>
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
								$subTotal = 0; $gstCount = 0; $pstCount = 0; $qstCount = 0; $productprice = 0;
								
								foreach($cardsTransData as $card){ 
								$decodeCardCat = json_decode($card->category);
								$multi_trans = 0;		
								foreach($decodeCardCat as $cat_vals){
									$cat_values = $cat_vals;

								$dResult = $this->db->where('company_id', $card->driver_id)->get('drivers')->row();								
								$companyTypeResult = $this->db->where('id', $card->company_type)->get('company_types')->row();
								$typeOfCompany = strtolower($companyTypeResult->company_type);

								$getRetailPrice = $this->db->where(array('product'=> $cat_values))->get('retail_pricing')->row();
								if(!empty($getRetailPrice->retail_price)){
									$retailPriceVals = $getRetailPrice->retail_price;
								}

								$finalProductPrice = 0;								
								$this->db->select('users.fix_cost_data, users.usa_pricing, users.cad_pricing');
								$this->db->from('cards');		
								$this->db->join('users', 'users.id=cards.company_id');
								$this->db->where('cards.card_number', $card->card_number);
								$getFixPriceStatus = $this->db->get()->row();
								
								$decodeUnitPrice = json_decode($card->unit_price);
								
								$unitprice_values = $decodeUnitPrice[$multi_trans];
								
								$decodeQuantity = json_decode($card->quantity);
								
								$qty_values = $decodeQuantity[$multi_trans];
								
								$productName = $cat_values;
								if(!empty($getFixPriceStatus->fix_cost_data)){	
								$decodeFixPriceProduct = json_decode($getFixPriceStatus->fix_cost_data);
								foreach($decodeFixPriceProduct as $decodeFixPriceProductRows){
									if($decodeFixPriceProductRows->fix_cost_product == $productName){
										$finalProductPrice = $decodeFixPriceProductRows->fix_cost_product_amt;
									}
								}
								}
								if(!empty($getFixPriceStatus->usa_pricing) || !empty($getFixPriceStatus->cad_pricing)){
									
									if($card->billing_currency == 'CAD'){
										$pricingType = $getFixPriceStatus->cad_pricing;
										$priceListAccType = $this->db->select($pricingType)->get('pricelist_edit_ca')->row();
									}else{
										$pricingType = $getFixPriceStatus->usa_pricing;
										$priceListAccType = $this->db->select($pricingType)->get('pricelist_edit_us')->row();
									}
									$decValues = json_decode($priceListAccType->$pricingType);
									$i = 0;
									$priceByCompType = 0;
									foreach($decValues as $key=>$decValuesrows){
										foreach($decValuesrows as $k=>$decValuesrows2){
											
											if($k == $cat_values){

											$gasStationName = str_replace(' ', '-', trim($card->gas_station_name));
											
											if($card->billing_currency == 'CAD'){
											$makeDefdName = 'defd_'.$typeOfCompany;
											//if($productName === 'DEFD'){
												$defdPriceByCompType = $decValuesrows2[0]->$makeDefdName[0];
											//}else{
												$priceByCompType = $decValuesrows2[0]->$typeOfCompany[0];
											//}
											/* if(in_array($makeDefdName, $decValuesrows2) == true){
												//unset($priceByCompType);//pre($decValuesrows2[0]->$makeDefdName[0]);
												$priceByCompType = $decValuesrows2[0]->$makeDefdName[0];
											}else{ */
												//unset($priceByCompType);
												//if($productName != 'DEFD'){
												
											//}
											//die;
											}
											else{
												$makeDefdName = 'defd_'.$typeOfCompany;
												$defdPriceByCompType = $decValuesrows2[0]->$makeDefdName[0];
												$gasStnName = str_replace(' ', '-', trim($card->gas_station_name));
												//pre($gasStnName);
												//pre($decValuesrows2[0]->gas_station[0]);	
												if($card->gas_station_state == $decValuesrows2[0]->state[0] && $gasStnName == $decValuesrows2[0]->gas_station[0]){//pre($gasStnName);
													//pre($decValuesrows2[0]->$typeOfCompany[0]);
													$priceByCompType = $decValuesrows2[0]->$typeOfCompany[0];
												}
											}
											}
											
										}

									}	
								}
								/* if($productName == 'DEFD'){
									pre($priceByCompType1);
									} */
									if($productName == 'DEFD'){
										$priceAfterQty = floatval($defdPriceByCompType) * $qty_values;
									}else{
				$priceAfterQty = floatval($priceByCompType) * $qty_values;
									}
								
		/* GST/PST/QST Start */						
		$taxapplicable = $this->db->where('product_name', $productName)->get('products')->row();

			if(!empty($taxapplicable->tax) ){
			$taxArray = json_decode($taxapplicable->tax);
				foreach($taxArray as $key=>$taxArrayRow){	
				$taxOutput[$taxArrayRow] = $this->db->select('tax_rate')->where(array('tax_type'=> $taxArray[$key], 'state'=>$card->gas_station_state))->get('tax')->row();

				//GST/HST/FNT || PST/QST
				if(!empty($taxOutput['gst']->tax_rate)){$gst = str_replace('%', '', $taxOutput['gst']->tax_rate);}else{$gst = 0;}
				if(!empty($taxOutput['pst']->tax_rate)){$pst = str_replace('%', '', $taxOutput['pst']->tax_rate);}else{$pst = 0;}
				if(!empty($taxOutput['qst']->tax_rate)){$qst = str_replace('%', '', $taxOutput['qst']->tax_rate);}else{$qst = 0;}			
				}
			}
			if(empty($gst)){$gst = 0;}
			if(empty($pst)){$pst = 0;}
			if(empty($qst)){$qst = 0;}
						
			$includeGST=0; $includePST=0; $includeQST=0;
			$includeGST = $priceAfterQty * $gst / 100;
			//$gstByCard += $includeGST;
			$gstCount += $includeGST;
			if($card->gas_station_state != 'QC'){
				$includePST = $priceAfterQty * $pst / 100;
				//$pstByCard += $includePST;
				$pstCount += $includePST;
			}else{
				$includeQST = $priceAfterQty * $qst / 100;
				//$qstByCard += $includeQST;				
				$qstCount += $includeQST;				
			}
			$fuelTax = 	$includeGST + $includePST + $includeQST;
		/* GST/PST/QST END */			
							
								if($finalProductPrice == ''){
									$finalProductPrice = $unitprice_values;
								}								
								$productPriceList = $this->db->get('pricelist_us')->row();
								//$finalPricing = floatval($grandAmount);
								$grand_amount = $priceAfterQty + $fuelTax;
								$subTotal += $grand_amount;
								?>								
								<?php $cResult = $this->db->where('id', $card->company_id)->get('users')->row();?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box">
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="cardNumber">Card Number</label>
										<div class="field-val-div"><?= $card->card_number ?></div>
										<input type="hidden" class="" name="card_number[]" value="<?= $card->card_number?>" />
									</div>
									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="productName">Product Name</label>
										<div class="field-val-div"><?= $cat_values?></div>

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
										<div class="field-val-div"><?= $qty_values?></div>
											
									</div>									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="price">Price </label>														
										<div class="field-val-div"><?php if($productName == 'DEFD'){echo floatval($defdPriceByCompType);}else{echo floatval($priceByCompType);}?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="fuelTax">Fuel Taxes</label>													
										<div class="field-val-div"><?php echo floor($fuelTax*100)/100 ?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="amount">Amount</label>									
										<div class="field-val-div"><?php echo number_format($grand_amount, 2);//number_format($GrandTotalwithTax, 2)?></div>
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="dateTime">Date & Time</label>									
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
				<?php $STAMT = floor($subTotal*100)/100;?>
				<table class="table table-bordered">
					<tr><td>Sub Total</td><td><input type="text" class="trans-inv-input" name="sub_total" value="<?= $STAMT?>" readonly /></td></tr>
					<tr><td>G.S.T.</td><td><input type="text" class="trans-inv-input" name="gst_total" value="<?= number_format($gstCount, 2)?>" readonly /></td></tr>
					<tr><td>P.S.T.</td><td><input type="text" class="trans-inv-input" name="pst_total" value="<?= number_format($pstCount, 2)?>" readonly /></td></tr>
					<tr><td>Total</td><td><input type="text" class="trans-inv-input" name="grand_total" value="<?= $STAMT?>" readonly /></td></tr>
				</table>
			</div>
			</div>
			<input type="hidden" name="company_id" value="<?= $this->uri->segment(3)?>" />
			<div class="clearfix"></div>
			<br />
			<?php //print_r($fetchInvoiceData = $this->account_model->export_invoice_pdf('3')) ?>
			<p class="text-center"><input class="btn btn-info" name="submit" type="submit" value="Generate Invoice" /></p>
			<!--p class="text-center"><a href="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>" class="btn btn-info" target="_blank">Generate PDF</a></p-->
			</form>
			<?php }else{echo "<p class='alert alert-info'>No Invoice Pending</p>";}?>
			</div>
			</div>
	</section>
</div>	