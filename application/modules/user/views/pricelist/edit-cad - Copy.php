<!-- Main Container -->
<div class="">

<section class="content box">
	<div class="addnew-user">
	<a href="<?php echo base_url('user/import_pricelist_ca') ?>" class="btn btn-success"><i class="fa fa-upload"></i> Import Pricelist</a>
	</div>
	<div class="card card-default">
		<div class="card-header bg-card-header">
			<h3 class="card-title">Company <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

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
					<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist_ca/').$this->uri->segment(3) ?>">
						<table class="table table-bordered">
							<tr>
								<th>Product</th>
								<!--th>EFS</th>
								<th>Retail</th-->
								<?php $companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>
							</tr>
							<?php 
							$i = 0;
							$j = 0;
								$totProduct = count($products);							
							foreach($products as $productRows): ?>
							<tr>
								<td><input type="text" class="form-control" name="product_name[]" value="<?= $productRows->product_name ?>" /></td>
								<?php
								$efsPriceDecode = json_decode($pricelist->efs_price);
								$priceDescrDecode = json_decode($pricelist->price_descr);
								$pname = $productRows->product_name;
								?>
								<!--td>	
								<?php 
								if(!empty($efsPriceDecode)){
								foreach($efsPriceDecode as $k=>$efsrows){
									if(array_key_exists($pname, $efsrows)){
										$efs_amount = $efsrows->$pname->efs_amt;
										//$retail_amount = $efsrows->$pname->retail_amt;										
									}
								}?>
								<?php if(!empty($efs_amount)){ ?>
									<input type="text" class="form-control" name="efs_amt[]" value="<?= $efs_amount ?>" />
									<?php }else{?>
									<input type="text" class="form-control" name="efs_amt[]" value="" />
									<?php }unset($efs_amount);?>								
								<?php } ?>
								</td>
								<td>	
								<?php 
								if(!empty($efsPriceDecode)){
								foreach($efsPriceDecode as $k=>$efsrows){
									if(array_key_exists($pname, $efsrows)){
										//$efs_amount = $efsrows->$pname->efs_amt;
										$retail_amount = $efsrows->$pname->retail_amt;										
									}
								}?>

								<?php if(!empty($retail_amount)){ ?>
									<input type="text" class="form-control" name="retail_amt[]" value="<?= $retail_amount ?>" />
									<?php }else{?>
									<input type="text" class="form-control" name="retail_amt[]" value="" />
									<?php }unset($retail_amount);?>								
								<?php } ?>
								</td-->								
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type); 	
								?>
								<?php 
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
								?>
								<td><?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){ ?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $dynamicrows->$pname[$i]->$compTypes[$i]  ?>" /></td>
								<?php }else{?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="" /></td>									
								<?php }?>
								<?php } } ?>
								<?php if($j >= count($priceDescrDecode)){
										echo '<td><input type="text" class="form-control" name="'.strtolower($companyTypeResultRows->company_type).'[]" value="" /></td>';
										}
									 ?>								
								<?php	} ?>
							
							</tr>
							<?php $j++; endforeach; ?>
						</table>
						<div class="form-group">
						  <button type="submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>				  
					  </form>
				  </div>
			  </div>
		</div>
	</div>		
</section>
</div>