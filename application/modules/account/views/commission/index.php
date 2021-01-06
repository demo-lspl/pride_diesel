<div class="">
	<section class="content box">
		<div class="addnew-user"><!--a href="<?php //echo base_url('account/invoice_edit') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Create Invoice</a--></div>
	<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Company</h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						<button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
					</div>
			</div>
			<div class="card-body">
				<!--form class="search form-inline" action="<?php //echo base_url().'account/cad_rebate_calc'; ?>" method="get" autocomplete="off">
					<div class="form-group">
						<input type="text" name="date_range" class="daterange form-control"  />&nbsp;&nbsp;
					</div>
					<button type="submit" class="btn btn-default" ><i class="fa fa-search"></i>&nbsp;&nbsp;<?php //echo 'Search'; ?></button>
					<a href="<?php //echo base_url(); ?>account/cad_rebate_calc">
						<input type="button" name="submitSearchReset" class="btn btn-primary" value="Reset"></a>
				</form-->
			<?php 
				$startDate = date('Y-m-d');
				$endDate = date('Y-m-d');
				$daterange = null;
					if(!empty($_GET['date_range'])){
						$daterange = $_GET['date_range'];
						$expDateRange = explode(' - ', $_GET['date_range']);
						$startDate = $expDateRange[0];
						$endDate = $expDateRange[1];
					}
				?>
				<script type="text/javascript">
					$('.daterange').daterangepicker({
						autoUpdateInput: false,
						locale: {
							format: 'YYYY-MM-DD',
							cancelLabel: 'Clear'
						},
							"startDate": '<?php echo $startDate; ?>',
							"endDate": '<?php echo $endDate; ?>'		
					});
						$('.daterange').on('apply.daterangepicker', function (ev, picker) {
							$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
						});
					$('.daterange').on('cancel.daterangepicker', function (ev, picker) {
						$(this).val('');
					});
			</script>				
			<table id="" class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Id</th> 
					<th>Company Name</th>
					<th>Company Address</th>					
					<th>State</th>
					<th>Company Email</th>
					<th>Company Location</th>
					<th>Action</th>				
				</tr>
			</thead>
			<tbody>
			  <?php if(!empty($get_comp)){ ?>
				<?php foreach($get_comp as $comp_dtl): 
			     $new_Created_Date = date("d-M-Y", strtotime($comp_dtl->date_created));
				
				 	
			  ?>
			<tr>
				<td><?= $comp_dtl->id;?></td>
				<td><?= $comp_dtl->company_name;?></td>
				<td><?= $comp_dtl->address;?></td>
				<td><?= $comp_dtl->province;?></td>
				<td><?= $comp_dtl->company_email;?></td>
				<td style="text-transform: uppercase"><?= $comp_dtl->company_location;?></td>
					
				<td><a href="<?php echo base_url('account/view_com_dtls/').$comp_dtl->id; ?>" class="btn btn-default" ><i class="fa fa-eye" aria-hidden="true"></i></a> </td>				
			</tr>
		<?php endforeach; ?>
		</tbody>
			
			<?php }else{ echo "<tr>
				<td colspan='7'>No record found</td>
			  </tr>"; }  ?>			  
			</table>
				<div class="row">
					<div class="col-md-5 col-sm-12">
					
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