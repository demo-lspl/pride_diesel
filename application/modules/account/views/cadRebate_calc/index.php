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
				<form class="search form-inline" action="<?php echo base_url().'account/cad_rebate_calc'; ?>" method="get" autocomplete="off">

				  <div class="form-group">

					<!--input class="form-control search-input" name="card_search" value="<?php //if(!empty($_GET['card_search'])){echo $_GET['card_search'];} ?>" placeholder="<?php //echo "Search by card"; ?>" type="text" /-->

					<input type="text" name="date_range" class="daterange form-control"  />&nbsp;&nbsp;

				  </div>

				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
					<a href="<?php echo base_url(); ?>account/cad_rebate_calc">
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
				<td><?= $invoicevalues->grand_total;?></td>	
				<td><?= $new_Created_Date;?></td>				
				<td><a href="<?php echo base_url('account/view_invoice/').$invoicevalues->id; ?>" class="btn btn-default" ><i class="fa fa-eye" aria-hidden="true"></i></a> </td>				
			</tr>
			  
			  <?php endforeach; ?>
			 
			  </tbody>
			  <?php 
			  $amt_total = $Qty_total = $Rebte_total =  $EFS_amount = 0;
				    $totDieselEFS = $count = $prideTotal = 0;
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
				
				foreach($data_transactions1 as $trns_val){
						$new_Created_Date = date("d-M-Y", strtotime($trns_val['date_created']));
						$unit_price = json_decode($trns_val['unit_price']);
						$pride_price = json_decode($trns_val['pride_price']);
						$qttys = json_decode($trns_val['quantity']);
						$amount = json_decode($trns_val['amount']);
						$cat = json_decode($trns_val['category']);
						
						
						$more_transc = 0;
						 foreach($unit_price as $unit_val){
						 $unt_p = $unit_price[$more_transc];
						 $prd_prce = $pride_price[$more_transc];
						 $QQTY = $qttys[$more_transc];
						 $amount_chk = $amount[$more_transc];
						 $cat = $cat[$more_transc];
						
						 $unit_into_qty = $unt_p * $QQTY;
						 
						 $pride_price_into_qty =  $prd_prce * $QQTY;
						  $prid_into_qty[] =  floor($pride_price_into_qty*100)/100;
						  $prid_into_UToal =  floor($pride_price_into_qty*100)/100;
						
						 $withrbate_AmtEFS = $rebate_amt = 0;
							
							if($cat == 'DEFD'){
								$withrbate_Amt = $amount_chk;
								
							}else{
								$rebate_amt = $QQTY * '0.068';
								$withrbate_Amt = $amount_chk - $rebate_amt;
								$withrbate_AmtEFS = $amount_chk;
							}
							//pre($QQTY.' 0.068');die;
							$single_unit_amt = $withrbate_Amt / $QQTY;
							$Qty_total +=$QQTY;
							$total = $QQTY * $single_unit_amt;
							$amt_total +=$total;
							$EFS_amount +=$amount_chk;
							$totDieselEFS += $withrbate_AmtEFS;
							$prideTotal += $prid_into_UToal;
							
						
							$Rebte_total +=$rebate_amt;
							$defd_Amt; $defd_Qty; 
								if($cat == 'DEFD'){
									$pride_price_into_qty_defd =  $prd_prce * $QQTY;
									@$defd_Amt += $pride_price_into_qty_defd;
									@$defd_Qty += $QQTY;
								}	
						 $more_transc++; 
					}
					
			
					$total_prid_qty = array_sum($prid_into_qty);
					$pride_total_per_invoice = $pride_price_into_qty;
						
			}
	 ?>
			   <tr><td colspan="7"><td/></tr>
			  <tr><td colspan="7"><td/></tr>
			   <tr>
				  <td colspan="5" align="right"></td>
				  <th>Amount</th>
				  <th>Qty</th>
				  <th>Rebate</th>  
			  </tr>
               <tr>
			  <td colspan="5" align="right"><b>Total Fuel Qty and Cost</b></td>
			  <td><?php echo floor($prideTotal*100)/100; ?></td>
			  <td><?php echo floor($Qty_total*100)/100; ?></td>
			  <td><?php echo floor($Rebte_total*100)/100; ?></td>
			  </tr>
			  <tr><td colspan="7"><td/></tr>
			 <tr>
				<td colspan="5" align="right"><b>Total EFS</b></td>
				<td><?php echo floor($EFS_amount*100)/100; ?></td>
				<td></td>
			  </tr>
			 
			  <tr>
				<td colspan="5" align="right"><b>Total Diesel</b></td>
				<td><?php
					echo floor($totDieselEFS *100)/100;
				?></td>
				<td><?php 
					$ttl_qty =  $Qty_total - @$defd_Qty; 
					echo floor($ttl_qty*100)/100;
					?>
				</td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="5" align="right"><b>Total Def</b></td>
				<td><?php echo floor(@$defd_Amt*100)/100; ?></td>
				<td><?php echo floor(@$defd_Qty*100)/100; ?></td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="5" align="right"><b>Total Fuel Qty and Cost</b></td>
				<td><?php echo floor($EFS_amount*100)/100; ?></td>
				<td><?php echo floor($Qty_total*100)/100; ?></td>
				<td></td>
			  </tr>
			  <tr>
				<td colspan="5" align="right"><b>Rebate</b></td>
				<td><?php echo floor($Rebte_total*100)/100; ?></td>
				<td></td>
				<td></td>
			  </tr>
			   <tr>
				  <td colspan="5" align="right"><b>Transcation FEES</b></td>
				  <td >$1.25 x <?php echo $count; ?>  =  <?php echo $count * 1.25; ?></td>
			  </tr>
			  <tr>
				<td colspan="5" align="right"><b>Actual Cost of Fuel</b></td>
				<td><?php 
				       echo floor($amt_total*100)/100; 
					?></td>
				<td></td>
				<td></td>
			  </tr> 
			   <tr>
				<td colspan="5" align="right"><b>Profit</b></td>
				<td><?php 
				      $profittt =  $total_prid_qty - $amt_total;
						echo floor($profittt*100)/100; 
					?></td>
				<td></td>
				<td></td>
			  </tr>
			<?php }else{ echo "<tr>
				<td colspan='7'>No record found</td>
			  </tr>"; }  ?>			  
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