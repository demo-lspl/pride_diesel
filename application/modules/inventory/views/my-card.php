<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Card</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">
					
			<div class="row">
				<?php if(!empty($cardDetails)){foreach($cardDetails as $carddatas){ ?>
				<div class="col-lg-3 col-6">
					<!-- small card -->
					<div class="small-box bg-info">
					  <div class="inner">
						  <p>Card Number</p>							
						  <h3><?= $carddatas->card_number?></h3>
						  </div>
						  <div class="icon">
							<i class="fas fa-credit-card"></i>
						  </div>
						  <a href="#" class="small-box-footer">
							Card Limit: <?= $carddatas->card_limit?> <!--i class="fas fa-arrow-circle-right"></i-->
						  </a>
					</div>
				</div>
				<?php } } ?>
			</div>			
				<!--div class="row justify-content-center">
				<div class="col-sm-6">
				<div class="form-messages">
					<?php //echo validation_errors(); ?>
					<?php //if($this->session->flashdata("success_msg")){?>
					<div class="alert alert-success">      
						<?php //echo $this->session->flashdata("success_msg")?>
					</div>
					<?php //} ?>
				</div>
				
				
					<?php if(!empty($values)){?>
					<h4><?php echo $values->card_number?></h4>
					
					<?php } ?>

				  </div>
				  
				  </div-->
			</div>
		</div>		
	</section>
</div>