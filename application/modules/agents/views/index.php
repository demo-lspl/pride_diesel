<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="addnew-user">
			<a href="<?php echo base_url('agents/edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add Agent</a>
		</div>
	<div class="">
	<?php if($this->session->flashdata("success")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success")?>
	</div>
	<?php } ?>
			<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Agents</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<form class="search form-inline" action="<?php echo base_url().'agents/index'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<!--input class="form-control search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search Company Name"; ?>" type="text" /-->
						<select name="company_name" class="form-control select2" style="width: 250px">
							<option value="">-- Search by User --</option>
							<?php foreach($getuserdata as $usernames): ?>
								<option <?php if(!empty($_GET['company_name'])){if(str_replace('+', ' ', $_GET['company_name'] == $usernames->company_name)){echo "selected";}} ?> value="<?php echo $usernames->company_name; ?>"><?php echo ucwords($usernames->company_name); ?></option>
							<?php endforeach; ?>
						</select>&nbsp;&nbsp;					
				  </div>
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
				</form>		
                <table class="table table-bordered table-striped table-hover">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php if(count($allUserData)>0){foreach($allUserData as $uservalues): ?>
                  <tr>
                    <td><?= $uservalues->id?></td>
                    <td><?= $uservalues->company_name?></td>
                    <td><?= $uservalues->address?></td>
                    <td><?= $uservalues->company_email?></td>
                    <td align="center"><a href="<?php echo base_url('agents/edit/').$uservalues->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('agents/delete/').$uservalues->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td>
                  </tr>
				  <?php endforeach;}else{ ?>
				  <tr><td colspan="5">No record found</td></tr>
				  <?php } ?>	
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
                <!--table id="example1" class="table table-bordered table-striped table-hover">
                  <thead>
                  <tr>
                    <th>Company #</th>
                    <th>Company Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php foreach($allUserData as $uservalues): ?>
                  <tr>
                    <td><?= $uservalues->id?></td>
                    <td><?= $uservalues->company_name?></td>
                    <td><?= $uservalues->address?></td>
                    <td><?= $uservalues->company_email?></td>
                    <td align="center"><a href="<?php echo base_url('user/edit/').$uservalues->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('user/delete/').$uservalues->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td>
                  </tr>
				  <?php endforeach; ?>	
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>Company #</th>
                    <th>Company Name</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Action</th>
                  </tr>
                  </tfoot>
                </table-->
			</div>
			</div>
		</div>		
	</section>
</div> 