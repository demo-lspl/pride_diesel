<div class="">
		<section class="content box">
			<div class="addnew-user">
				<!--a href="<?php //echo base_url('account/invoice_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Invoice</a-->
			</div>
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Sales Person Details</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						<button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
					</div>
			</div>
		<div class="card-body">
			<!--form class="search form-inline" action="<?php echo base_url().'account/sale_comission'; ?>" method="get" autocomplete="off">
				<div class="form-group">
					<input class="form-control search-input card-search-input" name="search" value="<?php if(!empty($_GET['search'])){echo $_GET['search'];} ?>" placeholder="<?php echo "Search Card Number"; ?>" type="text" />
				</div>
				 &nbsp;
				<button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php echo 'Search'; ?></button>
			</form-->
			<table id="" class="table table-bordered table-hover">
				<thead>
					<tr>
						<th>Id</th> 
						<th>Name</th>					
						<th>Email</th>				
						<th>Address</th>
						<th>City</th>
						<th>View Details</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if(!empty($get_sales_commision)){
					foreach($get_sales_commision as $usr_dtls){
						echo '<tr>';
						echo '<td>'.$usr_dtls->id.'</td>';
						echo '<td>'.$usr_dtls->company_name.'</td>';
						echo '<td>'.$usr_dtls->company_email.'</td>';
						echo '<td>'.$usr_dtls->address.'</td>';
						echo '<td>'.$usr_dtls->city.'</td>';
						echo '<td><a href="'.base_url("account/view_users_dtls/").$usr_dtls->id.'" class="btn btn-default" ><i class="fa fa-eye" aria-hidden="true"></i></a> </td>';
						echo '</tr>';
						
					}
				}
					
				?>	
			</tbody>
		</table>
			<div class="row">
				<div class="col-md-5 col-sm-12"></div>
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