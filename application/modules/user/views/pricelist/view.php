<!-- Main Container -->
<div class="">

<section class="content box">
	<div class="card card-default">
		<div class="card-header bg-card-header">
			<h3 class="card-title">Prices for you</h3>

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
					$userSessDetails = $this->session->userdata('userdata');
					$this->db->join('company_types', 'company_types.id=users.company_type');
					
					$this->db->where('users.id', $userSessDetails->id);
					$get_user_type = $this->db->get('users')->row();
					if(!empty($get_user_type)){
					?>
						<table class="table table-bordered">
							<tr>
								<th>Product Name</th>
								<th>Retail Price</th>
								<?php foreach($companyTypeResult as $key=>$companyTypeResultRows):
								if($companyTypeResultRows->company_type == $get_user_type->company_type){	
								?>
								<th>Pride Diesel Price<?php /*$companyTypeResultRows->company_type*/ ?></th>

								<?php } endforeach; ?>
							</tr>
							<?php 
							$i = 0;				
							foreach($products as $productRows): $EFS_PRICE = 0;	?>
							<tr>
								<td><?= $productRows->product_name ?></td>
								<?php
								$efsPriceDecode = json_decode($pricelist->efs_price);
								$priceDescrDecode = json_decode($pricelist->price_descr);
								$pname = $productRows->product_name;
								?>
								<td>
								<?php 
								if(!empty($efsPriceDecode)){
								foreach($efsPriceDecode as $k=>$efsrows){ ?>
								<?php if(array_key_exists($pname, $efsrows)){
								?>
								<?php if(!empty($efsrows->$pname->efs_amt)){ $EFS_PRICE = $efsrows->$pname->efs_amt;?>
									
									<?php }?>
								
								<?php } }?>
								
								<?php foreach($efsPriceDecode as $k=>$efsrows){ ?>
								<?php if(array_key_exists($pname, $efsrows)){
								?>
								<?php if(!empty($efsrows->$pname->retail_amt)){ $retail_price = $efsrows->$pname->retail_amt; ?>
									<?= $efsrows->$pname->retail_amt ?>
									<?php }?>
								
								
								<?php }?>
								<?php }   ?>									
								<?php } ?>
								</td>
								<td>
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type);
								if($compTypes == strtolower($get_user_type->company_type)){	
								?>
								
								<?php 
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i]) && $EFS_PRICE > 0){ 
										$diffrence = abs($retail_price - $EFS_PRICE);
										$gapAmount = '0.0250';
										if($diffrence > $gapAmount){
											$bothGap = $diffrence - $gapAmount;
											$addAmt = $diffrence - $bothGap;
											echo $EFS_PRICE + $dynamicrows->$pname[$i]->$compTypes[$i] + $addAmt;
										}else{
									?>
									<?php echo $EFS_PRICE + $dynamicrows->$pname[$i]->$compTypes[$i]; ?>
										<?php } }?>
								<?php } } ?>
									
								<?php }	} ?>
								</td>
							</tr>
							<?php unset($EFS_PRICE); endforeach; ?>
						</table>
						<?php }else{ ?>
							<p class="alert alert-info">Package not assigned.</p>
						<?php }?>
				  </div>
			  </div>
		</div>
	</div>		
</section>
</div>