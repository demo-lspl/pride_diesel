<div class="">
	<section class="content box">
		<div class="addnew-user">
			<!--a href="<?php //echo base_url('account/invoice_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Invoice</a-->
		</div>
	<div class="card card-default">
		<div class="card-header bg-card-header">
			<h3 class="card-title">Card Commission Details</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
					<button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
		</div>
		<div class="card-body">
			<form class="search form-inline" action="<?php echo base_url().'account/get_cadEFS_Trans/'.$this->uri->segment(3);?>" method="get" autocomplete="off">
					<div class="form-group">
						<input type="text" name="date_range" class="daterange form-control"  />&nbsp;&nbsp;
					</div>
					<button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
					<a href="<?php echo base_url(); ?>account/get_cadEFS_Trans/<?php echo $this->uri->segment(3); ?>">
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
					<th>Transaction Date</th> 
					<th>Card Number</th>
					<th>City</th>					
					<th>Province</th>
                    <th>Category</th>
                    <th>Cost Amount</th>
					<th>Quantity</th>	
                    <th>Sale Amount</th>
                    <th>Sale QTY</th>
						
                    <th>Currency</th>					
                    <th>Transaction Date</th>					
                  	<!--th>View Transaction</th-->					
				</tr>
			</thead>
			<tbody>
			 <?php
				if(!empty($all_trans_pagination)){
					//pre($all_trans_pagination);
					$total_saleAmt1 = $Qty_total1 = $total_CostAmt1 = $toalSaleAmount1 = $totalRebate1 = $totalDeff1 = $totalDeffqty1 = $total_QTY_total1 = 0 ;
				 foreach($all_trans_pagination as $dtld){
					 //pre($dtld);
					 $newDate = date("Y-m-d", strtotime($dtld->transaction_date));
					 
					$amount = json_decode($dtld->amount);
					$cat = json_decode($dtld->category);
					$QTY = json_decode($dtld->quantity);
					$pride_price = json_decode($dtld->pride_price);
					$more_transc = 0;
					$total_amount1 = $total_qty1 =  $sale_total1 =  $total_amount_all1 =  $all_Sale_total1 =  $total_deff_amount1 = $total_deff_qty1 = $rebateAmt = 0;
					foreach($amount as $total_amtt){
						$amount_chk = $amount[$more_transc];
						$cats = $cat[$more_transc];
						$QTYss = $QTY[$more_transc];
						$pride_prices = $pride_price[$more_transc];
						$total_amount_all1 +=$amount_chk;
						//if($cats != 'DEFD'){
							$grnd_total = $pride_prices * $QTYss;
							$grnd_total = floor($grnd_total*100)/100;

							$total_amount1 +=$amount_chk;
							$total_qty1 +=$QTYss;
							$sale_total1 +=$grnd_total;
						//}
						
						$Qty_total1 +=$total_qty1;
						$all_Sale_total1 +=$grnd_total;
						if($cats != 'DEFD'){
							$total_saleAmt1 +=$total_amount_all1;
							$rebateAmt = $total_qty1*0.05;
							$rebateAmt = floor($rebateAmt*100)/100;
							
						}else{
							$total_deff_amount1 = $amount_chk;
							$total_deff_qty1 = $total_qty1;
						}
						
						
						$more_transc++; 
					}
					
					$totalDeffqty1 += $total_deff_qty1;
					$totalDeff1 += $total_deff_amount1;
					$totalRebate1 += $rebateAmt;
					$toalSaleAmount1 += $all_Sale_total1;
					 $total_CostAmt1 += $amount_chk;
					 $total_QTY_total1 += $QTYss;
					 $afterMinus_Deff_amount = $total_CostAmt1 - $totalDeff1;
					 $afterMinus_Deff_Qty = $total_QTY_total1 - $totalDeffqty1;
					 echo '<tr>';
					 echo '<td>'.$newDate.'</td>';
					 echo '<td>'.substr($dtld->card_number, -4).'</td>';
					 echo '<td>'.$dtld->gas_station_city.'</td>';
					 echo '<td>'.$dtld->gas_station_state.'</td>';
					 echo '<td>'.$cats.'</td>';
					 echo '<td>'.$total_amount_all1.'</td>';
					 echo '<td>'.$QTYss.'</td>';
					 echo '<td>'.$all_Sale_total1.'</td>';
					 echo '<td>'.$QTYss.'</td>';
					
					 echo '<td>'.$dtld->billing_currency.'</td>';
					 echo '<td>'.$dtld->transaction_date.'</td>';
					
					 echo '</tr>';
				
			}
					
}


					
			 if(!empty($all_trans)){
			$total_saleAmt = $Qty_total = $total_CostAmt = $toalSaleAmount = $totalRebate = $totalDeff = $totalDeffqty = $total_QTY_total = 0 ;
				 foreach($all_trans as $dtld){
					 //pre($dtld);
					 $newDate = date("Y-m-d", strtotime($dtld->transaction_date));
					 
					$amount = json_decode($dtld->amount);
					$cat = json_decode($dtld->category);
					$QTY = json_decode($dtld->quantity);
					$pride_price = json_decode($dtld->pride_price);
					$more_transc = 0;
					$total_amount = $total_qty =  $sale_total =  $total_amount_all =  $all_Sale_total =  $total_deff_amount = $total_deff_qty = 0;
					foreach($amount as $total_amtt){
						$amount_chk = $amount[$more_transc];
						$cats = $cat[$more_transc];
						$QTYss = $QTY[$more_transc];
						$pride_prices = $pride_price[$more_transc];
						$total_amount_all +=$amount_chk;
						//if($cats != 'DEFD'){
							$grnd_total = $pride_prices * $QTYss;
							$grnd_total = floor($grnd_total*100)/100;

							$total_amount +=$amount_chk;
							$total_qty +=$QTYss;
							$sale_total +=$grnd_total;
						//}
						
						$Qty_total +=$total_qty;
						$all_Sale_total +=$grnd_total;
						if($cats != 'DEFD'){
							$total_saleAmt +=$total_amount_all;
							$rebateAmt = $total_qty*0.05;
							$rebateAmt = floor($rebateAmt*100)/100;
							
						}else{
							$total_deff_amount = $amount_chk;
							$total_deff_qty = $total_qty;
						}
						
						
						$more_transc++; 
					}
					
					$totalDeffqty += $total_deff_qty;
					$totalDeff += $total_deff_amount;
					$totalRebate += $rebateAmt;
					$toalSaleAmount += $all_Sale_total;
					 $total_CostAmt += $amount_chk;
					 $total_QTY_total += $QTYss;
					 $afterMinus_Deff_amount = $total_CostAmt - $totalDeff;
					 $afterMinus_Deff_Qty = $total_QTY_total - $totalDeffqty;
					
				
			}
			
			
			$rebate_amount_total = $afterMinus_Deff_Qty*0.05;
			
			$rebate_amount_total = floor($rebate_amount_total*100)/100;
			      $total_cost    = $total_CostAmt;
				  
				  $profit =     $toalSaleAmount - $total_cost;
				echo '<tr>';
				echo '<td colspan="5" align="right"><b>Total </b></td>';
				echo '<td><b>'.$total_CostAmt.'</b></td>';
				echo '<td><b>'.$Qty_total.'</b></td>';
				echo '<td><b>'.$toalSaleAmount.'</b></td>';
				echo '<td><b>'.$Qty_total.'</b></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td  colspan="5" align="right"><b>Deff </b></td>';
				echo '<td><b>'.$totalDeff.'</b></td>';
				echo '<td><b>'.$totalDeffqty.'</b></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td  colspan="5" align="right"><b>Diesel</b></td>';
				echo '<td><b>'.$afterMinus_Deff_amount.'</b></td>';
				echo '<td><b>'.$afterMinus_Deff_Qty.'</b></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '</tr>';
				
				echo '<tr>';
				echo '<td  colspan="5" align="right"><b>Cost</b></td>';
				echo '<td><b>'.$total_cost.'</b></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '</tr>';
				
				echo '<tr>';
				echo '<td  colspan="5" align="right"><b>Profit</b></td>';
				echo '<td><b>'.$profit.'</b></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '</tr>';
				
				echo '<tr><td colspan="12" ></td></tr>';
				echo '<tr><td colspan="12" ></td></tr>';
				
				
				
		}else{ 
				echo "<tr>
					<td colspan='11'>No record found</td>
				</tr>"; 
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