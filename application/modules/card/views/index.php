<?php $userSessDetails = $this->session->userdata('userdata'); if($userSessDetails->role != 'admin'){redirect(base_url('dashboard'), 'refresh');} ?>
<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="addnew-user">
			<!--a href="<?php //echo base_url('card/edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add Card</a-->
			<!--a href="<?php //echo base_url('user/exportUser') ?>" class="btn btn-info"><i class="fa fa-excel"></i> Export User</a-->

			
			<div class="btn-group" role="group" aria-label="Button group with nested dropdown">

			  <div class="btn-group" role="group">
				<button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  <i class="fa fa-download"></i> Export
				</button>
				<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
				  <a class="dropdown-item" href="<?php echo base_url().'card/exportBlankCardsExcel' ?>">Export Blank Excel</a>
				  <a class="dropdown-item" href="<?php echo base_url().'card/exportCards' ?>">Export Data Excel</a>
				</div>
			  </div>
			</div>			
			<a href="<?php echo base_url('Soap_client/import_cards') ?>" class="btn btn-success"><i class="fa fa-upload"></i> Import Card EFS</a>
			<a href="<?php echo base_url('Soap_client_husky/index') ?>" class="btn btn-success"><i class="fa fa-upload"></i> Import Card Husky</a>	
		</div>
	<div class="">
	<?php if($this->session->flashdata("success_msg")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success_msg")?>
	</div>
	<?php } ?>		
			<?php //print_r($allUserData) ?>
			<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Cards (<?php echo $searchCount; ?>)</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<form class="search form-inline" action="<?php echo base_url().'card/index'; ?>" method="get" autocomplete="off">

				  <div class="form-group">
					<input class="form-control search-input card-search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search Card Number"; ?>" type="text" />
						<select name="company_name" class="form-control selectledgergroup" style="width: 250px">
							<option value="">-- Search by Company --</option>
							<?php foreach($getuserdata as $usernames): ?>
								<option <?php if(!empty($_GET['company_name'])){if(str_replace('+', ' ', $_GET['company_name'] == $usernames->company_name)){echo "selected";}} ?> value="<?php echo $usernames->company_name; ?>"><?php echo ucwords($usernames->company_name); ?></option>
							<?php endforeach; ?>
						</select>				
				  </div>
				  &nbsp;
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>

				</form>				
                <table id="" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Card #</th>
                    <th>Card Number</th>
                    <th>Company Name</th>
                    <th>Card Limit</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php if(!empty($allCardData)){ //pre($allCardData)?>
				  <?php foreach($allCardData as $cardvalues): ?>
                  <tr>
                    <td><?= $cardvalues->id?></td>
                    <td><?= $cardvalues->card_number?></td>
                    <td><?= $cardvalues->company_name?></td>
                    <td><?= $cardvalues->card_limit?></td>
                    <td align="center"><a href="<?php echo base_url('card/edit/').$cardvalues->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <a href="<?php echo base_url('card/delete/').$cardvalues->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a></td>
                  </tr>
				  <?php endforeach; ?>
				  <?php }else{ echo "<tr>
					<td colspan='5'>No detail found</td>
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