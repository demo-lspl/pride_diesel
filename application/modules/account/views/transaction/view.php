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
				<form class="search form-inline" action="<?php echo base_url().'account/transaction_view_by_cid/'.$this->uri->segment(3); ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<!--input class="form-control search-input" name="search" value="<?php //if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php //echo "Search company name"; ?>" type="text" /-->
					Date Range: &nbsp;&nbsp;<input type="text" name="date_range" class="daterange1 form-control"  />&nbsp;&nbsp;
				  </div>
					<div class="form-group">
						<select class="form-control company-select" name="company_name">
							<option value="">Choose Company</option>
							<option value="efs" <?php if(!empty($_GET['company_name']) && $_GET['company_name'] == 'efs'){echo "selected";}?>>EFS</option>
							<option value="husky" <?php if(!empty($_GET['company_name']) && $_GET['company_name'] == 'husky'){echo "selected";}?>>Husky</option>
						</select>&nbsp;&nbsp;
					</div>				  
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
				</form>	
				<?php 
				$startDate = date('Y-m-d');
				$endDate = date('Y-m-d');
				$daterange = null;
				if(!empty($_GET['date_range'])){
					$daterange = $_GET['date_range'];
					$expDateRange = explode(' - ', $_GET['date_range']);
					$startDate = $expDateRange[0];
					$endDate = $expDateRange[1];
					
				}?>			
				<script type="text/javascript">
					$('.daterange1').daterangepicker({
						autoUpdateInput: false,
						locale: {
							format: 'YYYY-MM-DD',
							//cancelLabel: 'Clear'
						},
							"startDate": '<?php echo $startDate; ?>',
							"endDate": '<?php echo $endDate; ?>'		
					});
					$('.daterange1').on('apply.daterangepicker', function (ev, picker) {
						$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
					});

					$('.daterange1').on('cancel.daterangepicker', function (ev, picker) {
						$(this).val('');
					});					
				</script>				
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
							<div><input type="text" name="invoice_id" value="<?php echo ($maxInvId->id == 0)?'CL1':"CL".$maxInvId->id;// set_value('invoice_date', $cardDetail->invoice_date) ?>" class="form-control" required readonly /></div>
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
			<h3 class="text-center showCount">Non Invoiced Transactions</h3>
			<form method="post" action="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>" >
			<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap">
					
					<div class="panel panel-default">
					<input type="hidden" name="daterange" value="<?php echo $daterange; ?>" />
						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro">
							<div class="col-md-12 input_descr_wrap label-box mobile-view2 for-label">
								<div class="col-md-1 col-xs-12 item form-group">
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
									<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Retail Price</label>
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
								$subTotal = 0; $gstCount = 0; $pstCount = 0; $qstCount = 0; $productprice = 0; $grandTaxAmt = 0;
								$totrecord = 1; $totalTtransactions = 0;
								foreach($cardsTransData as $card){ 
								$decodeCardCat = json_decode($card->category);
								$multi_trans = 0;		
								foreach($decodeCardCat as $cat_vals){
									//pre($card);
									$cat_values = $cat_vals;

								$finalProductPrice = 0;								
								$this->db->select('users.fix_cost_data, users.usa_pricing, users.cad_pricing');
								$this->db->from('cards');		
								$this->db->join('users', 'users.id=cards.company_id');
								$this->db->where('cards.card_number', $card->card_number);
								$getFixPriceStatus = $this->db->get()->row();
								
								$decodeUnitPrice = json_decode($card->pride_price);
								$decodeUPrice = json_decode($card->unit_price);
								
								$prideDieselPrice = $decodeUnitPrice[$multi_trans];
								$efsPrice = $decodeUPrice[$multi_trans];
								
								$decodeQuantity = json_decode($card->quantity);
								
								$qty_values = $decodeQuantity[$multi_trans];
								//pre($decodeUnitPrice);
								$priceAfterQty = $prideDieselPrice * $qty_values;
								$productName = $cat_values;
								/* if(!empty($getFixPriceStatus->fix_cost_data)){	
								$decodeFixPriceProduct = json_decode($getFixPriceStatus->fix_cost_data);
								foreach($decodeFixPriceProduct as $decodeFixPriceProductRows){
									if($decodeFixPriceProductRows->fix_cost_product == $productName){
										$finalProductPrice = $decodeFixPriceProductRows->fix_cost_product_amt;
									}
								}
								} */
								/* if($productName == 'DEFD'){
									pre($priceByCompType1);
									} */
								
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
						
			$includeGST=0; $includePST=0; $includeQST=0; $finalTaxAmt = 0; //$totalTax = 0;
			//echo $gst + $pst + $qst;
			$totalTax = $gst + $pst + $qst;
			//echo $totalTax;
			if(strlen($totalTax) < 2){
					$revTotalTaxAmt = '1.0'.$totalTax;
					$amtAfterReversal = floatval($priceAfterQty) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $priceAfterQty - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;					
				}elseif(strpos($totalTax, '.')!==false){
					$revTotalTaxAmt = '1.'.str_replace(".","",$totalTax);
					$amtAfterReversal = floatval($priceAfterQty) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $priceAfterQty - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;
				}else{
					$revTotalTaxAmt = '1.'.$totalTax;
					$amtAfterReversal = floatval($priceAfterQty) / floatval($revTotalTaxAmt);
					$minuspriceandtax = $priceAfterQty - $amtAfterReversal;
					$finalTaxAmt =  floor($minuspriceandtax*100)/100;					
				}
			if(!empty($gst)){
				if(strlen($gst) < 2){
					$mergeOneGst = '1.0'.$gst;
				}else{
					$mergeOneGst = '1.'.$gst;
				}
				$reverseGst = floatval($priceAfterQty) / floatval($mergeOneGst);
				$includeGST =  $priceAfterQty - $reverseGst;
				//$gstPercent = '1'.$gst;
				//$includeGST = $priceAfterQty * 100 /$gstPercent;
				//$gstByCard += $includeGST;
				$gstCount += $includeGST;
			}
			if(!empty($pst)){
				if(strlen($pst) < 2){
					$mergeOnePst = '1.0'.$pst;
				}else{
					$mergeOnePst = '1.'.$pst;
				}
				$reversePst = floatval($priceAfterQty) / floatval($mergeOnePst);
				$includePST = $priceAfterQty - $reversePst;
				/* $pstPercent = '1'.$pst;
				$includePST = $priceAfterQty * 100/$pstPercent; */
				//$pstByCard += $includePST;
				$pstCount += $includePST;
			}
			if(!empty($qst) && $card->gas_station_state == 'QC'){
				if(strlen($qst) < 2){
					$mergeOneQst = '1.0'.$qst;
				}else{
					$mergeOneQst = '1.'.$qst;
				}
				$reverseQst = floatval($priceAfterQty) / floatval($mergeOneQst);
				$includeQST = $priceAfterQty - $reverseQst;
				/* $qstPercent = '1'.$qst;
				$includeQST = $priceAfterQty * 100/$qstPercent; */
				//$qstByCard += $includeQST;				
				$qstCount += $includeQST;			
			}
			
			$GPQtotal = 	$includeGST + $includePST + $includeQST;
			$fuelTax = $GPQtotal;
			//pre($GPQtotal);
			/* GST/PST/QST END */											
								$productPriceList = $this->db->get('pricelist_us')->row();
								//$finalPricing = floatval($grandAmount);
								$grand_amount = $priceAfterQty;
								$grandTaxAmt += $finalTaxAmt;
								$subTotal += $grand_amount;
								
								?>								
								<?php $cResult = $this->db->where('id', $card->company_id)->get('users')->row();?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box for-border">
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
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
									  <label class="col-md-12 col-sm-12 col-xs-12" for="price">Retail Price </label>
										<div class="field-val-div"><?php echo $efsPrice?></div>														
									</div>									
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									  <label class="col-md-12 col-sm-12 col-xs-12" for="price">Price </label>
										<div class="field-val-div"><input class="pprice" style="width: 100%;" type="text" name="" data-transid="<?= $card->transactionId ?>" data-rownum="<?= $multi_trans ?>" value="<?php echo $prideDieselPrice?>" /></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">	 
									<label class="col-md-12 col-sm-12 col-xs-12" for="fuelTax">Fuel Taxes</label>													
										<div class="field-val-div"><?php if($card->billing_currency == 'CAD'){echo floor($finalTaxAmt*100)/100;}else{echo 0;} ?></div>														
									</div>														
									<div class="col-md-1 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="amount">Amount</label>									
										<div class="field-val-div"><?php echo floor($grand_amount*100)/100;?></div>
									</div>														
									<div class="col-md-2 col-sm-12 col-xs-12 form-group">
                                        <label class="col-md-12 col-sm-12 col-xs-12" for="dateTime">Date & Time</label>									
										<div style="border-right: 1px solid #c1c1c1" class="field-val-div"><?= $card->transaction_date?></div>
										<input type="hidden" class="" name="transaction_date[]" value="<?= $card->transaction_date?>" />
										
									</div>	
															
								</div>
								<?php
								$totalTtransactions += $totrecord;	
								$multi_trans++;	//$subTotal += $amtAfterTax;							
								}
		
								unset($taxOutput, $ppfTot, $gst, $pst);
								}
								?>
						<input type="hidden" class="tot-trans" value="<?php echo $totalTtransactions; ?>" />
						</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
			<div class="col-sm-12 col-md-3 no-padding-right no-padding-left float-right" >
				<?php $STAMT = floor($subTotal*100)/100;?>
				<?php $sub_total = $subTotal - $grandTaxAmt;?>
				<table class="table table-bordered">
					<tr><td>Sub Total</td><td><input type="text" class="trans-inv-input" name="sub_total" value="<?= floor($sub_total*100)/100 ?>" readonly /></td></tr>
					<tr><td>HST/GST</td><td><input type="text" class="trans-inv-input" name="gst_total" value="<?= floor($grandTaxAmt*100)/100?>" readonly /></td></tr>
					<!--tr><td>G.S.T.</td><td><input type="text" class="trans-inv-input" name="gst_total" value="<?= floor($gstCount*100)/100?>" readonly /></td></tr>
					<tr><td>P.S.T.</td><td><input type="text" class="trans-inv-input" name="pst_total" value="<?= floor($pstCount*100)/100?>" readonly /></td></tr>
					<tr><td>Q.S.T.</td><td><input type="text" class="trans-inv-input" name="pst_total" value="<?= floor($qstCount*100)/100?>" readonly /></td></tr-->
					<tr><td>Total</td><td><input type="text" class="trans-inv-input" name="grand_total" value="<?= $STAMT?>" readonly /></td></tr>
				</table>
			</div>
			</div>
			<input type="hidden" name="company_id" value="<?= $this->uri->segment(3)?>" />
			<div class="clearfix"></div>
			<br />
			<?php //print_r($fetchInvoiceData = $this->account_model->export_invoice_pdf('3')) ?>
			<p class="text-center"><input class="btn btn-info" name="submit" type="submit" value="Generate Invoice" onclick="return confirm('Are you sure you want to generate invoice?');" /></p>
			<!--p class="text-center"><a href="<?php echo base_url('account/generate_trans_invoice/').$this->uri->segment(3)?>" class="btn btn-info" target="_blank">Generate PDF</a></p-->
			</form>
			<?php }else{echo "<p class='alert alert-info'>No Invoice Pending</p>";}?>
			</div>
			</div>
	</section>
</div>	