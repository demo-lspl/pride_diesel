<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="addnew-user">
		<a href="<?php echo base_url('driver/edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add Driver</a></div>
	<div class="">
	<?php if($this->session->flashdata("success_msg")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success_msg")?>
	</div>
	<?php } ?>		
			<?php $userSessDetails = $this->session->userdata('userdata'); ?>
			<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Drivers</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
                    <form class="search form-inline" action="<?php echo base_url().'driver/index'; ?>" method="get" autocomplete="off">
					<?php if($userSessDetails->role == 'admin'){ ?>
                      <div class="form-group">
						<select name="company_name" class="form-control selectledgergroup" style="width: 250px">
							<option value="">-- Search by Company --</option>
							<?php foreach($getuserdata as $usernames): ?>
								<option <?php if(!empty($_GET['company_name'])){if(str_replace('+', ' ', $_GET['company_name'] == $usernames->id)){echo "selected";}} ?> value="<?php echo $usernames->id; ?>"><?php echo ucwords($usernames->company_name); ?></option>
							<?php endforeach; ?>
						</select>						
                      </div>
					<?php }else{ ?>
                      <div class="form-group">
						<select name="company_name" class="form-control selectledgergroup" style="width: 250px">
							<option value="">-- Search by Driver --</option>
							<?php foreach($getuserdata as $usernames): ?>
								<option <?php if(!empty($_GET['company_name'])){if(str_replace('+', ' ', $_GET['company_name'] == $usernames->id)){echo "selected";}} ?> value="<?php echo $usernames->id; ?>"><?php echo ucwords($usernames->name); ?></option>
							<?php endforeach; ?>
						</select>						
					</div><?php } ?>&nbsp;					  
                      <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>

                    </form>				
                <table id="" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Driver #</th>
					<?php if($userSessDetails->role == 'admin'){?>
						<th>Company Name</th>
					<?php } ?>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Postal/ZIP</th>					
                    <th>State</th>					
                    <th>Country</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php if(!empty($allDriver)){ ?>
				  <?php foreach($allDriver as $driver): ?>
                  <tr>
                    <td><?= $driver->id?></td>
					<?php if($userSessDetails->role == 'admin'){?>
						<td><?= $driver->company_name?></td>
					<?php } ?>
                    <td><?= $driver->name?></td>
                    <td><?= $driver->address?></td>
                    <td><?= $driver->postal_code?></td>
                    <td><?= $driver->state?></td>
                    <td><?= $driver->country ?></td>
                    <td><?= $driver->email?></td>
                    <td><?= $driver->phone?></td>
                    <td align="center"><a href="<?php echo base_url('driver/edit/').$driver->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('driver/delete/').$driver->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td>
                  </tr>
				  <?php endforeach; ?>
				  <?php }else{ echo "<tr>
					<td colspan='10'>No record found</td>
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