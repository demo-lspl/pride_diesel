<!-- Main Container -->
<div class="">
	<?php $userSessDetails = $this->session->userdata('userdata'); ?>
	<section class="content box">
		<div class="addnew-user">
			<!--a href="<?php //echo base_url('card/edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Add Card</a-->
			<!--a href="<?php //echo base_url('user/exportUser') ?>" class="btn btn-info"><i class="fa fa-excel"></i> Export User</a-->

			
			<!--div class="btn-group" role="group" aria-label="Button group with nested dropdown">

			  <div class="btn-group" role="group">
				<button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				  <i class="fa fa-download"></i> Export
				</button>
				<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
				  <a class="dropdown-item" href="<?php //echo base_url().'card/exportBlankCardsExcel' ?>">Export Blank Excel</a>
				  <a class="dropdown-item" href="<?php //echo base_url().'card/exportCards' ?>">Export Data Excel</a>
				</div>
			  </div>
			</div>			
			<a href="<?php //echo base_url('card/import') ?>" class="btn btn-success"><i class="fa fa-upload"></i> Import</a-->
		<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
			<div class="btn-group" role="group">
			<button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			  <i class="fa fa-download"></i> Export
			</button>			
			<div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
				<!--a class="dropdown-item export-csv" data-cid="" href="#">Export CSV</a-->
				<a class="dropdown-item export-xlsx" data-cid="<?= $userSessDetails->id ?>" href="#">Export Excel</a>
			</div>
			</div>
		</div>			
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
				<h3 class="card-title">Cards (<?php echo $cardCount ?>)</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
				<form class="search form-inline" action="<?php echo base_url().'card/get_my_card'; ?>" method="get" autocomplete="off">
				  <div class="form-group">
					<input class="form-control search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search Card Number"; ?>" type="text" />
				  </div>
				  <button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
				</form>			
                <table id="" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Card #</th>
                    <th>Card Number</th>
                    <th>Card Status</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php if(!empty($allCardData)){ ?>
				  <?php foreach($allCardData as $cardvalues): ?>
                  <tr>
                    <td><?= $cardvalues->id?></td>
                    <td><?= $cardvalues->card_number?></td>
					<?php 
						switch($cardvalues->card_status){
							case 0:
							$status = 'Inactive';
							break;
							case 1:
							$status = 'Active';
							break;
							case 2:
							$status = 'Hold';
							break;
							case 3:
							$status = 'Blocked';
							break;
							case 4:
							$status = 'Clear';
							break;
							case 5:
							$status = 'Fraud';
							break;
							case 6:
							$status = 'Lost';
							break;
							case 7:
							$status = 'Stolen';
							break;
							case 8:
							$status = 'Permanent Blocked';
							break;							
						}
					?>
                    <td><?= $status?></td>					
                    <td align="center"><a href="<?php echo base_url('card/edit/').$cardvalues->id ?>" class="btn btn-default" ><i class=" fa fa-pen"></i></a> <!--a href="<?php echo base_url('card/delete/').$cardvalues->id ?>" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger button-delete" ><i class="fa fa-trash"></i></a--></td>
                  </tr>
				  <?php endforeach; ?>
				  <?php }else{ echo "<tr>
					<td colspan='4'>No card found</td>
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