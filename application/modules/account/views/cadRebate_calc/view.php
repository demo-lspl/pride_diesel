<!-- Main Container -->
<div class="">


	<section class="content box">
	<div class="addnew-user"><!--a href="<?php //echo base_url('account/invoice_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Invoice</a--></div>
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Invoice</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<!--form class="search form-inline" action="<?php echo base_url().'account/cad_rebate_calc'; ?>" method="get" autocomplete="off">

				  <div class="form-group">


					<input type="text" name="date_range" class="daterange form-control"  />&nbsp;&nbsp;

				  </div>

				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
					<a href="<?php echo base_url(); ?>account/cad_rebate_calc">
						<input type="button" name="submitSearchReset" class="btn btn-primary" value="Reset"></a>
				</form-->
				<a href="<?php echo base_url(); ?>account/cad_rebate_calc"><input type="button" name="submitSearchReset" class="btn btn-primary" value="Back"></a><br/>
				
				
			
			  <?php 
			   $amt_total = $Qty_total = $Rebte_total = 0;
			  	$count = 0;
			  foreach($allInvoices_rebate_calc as $invoicevalues){ 
			    //$get_transaction_details = getNameById('transaction_invoice',$invoicevalues->id,'id');
				$trans_data = json_decode($invoicevalues->trans_data);
				$trans_id_data = json_decode($invoicevalues->invoice_data);
				
				
				foreach($trans_id_data as $trans_id){
				   $trans_idd = $trans_id->transaction_id;
				   $get_transaction_details[] = getNameById_new('transactions',$trans_idd,'transaction_id');
				   $data_transactions = $get_transaction_details;
				}
				//pre($data_transactions);
				
				$count++;
			  }
			  
			   $unique_data = array_unique(array_column($data_transactions, 'transaction_id'));
			   
				foreach($unique_data as $trans_id){
					$get_transaction_details1[] = getNameById_new('transactions',$trans_id,'transaction_id');
					$data_transactions1 = $get_transaction_details1;
					//
				}
				
				
				
				
			  ?>
			  <table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Id</th> 
					<th>Card Number</th>
					<th>Currency</th>					
					<th>State</th>
					<th>City</th>
					<th>Transcation ID</th>
					<th>Quantity</th>
					<th>Grand Total</th>
					<th>Created On</th>
                </tr>
			</thead>
				<?php 
				  $Unit_PRICE_total = $Pride_price_total = $QQTY = $pride_total_per_invoice = 0;
				    $count = 0;
					foreach($data_transactions1 as $val){
						
						$new_Created_Date = date("d-M-Y", strtotime($val['date_created']));
						$unit_price = json_decode($val['unit_price']);
						$pride_price = json_decode($val['pride_price']);
						$qttys = json_decode($val['quantity']);
						$amount = json_decode($val['amount']);
						$cat = json_decode($val['category']);
						
						
						$more_transc = 0;
					
					foreach($unit_price as $unit_val){
						 $unt_p = $unit_price[$more_transc];
						 $prd_prce = $pride_price[$more_transc];
						 $QQTY = $qttys[$more_transc];
						 $amount_chk = $amount[$more_transc];
						 $cat = $cat[$more_transc];
						
						 $unit_into_qty = $unt_p * $QQTY;
						 $pride_price_into_qty = $prd_prce * $QQTY;
						  $prid_into_qty[] =  $pride_price_into_qty;
						
						 
							$rebate_amt = $QQTY * 0.068;
							if($cat == 'DEFD'){
								$withrbate_Amt = $amount_chk;
							}else{
								$withrbate_Amt = $amount_chk - $rebate_amt;
							}
							$single_unit_amt = $withrbate_Amt / $QQTY;
							$Qty_total +=$QQTY;
							$total = $QQTY * $single_unit_amt;
							$amt_total +=$total;
							
						
							$Rebte_total +=$rebate_amt;
							$defd_Amt; $defd_Qty; 
								if($cat == 'DEFD'){
									@$defd_Amt += $total;
									@$defd_Qty += $QQTY;
								}	
						 
						 
						 
						 
						 
						 
						 $more_transc++; 
					}
					
				
					$total_prid_qty = array_sum($prid_into_qty);
					$pride_total_per_invoice = $pride_price_into_qty;
				?>
				<tr>
					<td><?= $val['id'];?></td>
					<td><?= $val['card_number'];?></td>
					<td><?= $val['billing_currency'];?></td>
					<td><?= $val['gas_station_name'];?></td>
					<td><?= $val['gas_station_city'];?></td>
					<td><?= $val['gas_station_state'];?></td>	
					<td><?= $val['transaction_id'];?></td>	
					
					<td><?= bcdiv($pride_total_per_invoice,1,2);?></td>	
					<td><?= $new_Created_Date;?></td>				
				</tr>
			</tbody>
	<?php
	$count++;
		} 
		?>
			   <tr><td colspan="8"><td/></tr>
			  <tr><td colspan="8"><td/></tr>
			   <tr>
				  <td colspan="6" align="right"></td>
				  <th>Amount</th>
				  <th>Qty</th>
				  <th>Rebate</th>  
			  </tr>
               <tr>
			  <td colspan="6" align="right"><b>Total Fuel Qty and Cost</b></td>
			  <td><?php echo bcdiv($total_prid_qty,1,2); ?></td>
			  <td><?php echo bcdiv($Qty_total,1,2); ?></td>
			  <td><?php echo bcdiv($Rebte_total,1,2); ?></td>
			  </tr>
			  <tr><td colspan="8"><td/></tr>
			  <tr><td colspan="8"><td/></tr>
			 
			  <tr>
				<td colspan="6" align="right"><b>Total Diesel</b></td>
				<td><?php
					$ttl_amtt =  $total_prid_qty - @$defd_Amt; 
					echo bcdiv($ttl_amtt ,1,2);
				?></td>
				<td><?php 
					$ttl_qty =  $Qty_total - @$defd_Qty; 
					echo bcdiv($ttl_qty ,1,2);
					?>
				</td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="6" align="right"><b>Total Def</b></td>
				<td><?php echo bcdiv(@$defd_Amt,1,2); ?></td>
				<td><?php echo bcdiv(@$defd_Qty,1,2); ?></td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="6" align="right"><b>Total Fuel Qty and Cost</b></td>
				<td><?php echo bcdiv($total_prid_qty,1,2); ?></td>
				<td><?php echo bcdiv($Qty_total,1,2); ?></td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="6" align="right"><b>Rebate</b></td>
				<td><?php echo bcdiv($Rebte_total,1,2); ?></td>
				<td></td>
				<td></td>
			  </tr>
			   <tr>
				  <td colspan="6" align="right"><b>Transcation FEES</b></td>
				  <td >$1.25 x <?php echo $count; ?>  =  <?php echo $count * 1.25; ?></td>
			  </tr>
			  <tr>
				<td colspan="6" align="right"><b>Actual Cost of Fuel</b></td>
				<td><?php 
				       $Actl_cost = $total_prid_qty - $Rebte_total;
						echo bcdiv($Actl_cost,1,2); 
					?></td>
				<td></td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="6" align="right"><b>Profit</b></td>
				<td><?php 
				       $profittt = $total_prid_qty - $Actl_cost;
						echo bcdiv($profittt,1,2); 
					?></td>
				<td></td>
				<td></td>
			  </tr>	
			 
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