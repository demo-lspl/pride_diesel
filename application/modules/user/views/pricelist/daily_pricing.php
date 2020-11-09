<!-- Main Container -->
<div class="">

<section class="content box">
	<div class="card card-default">
		<div class="card-header bg-card-header">
			<h3 class="card-title">Daily Prices by TA</h3>

			<div class="card-tools">
			  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
			  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
			</div>
		</div>
		<div class="card-body">
			<div class="row justify-content-center">
				<div class="col-sm-12">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
					</div>
					<?php 
					
					?>
						<table class="table table-bordered">
							<tr>
								<th>Gas Station</th>
								<th>City</th>
								<th>State</th>
								<th>Product</th>
								<th>Retail Price</th>
								<th>Pride Diesel Price</th>
								<th>Date</th>
							</tr>
							<?php if(!empty($dailyPriceList)){ foreach($dailyPriceList as $dailyPriceListRows):?>
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<td><?= $dailyPriceListRows->retail_price ?></td>
								<td><?= $dailyPriceListRows->pride_price ?></td>
								<td><?= $dailyPriceListRows->date ?></td>
							</tr>
							<?php endforeach;} ?>
						</table>
				  </div>
			  </div>
		</div>
	</div>		
</section>
</div>