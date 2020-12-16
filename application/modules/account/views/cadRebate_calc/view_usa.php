<!-- Main Container -->
<div class="">
	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Invoice</h3>
				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<a href="<?php echo base_url(); ?>account/usa_rebate_calc"><input type="button" name="submitSearchReset" class="btn btn-primary" value="Back"></a><br/>
			 <?php 
			  $amt_total = $Qty_total = $Rebte_total = 0;
			  foreach($allInvoices_rebate_calc as $invoicevalues){
				$trans_data = json_decode($invoicevalues->trans_data);
				$trans_id_data = json_decode($invoicevalues->invoice_data);
				
				
				/* For Getting pride Price and Unit price of Transcations */
				//$data_transactions = [];
			foreach($trans_id_data as $trans_id){
				   $trans_idd = $trans_id->transaction_id;
				   $get_transaction_details[] = getNameById_new('transactions',$trans_idd,'transaction_id');
				   $data_transactions = $get_transaction_details;
				}
			   /* For Getting pride Price and Unit price of Transcations */
			  }
			
				$unique_data = array_unique(array_column($data_transactions, 'transaction_id'));
			 	foreach($unique_data as $trans_id){
					$get_transaction_details1[] = getNameById_new('transactions',$trans_id,'transaction_id');
					$data_transactions1 = $get_transaction_details1;
				}
			?>
			<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Id</th> 
					<th>Card Number</th>
					<th>Invoice ID</th>
					<th>Currency</th>					
					<th>Gas Station Name</th>
					<th>State</th>
					<th>City</th>
					<th>Transcation ID</th>
					<th>Grand Total</th>
					<th>Created On</th>
                </tr>
			</thead>
			<tbody>
				<?php
					$Unit_PRICE_total = $Pride_price_total = $QQTY = $pride_total_per_invoice =   $amount_grd_totl = 0;
					 $count = 0;
					foreach($data_transactions1 as $val){
						
						$new_Created_Date = date("d-M-Y", strtotime($val['date_created']));
						$unit_price = json_decode($val['unit_price']);
						$pride_price = json_decode($val['pride_price']);
						$qttys = json_decode($val['quantity']);
						$amount = json_decode($val['amount']);
						$more_transc = 0;
					$prid_into_qty =  array();
					foreach($unit_price as $unit_val){
						 $unt_p = $unit_price[$more_transc];
						 $prd_prce = $pride_price[$more_transc];
						 $QQTY = $qttys[$more_transc];
						 $amount_chkk = $amount[$more_transc];
						
						 $unit_into_qty = $amount_chkk;
						 $pride_price_into_qty = $prd_prce * $QQTY;
						 $prid_into_qty[] =  $pride_price_into_qty;
						 $Qty_total += $QQTY;
						 
						 $amount_grd_totl += $amount_chkk;
						 $more_transc++; 
					}
						$total_prid_qty = array_sum($prid_into_qty);
						$amt_total += $total_prid_qty;
						$Unit_PRICE_total  += $unit_into_qty; 
						$Pride_price_total += $total_prid_qty;
						$pride_total_per_invoice += $pride_price_into_qty;
						
						$Invoice_id = $this->uri->segment(3);
						
				?>
				<tr>
					<td><?= $val['id'];?></td>
					<td><?= $val['card_number'];?></td>
					<td><?= 'CL'.$Invoice_id;?></td>
					<td><?= $val['billing_currency'];?></td>
					<td><?= $val['gas_station_name'];?></td>
					<td><?= $val['gas_station_city'];?></td>
					<td><?= $val['gas_station_state'];?></td>	
					<td><?= $val['transaction_id'];?></td>	
					
					<td><?= bcdiv($total_prid_qty,1,2);?></td>	
					<td><?= $new_Created_Date;?></td>				
				</tr>
			</tbody>
				<?php		
			 $count++;		
			}
		?>
				<tr><td colspan="9"><td/></tr>
				<tr><td colspan="9"><td/></tr>
			   <tr>
				  <td colspan="8" align="right"></td>
				  <th>Amount</th>
				  <th>Qty</th>
				</tr>
               <tr>
				  <td colspan="8" align="right"><b>Total Fuel Qty and Cost</b></td>
				  <td><?php echo bcdiv($amt_total,1,2); ?></td>
				  <td><?php echo bcdiv($Qty_total,1,2); ?></td>
			  </tr>
				<tr><td colspan="9"><td/></tr>
				<tr><td colspan="9"><td/></tr>
			   <tr>
				<td colspan="8" align="right"><b>Invoice Pricing</b></td>
				<td><?php echo bcdiv($Pride_price_total ,1,2); ?></td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="8" align="right"><b>EFS Pricing</b></td>
				<td><?php echo bcdiv($amount_grd_totl ,1,2);?></td>
				<td></td>
			  </tr>
			   <tr>
				  <td colspan="8" align="right"><b>Transcation FEES</b></td>
				  <td >$1.25 x <?php echo $count; ?>  =  <?php echo $count * 1.25; ?></td>
			  </tr> 
			  <tr>
				<td colspan="8" align="right"><b>Profit</b></td>
				<td><?php
					//$net_profit = $Pride_price_total  -  $Unit_PRICE_total;
					$net_profit = $Pride_price_total - $amount_grd_totl ;
					echo bcdiv($net_profit ,1,2);
				?></td>
				<td></td>
			  </tr>
			</table>
				<div class="row">
					<div class="col-md-7 col-sm-12">
					
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