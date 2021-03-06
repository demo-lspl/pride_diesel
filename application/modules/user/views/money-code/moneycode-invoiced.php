<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="addnew-user">
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
				<h3 class="card-title">Invoiced</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
                    <form class="search form-inline" action="<?php echo base_url().'user/moneyCodeInvoices'; ?>" method="get" autocomplete="off">

                      <div class="form-group">
						<select name="company_name" class="form-control select2" style="width: 250px">
							<option value="">-- Search by Company --</option>
							<?php foreach($getuserdata as $usernames): ?>
								<option <?php if(!empty($_GET['company_name'])){if(str_replace('+', ' ', $_GET['company_name'] == $usernames->company_name)){echo "selected";}} ?> value="<?php echo $usernames->company_name; ?>"><?php echo ucwords($usernames->company_name); ?></option>
							<?php endforeach; ?>
						</select>&nbsp;&nbsp;
                      </div>
                      <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>

                    </form>			
                <table id="" class="table table-bordered table-striped table-hover">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Company Name</th>
                    <th>Company Email</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php if(count($invoicePending)>0){foreach($invoicePending as $invoicePendingRows): 
					$dateCreated = strtotime($invoicePendingRows->date_created);
				  ?>
                  <tr>
                    <td><?= $invoicePendingRows->id?></td>
                    <td><?= $invoicePendingRows->company_name?></td>
                    <td><?= $invoicePendingRows->company_email?></td>
                    <td align="center">
					<a href="<?php /* echo base_url('user/viewMoneycodeInvoice/').$invoicePendingRows->id; */ echo base_url('assets/modules/invoices/money_code_trans_').$invoicePendingRows->invoiceId."_" .date('Y-m-d',$dateCreated)."_".$invoicePendingRows->companyId.".pdf"?>" class="btn btn-primary" ><i class=" fa fa-eye"></i></a>
					</td>
                  </tr>
				  <?php endforeach; }else{ ?>
				<tr>
					<td colspan="4">No record found</td>
				</tr>	
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
			</div>
			</div>
		</div>		
	</section>
</div> 