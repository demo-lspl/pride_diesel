<!-- Main Container -->
<div class="">

	<section class="content box">
	<!--div class="addnew-user"><a href="<?php //echo base_url('account/ledger_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Ledger</a></div-->
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Ledger</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<form class="search form-inline" action="<?php echo base_url().'account/ledgers'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<!--input class="form-control search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search company name"; ?>" type="text" /-->
						<select name="company_name" class="form-control select2" style="width: 250px">
							<option value="">-- Search by Company --</option>
							<?php foreach($getuserdata as $usernames): ?>
								<option <?php if(!empty($_GET['company_name'])){if(str_replace('+', ' ', $_GET['company_name'] == $usernames->company_name)){echo "selected";}} ?> value="<?php echo $usernames->company_name; ?>"><?php echo ucwords($usernames->company_name); ?></option>
							<?php endforeach; ?>
						</select>&nbsp;&nbsp;					
				  </div>
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
				</form>			
			<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Id</th> 
					<th>Company Name</th>
					<th>Province</th>
					<th>Email</th>
					<th>Created On</th>
					<th>Action</th>				
				</tr>
			</thead>
			<tbody>
			  <?php if(!empty($allLedger)){ ?>
			  <?php foreach($allLedger as $ledgervalues): ?>
			  <tr>
				<td><?= $ledgervalues->id?></td>
				<td><?= $ledgervalues->company_name?></td>
				<td><?= $ledgervalues->province?></td>
				<td><?= $ledgervalues->company_email?></td>
				<td><?= $ledgervalues->date_created?></td>
				<td align="center">
				<a href="<?php echo base_url('account/invoiced/').$ledgervalues->id ?>" class="btn btn-primary" title="View Old Invoices">Invoiced <i class=" fa fa-eye"></i></a>
				<!--a href="<?php //echo base_url('account/invoice_view/').$ledgervalues->id ?>" class="btn btn-info" title="View Old Invoices">Invoiced <i class=" fa fa-eye"></i></a--> <a href="<?php echo base_url('account/transaction_view_by_cid/').$ledgervalues->id ?>" class="btn btn-warning" title="Transaction">Non-Invoiced <i class=" fa fa-eye"></i></a> <!--a href="<?php echo base_url('account/ledger_edit/').$ledgervalues->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('account/delete_ledger/').$ledgervalues->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a--></td>
			  </tr>
			  <?php endforeach; ?>
			  <?php }else{ echo "<tr>
				<td colspan='4'>No ledger found</td>
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