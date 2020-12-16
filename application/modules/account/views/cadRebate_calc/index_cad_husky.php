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
				<form class="search form-inline" action="<?php echo base_url().'account/husky_rebate'; ?>" method="get" autocomplete="off">

				  <div class="form-group">

					<!--input class="form-control search-input" name="card_search" value="<?php //if(!empty($_GET['card_search'])){echo $_GET['card_search'];} ?>" placeholder="<?php //echo "Search by card"; ?>" type="text" /-->

					<input type="text" name="date_range" class="daterange form-control"  />&nbsp;&nbsp;

				  </div>

				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
					<a href="<?php echo base_url(); ?>account/husky_rebate">
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

					

				}?>

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
					<th>Id</th> 
					<th>Company Name</th>
					<th>Company Address</th>					
					<th>Invoice ID</th>
					<th>Currency</th>
					<th>Grand Amount</th>
					<th>Created On</th>				
					<th>Action</th> 			
								
				</tr>
			</thead>
			<tbody>
			  <?php if(!empty($allInvoices)){ ?>
			  <?php foreach($allInvoices as $invoicevalues): 
			     $new_Created_Date = date("d-M-Y", strtotime($invoicevalues->date_created));
				 
				 //$get_transaction_details = getNameById('transaction_invoice',$invoicevalues->id,'id');
				 
				
					
			  ?>
			<tr>
				<td><?= $invoicevalues->id;?></td>
				<td><?= $invoicevalues->company_name;?></td>
				<td><?= $invoicevalues->address;?></td>
				<td><?= $invoicevalues->invoice_id;?></td>
				<td><?= $invoicevalues->billingCurrency;?></td>
				<td><?= bcdiv($invoicevalues->grand_total ,1,2);?></td>	
				<td><?= $new_Created_Date;?></td>
				<td><a href="<?php echo base_url('account/husky_rebate_per_invoice/').$invoicevalues->id; ?>" class="btn btn-default" ><i class="fa fa-eye" aria-hidden="true"></i></a></td>	
			</tr>
			  
			  <?php endforeach; ?>
			  <?php }else{ echo "<tr>
				<td colspan='7'>No record found</td>
			  </tr>"; }  ?>
			  </tbody>
			  <?php 
			  $amt_total = $Qty_total = $Rebte_total = 0;
			  foreach($allInvoices_rebate_calc as $invoicevalues){
				
				//$get_transaction_details = getNameById('transaction_invoice',$invoicevalues->id,'id');
				$trans_data = json_decode($invoicevalues->trans_data);
				
				$trans_id_data = json_decode($invoicevalues->invoice_data);
				
				
				
				$more_transc = 0;
					foreach($trans_data as $cate_val){
						//pre($cate_val);
						foreach($cate_val as $Trans_data){
							
							
						
						$cat_name = $Trans_data->product_name;
						$quantity = $Trans_data->quantity;
						$unit_price = $Trans_data->unit_price;
						//$taxamount = $Trans_data->taxamount;
						$amountwithouttax = $Trans_data->amountwithouttax;
						
                        $total = $quantity * $unit_price;
                      
						
						  $amt_total +=$total;
						  $Qty_total +=$quantity;
						 
						
                       			
			  
					}
				}
				
				/* For Getting pride Price and Unit price of Transcations */
				//$data_transactions = [];
				
				foreach($trans_id_data as $trans_id){
				   $trans_idd = $trans_id->transaction_id;
				   $get_transaction_details[] = getNameById_new('transactions',$trans_idd,'transaction_id');
				  // pre($get_transaction_details);
				   $data_transactions = $get_transaction_details;
				  
				}
			   /* For Getting pride Price and Unit price of Transcations */
			  }
			  ?>
			   <tr><td colspan="7"><td/></tr>
			  <tr><td colspan="7"><td/></tr>
			   <tr>
				  <td colspan="5" align="right"></td>
				  <th>Amount</th>
				  <th>Qty</th>
				  <!--th>Rebate</th-->  
			  </tr>
               <tr>
			  <td colspan="5" align="right"><b>Total Fuel Qty and Cost</b></td>
			  <td><?php echo bcdiv($amt_total,1,2); ?></td>
			  <td><?php echo bcdiv($Qty_total,1,2); ?></td>
			  <td><?php //echo number_format($Rebte_total,2); ?></td>
			  </tr>
			  		
			<?php
			if(!empty($data_transactions)){
				$unique_data = array_unique(array_column($data_transactions, 'transaction_id'));
				foreach($unique_data as $trans_id){
					$get_transaction_details1[] = getNameById_new('transactions',$trans_id,'transaction_id');
					$data_transactions1 = $get_transaction_details1;
					
				}
				
					$Unit_PRICE_total = $Pride_price_total = $QQTY = 0;
					foreach($data_transactions1 as $val){
						$unit_price = json_decode($val['unit_price']);
						$pride_price = json_decode($val['pride_price']);
						$qttys = json_decode($val['quantity']);
						$more_transc = 0;
						foreach($unit_price as $unit_val){
							 $unt_p = $unit_price[$more_transc];
							 $prd_prce = $pride_price[$more_transc];
							 $QQTY = $qttys[$more_transc];
							 
							
							
							 // echo $unit_into_qty.'<br/>';
							$more_transc++; 
							  $unit_into_qty = $unt_p * $QQTY;
							  $pride_price_into_qty = $prd_prce * $QQTY;
							
						}
						
					  $Unit_PRICE_total  += $unit_into_qty; 
					  
					 
					  
					  
					  $Pride_price_total += $pride_price_into_qty;
						
				}
			
			?>
			  <tr><td colspan="7"><td/></tr>
			  <tr><td colspan="7"><td/></tr>
			   <tr>
				<td colspan="6" align="right"><b>Invoice Pricing</b></td>
				<td><?php
					echo bcdiv($Pride_price_total ,1,2);
				?></td>
				
				<td></td>
			  </tr>
			  <tr>
				<td colspan="6" align="right"><b>HUSKY Pricing</b></td>
				<td><?php
					echo bcdiv($Unit_PRICE_total ,1,2);
				?></td>
				
				<td></td>
			  </tr>
			  
			  <tr>
				<td colspan="6" align="right"><b>Profit</b></td>
				<td><?php
				  $net_profit = $Pride_price_total - $Unit_PRICE_total;
					echo bcdiv($net_profit ,1,2);
				?></td>
				
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