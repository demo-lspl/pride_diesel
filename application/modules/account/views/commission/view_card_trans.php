<div class="">
	<section class="content box">
		<div class="addnew-user">
			<!--a href="<?php //echo base_url('account/invoice_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Invoice</a-->
		</div>
	<div class="card card-default">
		<div class="card-header bg-card-header">
			<h3 class="text-center">Transaction Details</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
					<button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
		</div>
		<div class="card-body">
		<form class="search form-inline" action="<?php echo base_url().'account/view_crd_trns_dtls/'.$this->uri->segment(3);?>" method="get" autocomplete="off">
					<div class="form-group">
						<input type="text" name="date_range" class="daterange form-control"  />&nbsp;&nbsp;
					</div>
					<button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
					<a href="<?php echo base_url(); ?>account/view_crd_trns_dtls/<?php echo $this->uri->segment(3); ?>">
						<input type="button" name="submitSearchReset" class="btn btn-primary" value="Reset"></a>
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
					}
				?>
				<script type="text/javascript">
					$('.daterange').daterangepicker({
						autoUpdateInput: false,
						locale: {
							format: 'YYYY-MM-DD',
							cancelLabel: 'Clear'
						},
							"startDate": '<?php echo $startDate; ?>',
							"endDate": '<?php echo $endDate; ?>'		
					});
						$('.daterange').on('apply.daterangepicker', function (ev, picker) {
							$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
						});
					$('.daterange').on('cancel.daterangepicker', function (ev, picker) {
						$(this).val('');
					});
			</script>
		<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Product Name </th> 
					<th>Quantity</th>
					<th>Price</th>					
					<th>Fuel Taxes</th>				
					<th>Amount</th>
					<th>Commission</th>
					<th>Date & Time</th>						
				</tr>
			</thead>
			<tbody>
			 <?php 
				$subTotal = 0; $gstCount = 0; $pstCount = 0; $qstCount = 0; $grandTotal = $grand_total_amt = $grand_commision_amt = 0;
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
					// pre($productQuantity);
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
					// $subTotal += $amount - $totalTaxAmount;
					// $gstCount += floor($finalGST*100)/100;
					// $pstCount += floor($finalPST*100)/100;
					// $qstCount += floor($finalQST*100)/100;
					// $grandTotal += floor($amount*100)/100;
				?>								
				<?php 
					$multi_trans++;	//$subTotal += $amtAfterTax;							
				}
				?>
				<tr>	
						<td><?= $productName?></td>
						<td><?= $productQuantity?></td>
						<td><?php echo $prideDieselPrice;?></td>														
						<td><?php echo $totalTaxAmount ?></td>														
						<td><?php echo $amount?></td>
						<?php
						$grand_total_amt +=$amount;
							if($productName != 'DEFD'){	
								if($amount< 1){
									$commision = ($amount*0.05);
								}else{	
									$commision = ($amount*0.05)/100;
									$commision = floor($commision*100)/100;
								}
							$comm =  $commision;
							}else{
								$comm =  '0.00';
							}
								$grand_commision_amt += $comm;
							?>													
							<td><?php echo $comm; ?></td>
						
						<td><?= $card->transaction_date?></td>
							<input type="hidden" class="" name="transaction_date[]" value="<?= $card->transaction_date?>" />
					</tr>
				<?php	
					unset($taxOutput, $ppfTot, $gst, $pst);
					}
					?>
				<tr>
					<td colspan="4" align="right"><b>Total</b></td>
					<td><b><?php echo $grand_total_amt;?></b></td>
					<td><b><?php echo $grand_commision_amt; ?> </b></td>
					<td></td>
				</tr>								
		</tbody>
	</table>
		<div class="row">
			<div class="col-md-5 col-sm-12">
			</div>
			<div class="col-md-7 col-sm-12">
				<div class="pagination-container">
					<?php echo $pagination ?>
				</div>				
			</div>				
		</div>				
	</div>
	</div>
</section>
</div>	