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
				<form class="search form-inline" action="<?php echo base_url().'account/invoice_pdf'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<input class="form-control search-input" name="invoiceid" value="<?php if(!empty($_GET['invoiceid'])){echo $_GET['invoiceid'];} ?>" placeholder="<?php echo "Search Invoice ID"; ?>" type="text" />
				  </div>
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
				</form>		
			<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Id</th> 
					<th>Invoice ID</th>					
					<th>Billing Currency</th>
					<th>Invoice Date</th>								
					<th>Grand Amount</th>								
					<th>Action</th>				
				</tr>
			</thead>
			<tbody>
			  <?php if(!empty($allInvoices)){ ?>
			  <?php foreach($allInvoices as $invoicevalues): ?>
			  <tr>
				<td><?= $invoicevalues->id?></td>
				<td><?= $invoicevalues->invoice_id?></td>				
				<td><?= $invoicevalues->billingCurrency?></td>
				<td><?= $invoicevalues->invoice_date?></td>				
				<td><?= $invoicevalues->grand_total?></td>
				<td align="center"><a href="<?php echo base_url('assets/modules/invoices/trans_invoice_').$invoicevalues->billingCurrency."_".$invoicevalues->invoice_date."_".$invoicevalues->company_id.".pdf" ?>" class="btn btn-warning" ><i class=" fa fa-eye"></i></a> </td>
			  </tr>
			  <?php endforeach; ?>
			  <?php }else{ echo "<tr>
				<td colspan='6'>No invoice found</td>
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