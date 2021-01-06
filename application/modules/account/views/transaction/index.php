<!-- Main Container -->
<div class="">
	<?php $userSessDetails = $this->session->userdata('userdata'); $cid = $userSessDetails->id;?>
	<section class="content box">
	<div class="addnew-user">
		<a href="<?php echo base_url('soap_client/get_transaction_summ') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Import Transaction EFS</a>
		<a href="<?php echo base_url('account/import_transactions_husky') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Import Transaction Husky</a>
		<select name="currency" class="form-control select2 currency" style="width: 150px">
			<option value="undefined">-- Currency --</option>
			<option value="USD">USD</option>
			<option value="CAD">CAD</option>
		</select>		
		<select name="company_name[]" class="form-control select2 companyname" multiple style="width: 300px" data-placeholder="-- Export by Company --">
			<!--option value="undefined">-- Export by Company --</option-->
			<?php foreach($getuserdata as $usernames): ?>
				<option <?php if(!empty($_GET['company_name'])){if(str_replace('+', ' ', $_GET['company_name'] == $usernames->company_name)){echo "selected";}} ?> value="<?php echo $usernames->id; ?>"><?php echo ucwords($usernames->company_name); ?></option>
			<?php endforeach; ?>
		</select>		
		<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
			<div class="btn-group" role="group">
			<button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  <i class="fa fa-download"></i> Export
			</button>			
			<div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
				<!--a class="dropdown-item export-csv" data-cid="" href="#">Export CSV</a-->
				<a class="dropdown-item export-xlsx" data-cid="undefined" href="#">Export Excel</a>
				<a class="dropdown-item export-othersoft-xlsx" data-cid="undefined" href="#">Export Excel for Xpert</a>
			</div>
			</div>
		</div>
		<div class="display-none export-msg"><img src="<?php echo base_url('assets/images/please-wait.gif') ?>" width="210" /></div>		
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
				<form class="search form-inline" action="<?php echo base_url().'account/transactions'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<input class="form-control search-input" name="card_search" value="<?php if(!empty($_GET['card_search'])){echo $_GET['card_search'];} ?>" placeholder="<?php echo "Search by card"; ?>" type="text" />
					<input type="text" name="date_range" class="daterange form-control"  data-placeholder="-- Date Range --"/>&nbsp;&nbsp;
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
						autoUpdateInput: false,
						locale: {
							format: 'YYYY-MM-DD',
							cancelLabel: 'Clear'

						},
							"startDate": '<?php echo $startDate; ?>',
							"endDate": '<?php echo $endDate; ?>'		
					});
/* $('.daterange').daterangepicker({
    //minimumResultsForSearch: -1,
    placeholder: function(){
        $(this).data('placeholder');
    }
}); */						
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
					<th>Company Name</th>
					<th>Card Status</th>
					<th>Transaction Date</th>				
				</tr>
			</thead>
			<tbody>
			  <?php if(!empty($transactionData)){ ?>
			  <?php foreach($transactionData as $transaction): ?>
			  <tr>
				<td><?= $transaction->id?></td>
				<td><a href="<?php echo base_url().'account/card_transactions/'.$transaction->id?>" ><?php echo $transaction->card_number?></a></td>
				<?php $getCompanyName = $this->db->select('company_name')->where('id', $transaction->company_id)->get('users')->row();?>
				<td><?= !empty($getCompanyName->company_name)?$getCompanyName->company_name:''; ?></td>
				<td><?php if($transaction->card_status==0){echo 'Inactive';}else if($transaction->card_status==1){echo 'Active';}else{echo 'Hold';}?></td>

				<td><?= $transaction->transaction_date?></td>

				<!--td align="center"><a href="<?php echo base_url('account/transaction_edit/').$transaction->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('account/transaction_delete/').$transaction->id ?>" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td-->
			  </tr>
			  <?php endforeach; ?>
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