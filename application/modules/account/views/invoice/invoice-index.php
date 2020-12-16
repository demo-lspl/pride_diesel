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
				<form class="search form-inline" action="<?php echo base_url().'account/invoice'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<input class="form-control search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search Invoice ID"; ?>" type="text" />
					
				  </div>
				  <div class="form-group">
					<?php $options = array('efs' => 'EFS', 'husky' => 'HUSKY'); ?>
					<select class="form-control" name="transactions_from">
						<option value="">--Filter Invoices--</option>
						<?php
							if(!empty($_GET['transactions_from'])){
								$searchCompany = $_GET['transactions_from'];
							}
							foreach($options as $key => $optionsItems){
								($key == $searchCompany)? $sel = "selected": $sel = '';
								echo "<option value='{$key}' {$sel}>{$optionsItems}</option>";
							}
						?>
					</select>
				  </div>&nbsp;&nbsp;				  
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
				</form>		
			<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Id</th> 
					<th>Company Name</th>
					<th>Company Address</th>					
					<th>Invoice ID</th>
					<th>Currency</th>
					<th>BillingAt</th>
					<th>Grand Amount</th>
					<th>Created On</th>				
					<th>Action</th>				
				</tr>
			</thead>
			<tbody>
			  <?php if(!empty($allInvoices)){ ?>
			  <?php foreach($allInvoices as $invoicevalues): ?>
			  <tr>
				<td><?= $invoicevalues->id?></td>
				<td><?= $invoicevalues->company_name?></td>
				<td><?= $invoicevalues->address?></td>
				<td><?= $invoicevalues->invoice_id?></td>
				<td><?= $invoicevalues->billingCurrency?></td>
				<td><?= $invoicevalues->billingOn?></td>
				<td><?= $invoicevalues->grand_total?></td>	
				<td><?= $invoicevalues->date_created?></td>				
				<?php $husky = '';if($invoicevalues->billingOn == 'HUSKY'){$husky = '_husky';}?>
				<td align="center"><?php if($invoicevalues->status == 0){ ?><a class="btn btn-primary" href="<?php echo base_url('account/update_invoice_status/'.$invoicevalues->id."/".$this->uri->segment(3)) ?>">UnPaid</a><?php }else{ echo "<a class='btn btn-success text-white'>Paid <i class='fa fa-check'></i></a>"; }?> <a href="<?php echo base_url('assets/modules/invoices/trans_invoice_'.$invoicevalues->billingCurrency.$husky."_".$invoicevalues->invoice_date."_".$invoicevalues->company_id.".pdf") ?>" class="btn btn-warning" ><i class=" fa fa-eye"></i></a><!--a href="<?php //echo base_url('account/view_invoiced_trans/').$invoicevalues->id ?>" class="btn btn-warning" ><i class=" fa fa-eye"></i></a--> <!--a href="<?php //echo base_url('account/invoice_edit/').$invoicevalues->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a--> <!--a href="<?php //echo base_url('account/delete/').$invoicevalues->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a--></td>
			  </tr>
			  <?php endforeach; ?>
			  <?php }else{ echo "<tr>
				<td colspan='9'>No record found</td>
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