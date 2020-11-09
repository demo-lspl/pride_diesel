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
							<div><?php echo $cardDetail->invoice_id ?></div>
						</div>
					</div>				
				   <div class="col-md-12 col-sm-12 col-xs-12 form-group">
						<label for="material">Invoice Date :</label>
						<div class="col-md-7 col-sm-7 col-xs-6 form-group">
							<div><?php echo $cardDetail->invoice_date ?></div>
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
								<div class="col-md-2 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="rate">Amount</label>
								</div>
								<div class="col-md-2 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="rate">Date & Time</label>
								</div>								
							</div>
								<?php $subTotal = 0; $gstCount = 0; $pstCount = 0; $productprice = 0;
								//pre($this->db->last_query());die;
								foreach($cardsTransData as $card){?>
								<?php
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
								$this->db->select('users.fix_cost_data, users.pricing_type');
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
								/* if(!empty($getFixPriceStatus->pricing_type)){
									$pricingType = $getFixPriceStatus->pricing_type;
									$priceListAccType = $this->db->select($pricingType)->get('pricelist_us')->row();
									$decValues = json_decode($priceListAccType->$pricingType);
									$i = 0;

									foreach($decValues as $key=>$decValuesrows){
										foreach($decValuesrows as $k=>$decValuesrows2){
											if($k == $cat_values){
												$priceByCompType = $decValuesrows2[0]->$typeOfCompany[0];
											}
										}

									}	
								} */
								$grandAmount = '0.00';
								/* if(!empty($retailPriceVals)){
									//$finalPrice = floatval($retailPriceVals) - floatval($priceByCompType);
									$grandAmount = $finalPrice * $qty_values;
								}else{ */
									$finalPrice = '0.00';
								//}
								
								if($finalProductPrice == ''){
									$finalProductPrice = $unitprice_values;
								}								
								$productPriceList = $this->db->get('pricelist_us')->row();

								$subTotal += $grandAmount;
								?>								
								<?php $cResult = $this->db->where('id', $card->company_id)->get('users')->row();?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box">
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="tax">Card Number</label>
										<div class="field-val-div"><?= $card->card_number ?></div>
										<input type="hidden" class="" name="card_number[]" value="<?= $card->card_number?>" />
									</div>
									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Product Name</label>
										<div class="field-val-div"><?= $cat_values?></div>

									</div>
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Gas Station</label>														
										<div class="field-val-div"><?= $card->gas_station_name?></div>
											
									</div>
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Quantity</label>														
										<div class="field-val-div"><?= $qty_values?></div>
											
									</div>									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Price </label>														
										<div class="field-val-div"><?php echo number_format($finalPrice, 4)?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Fuel Taxes</label>													
										<div class="field-val-div"><?php if(!empty($ppfTot)){echo number_format($ppfTot, 4);}else{echo "0.0000";} ?></div>														
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Amount</label>									
										<div class="field-val-div"><?php echo number_format($grandAmount, 2);?></div>
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="rate">Date & Time</label>									
										<div style="border-right: 1px solid #c1c1c1" class="field-val-div"><?= $card->transaction_date?></div>
										<input type="hidden" class="" name="transaction_date[]" value="<?= $card->transaction_date?>" />
										
									</div>	
															
								</div>
								<?php
								$multi_trans++;								
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
					<tr><td>Sub Total</td><td><input type="text" class="trans-inv-input" name="sub_total" value="<?= number_format($subTotal, 2)?>" readonly /></td></tr>
					<tr><td>G.S.T.</td><td><input type="text" class="trans-inv-input" name="gst_total" value="<?= number_format($gstCount, 2)?>" readonly /></td></tr>
					<tr><td>P.S.T.</td><td><input type="text" class="trans-inv-input" name="pst_total" value="<?= number_format($pstCount, 2)?>" readonly /></td></tr>
					<tr><td>Total</td><td><input type="text" class="trans-inv-input" name="grand_total" value="<?= number_format($subTotal + $gstCount + $pstCount, 2)?>" readonly /></td></tr>
				</table>
			</div>
			</div>
			<input type="hidden" name="company_id" value="<?= $this->uri->segment(3)?>" />
			<div class="clearfix"></div>
			</form>
			<?php }else{echo "<p class='alert alert-info'>No record found</p>";}?>
			</div>
			</div>
	</section>
</div>	