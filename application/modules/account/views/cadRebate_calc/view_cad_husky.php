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
				<a href="<?php echo base_url(); ?>account/husky_rebate"><input type="button" name="submitSearchReset" class="btn btn-primary" value="Back"></a><br/>
			<?php 
			
			  $amt_total = $Qty_total = $Rebte_total = 0;
			  foreach($allInvoices_rebate_calc as $invoicevalues){
				$trans_data = json_decode($invoicevalues->trans_data);
				$trans_id_data = json_decode($invoicevalues->invoice_data);
				$more_transc = 0;
					foreach($trans_data as $cate_val){
						foreach($cate_val as $Trans_data){
						$cat_name = $Trans_data->product_name;
						$quantity = $Trans_data->quantity;
						$unit_price = $Trans_data->unit_price;
						$amountwithouttax = $Trans_data->amountwithouttax;
				        $total = $quantity * $unit_price;
                		  $amt_total +=$total;
						  $Qty_total +=$quantity;
					}
				}
				
				/* For Getting pride Price and Unit price of Transcations */
				foreach($trans_id_data as $trans_id){
				   $trans_idd = $trans_id->transaction_id;
				   $get_transaction_details[] = getNameById_new('transactions',$trans_idd,'transaction_id');
				   $data_transactions = $get_transaction_details;
				  
				}
			   /* For Getting pride Price and Unit price of Transcations */
			  }
			 
			  ?>
			
			  		
			<?php
			if(!empty($data_transactions)){
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
					<th>State</th>
					<th>City</th>
					<th>Invoice ID</th>
					<th>Transcation ID</th>
					<th>Category</th>
					<th>Grand Total</th>
					<th>Quantity</th>
					<th>Created On</th>
                </tr>
				</thead>
				<tbody>
			<?php	
					$Unit_PRICE_total = $Pride_price_total = $QQTY = $pride_total_per_invoice = 0;
					foreach($data_transactions1 as $val){
						
						
						$new_Created_Date = date("d-M-Y", strtotime($val['date_created']));
						$unit_price = json_decode($val['unit_price']);
						$pride_price = json_decode($val['pride_price']);
						$qttys = json_decode($val['quantity']);
						$category = json_decode($val['category']);
						$more_transc = 0;
						
						foreach($unit_price as $unit_val){
							$husky_tax = getNameById_state('tax',$val['gas_station_state'],'state');
							$total_tax = 0;
							foreach($husky_tax as $tax_rate){
								$total_tax += str_replace('%','',$tax_rate->tax_rate);
							}
						
							 $unt_p = $unit_price[$more_transc];
							 $prd_prce = $pride_price[$more_transc];
							 $QQTY = $qttys[$more_transc];
							 $cat = $category[$more_transc];
							 $unit_into_qty = $unt_p * $QQTY;
							 $pride_price_into_qty = (floor($prd_prce*100)/100) * $QQTY;
							 $huskyTAX_amt = $unit_into_qty*$total_tax/100;
							$more_transc++; 
						}
						$Unit_PRICE_total  += $unit_into_qty + $huskyTAX_amt; 
						$Pride_price_total += $pride_price_into_qty;
						$pride_total_per_invoice = $pride_price_into_qty;
						$invoice_id = $this->uri->segment(3);
				?>		
					<tr>
					<td><?= $val['id'];?></td>
					<td><?= $val['card_number'];?></td>
					<td><?= $val['gas_station_city'];?></td>
					<td><?= $val['gas_station_state'];?></td>
					<td><?= 'CL'.$invoice_id;?></td>	
					<td><?= $val['transaction_id'];?></td>
                    <td><?= $cat;?></td>					
					<td><?= floor($pride_total_per_invoice*100)/100;?></td>
					<td><?= $QQTY;?></td>	
					<td><?= $new_Created_Date;?></td>				
				</tr>
			</tbody>	
						
			<?php }  ?>
			  <tr><td colspan="9"><td/></tr>
			  <tr><td colspan="9"><td/></tr>
			   <tr>
				  <td colspan="7" align="right"></td>
				  <th>Amount</th>
				  <th>Qty</th>
				  <!--th>Rebate</th-->  
			  </tr>
               <tr>
				  <td colspan="7" align="right"><b>Total Fuel Qty and Cost</b></td>
				  <td><?php echo floor($amt_total*100)/100; ?></td>
				  <td><?php echo floor($Qty_total*100)/100; ?></td>
				  <td><?php //echo number_format($Rebte_total,2); ?></td>
			  </tr>
			  <tr><td colspan="9"><td/></tr>
			  <tr><td colspan="9"><td/></tr>
			   <tr>
				<td colspan="7" align="right"><b>Invoice Pricing</b></td>
				<td><?php
					echo floor($Pride_price_total*100)/100;
				?></td>
				
				<td></td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="7" align="right"><b>HUSKY Pricing</b></td>
				<td><?php
					echo floor($Unit_PRICE_total*100)/100;
				?></td>
				
				<td></td>
			  </tr>
			  
			  <tr>
				<td colspan="7" align="right"><b>Profit</b></td>
				<td><?php
				  $net_profit =  (floor($Pride_price_total*100)/100) - (floor($Unit_PRICE_total*100)/100);
					echo floor($net_profit*100)/100;
				?></td>
				
				<td></td>
				<td></td>
			  </tr>
			<?php } ?> 
			 <!--tr><td colspan="4" align="right"></td> <td>Amount</td><td>Qty</td></tr-->
			 
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