<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="addnew-user">
			<a href="<?php echo base_url('user/edit_pricelist') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add Price list</a>
			<!--a href="<?php echo base_url('user/create_users_pdf/') ?>" class="btn btn-success"><i class="fa fa-file"></i> Export PDF</a-->
			<!--a href="<?php echo base_url('user/exportUser') ?>" class="btn btn-info"><i class="fa fa-excel"></i> Eport User</a>
			<form method="post" action="<?php echo base_url('user/importUser')?>" enctype="multipart/form-data">
				<input type="file" name="uploadFile" />
				<input type="submit" value="Import" />
			</form-->
		</div>
	<div class="">
	<?php if($this->session->flashdata("success")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success")?>
	</div>
	<?php } ?>
			<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Price List</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
			<?php //print_r($allUserData) ?>
                    <!--form class="search-admin form-inline" action="<?php echo site_url($this->uri->uri_string()); ?>" method="GET" autocomplete="off">

                      <div class="form-group">
                        <input class="form-control" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search"; ?>" type="text" />
                      </div>
                      <button type="submit" class="btn btn-default"><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>

                    </form-->		
                <table id="example1" class="table table-bordered table-striped table-hover">
                  <thead>
                  <tr>
                    <th>List #</th>
                    <th>Product Name</th>
                    <th>Company Type</th>
                    <th>Price</th>

                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php foreach($pricelist as $row): ?>
                  <tr>
                    <td><?= $row->id?></td>
					<?php $productName = $this->db->where('id', $row->product_id)->get('products')->row();?>
                    <td><?= $productName->product_name?></td>
					<?php $jsonDecoded = json_decode($row->price_descr);?>
					<?php //print_r($jsonDecoded) ?>
					<?php
					$ctype = '';
					for($i=0; $i<count($jsonDecoded); $i++){	
						$companyType = $this->db->select('company_type')->where('id', $jsonDecoded[$i]->company_id)->get('company_types')->row();
					$ctype .= $companyType->company_type.", ";
					}
					?>
                    <td><?= rtrim($ctype, ', ') ?></td>
					<?php
					$price = '';
					for($i=0; $i<count($jsonDecoded); $i++){	
						/* $companyType = $this->db->select('company_type')->where('id', $jsonDecoded[$i]->company_id)->get('company_types')->row(); */
					$price .= $jsonDecoded[$i]->price.", ";
					}
					?>					
                    <td><?= rtrim($price, ', ')?></td>

                    <td align="center"><a href="<?php echo base_url('user/edit_pricelist/').$row->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('user/company_delete/').$row->id ?>" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td>
                  </tr>
				  <?php endforeach; ?>	
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>List #</th>
                    <th>Product Name</th>
                    <th>Company Type</th>
                    <th>Price</th>

                    <th>Action</th>
                  </tr>
                  </tfoot>
                </table>
				<div class="row">
					<div class="col-md-5 col-sm-12">
					<!--div class="tables_info">
						Showing <?= $pagination_offset+1 ?> to <?php echo $pagination_offset + $perpage ?> of <?php echo count($allUserData) ?> entries
					</div-->
					</div>
					<div class="col-md-7 col-sm-12">
				<div class="pagination-container">
				<?php //echo $pagination ?>
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
                    <td align="center"><a href="<?php echo base_url('user/edit/').$uservalues->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('user/delete/').$uservalues->id ?>" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td>
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