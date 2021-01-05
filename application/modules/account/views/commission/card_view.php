<div class="">
	<section class="content box">
		<div class="addnew-user">
			<!--a href="<?php //echo base_url('account/invoice_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Invoice</a-->
		</div>
	<div class="card card-default">
		<div class="card-header bg-card-header">
			<h3 class="card-title">Company Card Details</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
					<button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
		</div>
		<div class="card-body">
			<form class="search form-inline" action="<?php echo base_url().'account/get_com_cards'; ?>" method="get" autocomplete="off">
				<div class="form-group">
					<input class="form-control search-input card-search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search Card Number"; ?>" type="text" />
				</div>
				  &nbsp;
				<button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
			</form>
			<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Id</th> 
					<th>Card Number</th>
					<th>Company Name</th>					
					<th>Card Token</th>				
					<th>Card Status</th>
                    <th>Qty Total</th>
                    <th>Grand Total</th>
					<th>Rebate</th>		
                    <th>Cost</th>					
                    <th>Profit</th>					
                    <th>Commission</th>					
                  	<th>View Transaction</th>					
				</tr>
			</thead>
			<tbody>
			 <?php 
			 if(!empty($get_comp_cards)){
				 $grand_total_amt = 0;
				 $grand_commiion_amt = 0;
				 foreach($get_comp_cards as $dtld){
					 if($dtld->card_status == 0){
						 $crdsttus = 'Inactive';
						 }elseif($dtld->card_status == 1){
							 $crdsttus = 'Active';
						 }elseif($dtld->card_status == 2){
							 $crdsttus = 'Hold';
						 }elseif($dtld->card_status == 3){
							 $crdsttus = 'Blocked';
						 }elseif($dtld->card_status == 4){
							 $crdsttus = 'Clear';
						 }elseif($dtld->card_status == 5){
							 $crdsttus = 'Fraud';
						 }elseif($dtld->card_status == 6){
							 $crdsttus = 'Lost';
						 }elseif($dtld->card_status == 7){
							 $crdsttus = 'Stolen';
						 }elseif($dtld->card_status == 8){
							 $crdsttus = 'Permanent Blocked	';
						 }
					$where2 = 0;
					 $comID =  getNameById('users',$dtld->company_id,'id');
					$trans_dtails = $this->account_model->getTRANS_details_count($dtld->card_number,$where2);
					
					
					
					
					$total_amount = $total_qty =  $sale_total = 0;
					foreach($trans_dtails as $trans_AMT_details){
							$amount = json_decode($trans_AMT_details->amount);
							$cat = json_decode($trans_AMT_details->category);
							$QTY = json_decode($trans_AMT_details->quantity);
							$pride_price = json_decode($trans_AMT_details->pride_price);
							$more_transc = 0;
							
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
						//$sale_total +=$grnd_total
					}		
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
					 echo '<td>'.$dtld->id.'</td>';
					 echo '<td>'.$dtld->card_number.'</td>';
					 echo '<td>'.$comID->company_name.'</td>';
					 echo '<td>'.$dtld->cardCompany.'</td>';
					 echo '<td>'.$crdsttus.'</td>';
					 echo '<td>'.$total_qty.'</td>';
					 echo '<td>'.$sale_total.'</td>';
					 echo '<td>'.$Qty_rebate.'</td>';
					 echo '<td>'.$cost.'</td>';
					 echo '<td>'.$profit.'</td>';
					 echo '<td>'.$commission.'</td>';
					 echo '<td><a href="'.base_url("account/view_crd_trns_dtls/").$dtld->card_number.'" class="btn btn-default" ><i class="fa fa-eye" aria-hidden="true"></i></a> </td>';
				
			}
			// echo '</tr>';
				// echo '<tr>';
				// echo '<td colspan="5" align="right"><b>Total</b></td>';
				// echo '<td><b>'. $grand_total_amt .'</b></td>';
				// echo '<td><b>'. $grand_commiion_amt .'</b></td>';
				// echo '<td></td>';
				
				// echo '</tr>';
		}else{ 
				echo "<tr>
					<td colspan='7'>No record found</td>
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