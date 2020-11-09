<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="addnew-user">
		<a href="<?php echo base_url('gas_station/edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add Gas Station</a></div>
	<div class="">
	<?php if($this->session->flashdata("success_msg")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success_msg")?>
	</div>
	<?php } ?>		
			<?php //print_r($allUserData) ?>
			<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Gas Station</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<form class="search form-inline" action="<?php echo base_url().'gas_station/index'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<input class="form-control search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search Gas Station"; ?>" type="text" />
				  </div>
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
				</form>				
                <table id="" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Gas Station #</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Services</th>
                    <th>Contact Number</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php if(!empty($allGasStation)){ ?>
				  <?php foreach($allGasStation as $gasstation): ?>
				  <?php  
				  $services = '';
				  if(!empty($gasstation->services) && $gasstation->services != null){
					  $servicejdec = json_decode($gasstation->services); 
					  $services = implode(', ', $servicejdec);
					}?>
                  <tr>
                    <td><?= $gasstation->id?></td>
                    <td><?= $gasstation->name?></td>
                    <td><?= $gasstation->address?></td>
                    <td><?= $gasstation->city?></td>
                    <td><?= $gasstation->state?></td>
                    <td><?= ucwords(rtrim($services, ',')) ?></td>
                    <td><?= $gasstation->contact_number?></td>
                    <td align="center"><a href="<?php echo base_url('gas_station/edit/').$gasstation->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('gas_station/delete/').$gasstation->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td>
                  </tr>
				  <?php endforeach; ?>
				  <?php }else{ echo "<tr>
					<td colspan='4'>No gas station found</td>
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
		</div>		
	</section>
</div>