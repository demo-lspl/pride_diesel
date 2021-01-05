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
					<th>#</th> 
					<th>Card Number</th>
					<th>Qty Total</th>
                    <th>Grand Total</th>
					<th>Rebate</th>		
                    <th>Cost</th>					
                    <th>Profit</th>					
                    <th>Commission</th>					
                  	<th>Date & Time</th>				
				</tr>
			</thead>
			<tbody>
			 <?php 
				$total_amount = $total_qty =   0;
					foreach($cardsTransData as $trans_AMT_details){
						
						//if($trans_AMT_details->billing_currency == 'CAD'){
							$amount = json_decode($trans_AMT_details->amount);
							$cat = json_decode($trans_AMT_details->category);
							$QTY = json_decode($trans_AMT_details->quantity);
							$pride_price = json_decode($trans_AMT_details->pride_price);
							$more_transc = 0;
							
							
							$total_qty = $total_amount = $sale_total = 0;
							foreach($amount as $total_amtt){
								
								$amount_chk = $amount[$more_transc];
								$cats = $cat[$more_transc];
								$QTYss = $QTY[$more_transc];
								$pride_prices = $pride_price[$more_transc];
								
								if($cats != 'DEFD'){
									$grnd_total = $pride_prices * $QTYss;
									$grnd_total = floor($grnd_total*100)/100;
									$total_amount +=$amount_chk;
									$total_qty +=$QTYss;
									$sale_total +=$grnd_total;
								}
								
								$more_transc++; 
							}
							
							if($trans_AMT_details->billing_currency == 'CAD'){
								$Qty_rebate = ($total_qty*0.05);
								$Qty_rebate = floor($Qty_rebate*100)/100;
								$cost = $total_amount - $Qty_rebate;
								$Qty_rebate = floor($Qty_rebate*100)/100;
								$total_QTYS = floor($total_qty*100)/100;
								$profit = $sale_total - $cost;
							}else{
								$Qty_rebate = ($total_qty*0.05);
								$Qty_rebate = floor($Qty_rebate*100)/100;
								$cost = $total_amount;
								$Qty_rebate = '0.00';
								$total_QTYS = floor($total_qty*100)/100;
								$profit = $sale_total - $cost;
							}
					
					//Commission According To Slab
					   if($profit <= 200000){
						   $commission = $profit*10/100;
					   }elseif($profit >= 300000){
						   $commission = $profit*20/100;
					   }
					   $commission = floor($commission*100)/100;
					//Commission According To Slab   
					 echo '<tr>';
					 echo '<td>'.$trans_AMT_details->id.'</td>';
					 echo '<td>'.$trans_AMT_details->card_number.'</td>';
					 echo '<td>'.$total_qty.'</td>';
					 echo '<td>'.$sale_total.'</td>';
					 echo '<td>'.$Qty_rebate.'</td>';
					 echo '<td>'.$cost.'</td>';
					 echo '<td>'.$profit.'</td>';
					 echo '<td>'.$commission.'</td>';
					 echo '<td>'.$trans_AMT_details->transaction_date.'</td>';
					 //echo '<td><a href="'.base_url("account/view_crd_trns_dtls/").$dtld->card_number.'" class="btn btn-default" ><i class="fa fa-eye" aria-hidden="true"></i></a> </td>';
				
			}
		?>	
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