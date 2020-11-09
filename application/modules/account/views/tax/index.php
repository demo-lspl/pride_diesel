<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="addnew-user"><a href="<?php echo base_url('account/edit_tax') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Tax</a></div>
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Tax</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<form class="search form-inline" action="<?php echo base_url().'account/tax'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<input class="form-control search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search Province"; ?>" type="text" />
				  </div>
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
				</form>			
	<?php if($this->session->flashdata("success")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success")?>
	</div>
	<?php } ?>
	
			<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Id</th> 
					<th>Tax Rate</th>
					<th>Province</th>
					<th>Tax Type</th>					
					<th>Action</th>				
				</tr>
			</thead>
			<tbody>
			  <?php if(!empty($results)){ ?>
			  <?php foreach($results as $result): ?>
			  <tr>
				<td><?= $result->id?></td>
				<td><?= $result->tax_rate?></td>	
				<td><?= ucfirst($result->state)?></td>
				<td><?= strtoupper($result->tax_type)?></td>

				<td align="center"><a href="<?php echo base_url('account/edit_tax/').$result->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('account/delete_tax_record/').$result->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td>
			  </tr>
			  <?php endforeach; ?>
			  <?php }else{ echo "<tr>
				<td colspan='4'>No Records Found</td>
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