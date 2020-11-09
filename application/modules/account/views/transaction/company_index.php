<!-- Main Container -->
<div class="">
	<?php $userSessDetails = $this->session->userdata('userdata'); $cid = $userSessDetails->id;?>
	<section class="content box">
	<div class="addnew-user">
	<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
		<div class="btn-group" role="group">
		<button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		  <i class="fa fa-download"></i> Export
		</button>
		<div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
		  <a class="dropdown-item export-csv" data-cid="<?php echo $cid ?>" href="#">Export CSV</a>
		  <a class="dropdown-item export-xlsx" data-cid="<?php echo $cid ?>" href="#">Export Excel</a>
		  <!--a class="dropdown-item export-fleetmanager" href="http://localhost/pride_diesel/card/exportCards">Export for Fleet Manager</a>
		  <a class="dropdown-item" href="http://localhost/pride_diesel/card/exportCards">Export Xlsx</a>
		  <a class="dropdown-item" href="http://localhost/pride_diesel/card/exportCards">Export CSV</a-->
		</div>
		</div>
	</div>	
	<!--a href="<?php echo base_url('account/exportTransactionByCompany/').$cid ?>" class="btn btn-success"><i class="fa fa-download"></i> Export Xlsx</a>
	<a href="<?php echo base_url('account/export_company_trans_csv/').$cid ?>" class="btn btn-success"><i class="fa fa-download"></i> Export CSV</a-->
	</div>
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Transactions</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<form class="search form-inline" action="<?php echo base_url().'account/company_transactions'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<!--input class="form-control search-input" name="search" value="<?php //if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php //echo "Search card number"; ?>" type="text" /-->
					<input type="text" name="date_range" class="daterange form-control"  />&nbsp;&nbsp;
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
					$('.daterange').daterangepicker({
						locale: {
							format: 'YYYY-MM-DD',

						},
							"startDate": '<?php echo $startDate; ?>',
							"endDate": '<?php echo $endDate; ?>'		
					});
				</script>				
			<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>#</th> 
					<th>Card Number</th>
					<th>Driver</th>
					<th>Card Status</th>
					<th>Date Created</th>			
					<!--th>Action</th-->				
				</tr>
			</thead>
			<tbody>
		
			  <?php $i=1;if(!empty($transactionData)){ ?>
			  <?php if(!empty($this->uri->segment(3))){$seg3Val = $this->uri->segment(3) - 1;$ival = $seg3Val.$i;}else{$ival = $i;}?>
			  <?php foreach($transactionData as $transaction): ?>
			  <tr>
				<td><?= $ival ?></td>
				<td><a href="<?php echo base_url().'account/comp_card_transactions/'.$transaction->card_number.'/'.$daterange?>" ><?php echo $transaction->card_number?></a></td>
				<td><?= $transaction->name?></td>
				<td><?php if($transaction->card_status==0){echo 'Inactive';}else if($transaction->card_status==1){echo 'Active';}else{echo 'Hold';}?></td>
				<td><?= $transaction->transaction_date?></td>
				<!--td align="center"><a href="<?php echo base_url('account/transaction_edit/').$transaction->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('account/transaction_delete/').$transaction->id ?>" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td-->
			  </tr>
			  <?php $i++; $ival++; endforeach; ?>
			  <?php }else{ echo "<tr>
				<td colspan='5'>No Records Found</td>
			  </tr>"; }  ?>
			  </tbody>
			</table>
				<div class="row">
					<div class="col-md-5 col-sm-12">
					<!--div class="tables_info">
						Showing <?= $pagination_offset+1 ?> to <?php echo $pagination_offset + $perpage ?> of <?php echo count($allUserData) ?> entries
					</div-->
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