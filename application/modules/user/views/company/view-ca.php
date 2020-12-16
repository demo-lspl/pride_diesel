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
					$getPriceDate = $this->db->select('date, date_created')->where('id', 1)->get('retail_pricing_ca')->row();	
					$userSessDetails = $this->session->userdata('userdata');
					$this->db->select('users.*, company_types.*');
					$this->db->join('company_types', 'company_types.id=users.company_type_ca');
					//$this->db->join('retail_pricing_ca', 'retail_pricing_ca.id=users.company_type');
					$this->db->where('users.id', $userSessDetails->id);
					$get_user_type = $this->db->get('users')->row();
					if(is_object($get_user_type)){
						$pricingType = $get_user_type->cad_pricing;
					}else{
						$pricingType = '';
					}
					
					if(!empty($get_user_type)){
					?>
						<table class="table table-bordered">
							<tr>
								<th>Gas Station</th>
								<th>City</th>
								<th>State</th>
								<th>Product</th>
								<?php $i=0;$cnt =0;$companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): 
								$cTYPE = $companyTypeResultRows->company_type;

								if(strtolower($cTYPE) == strtolower($get_user_type->company_type)){?>
								<th><?php echo "Pride Diesel Price"; //$companyTypeResultRows->company_type ?></th>
								<th>Date</th>
								<?php } endforeach; ?>								
							</tr>
							<?php if(!empty($dailyPriceList)){ foreach($dailyPriceList as $dailyPriceListRows):
							$priceListEdited = $this->db->get('pricelist_edit_ca')->row();
							$retailPriceDecode = json_decode($priceListEdited->$pricingType);

							$pname = $dailyPriceListRows->product;

							$j = 0;
							?>
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type);	
								?>
								<?php if(!empty($retailPriceDecode)){

								foreach($retailPriceDecode as $k=>$dynamicrows){

								if(str_replace(' ', '-', trim($dailyPriceListRows->id)) == $k){
								if(array_key_exists($pname, $dynamicrows)){
								if($compTypes == strtolower($get_user_type->company_type)){
								?>
								<?php 
								if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){
									$amtAfterDec = $dynamicrows->$pname[$i]->$compTypes[$i];			
									//echo "<td>".number_format($taxTotal, 4)."</td>"; 
									echo "<td>".number_format($amtAfterDec, 4)."</td>"; 
									 } } } }
								}} 	} ?>
								<td>
									<?php echo date('d-m-Y', strtotime($getPriceDate->date_created)); ?>
								</td>	
							</tr>
							<?php $j++;endforeach;} ?>
							
						</table>
						<?php }else{ ?>
							<p class="alert alert-info">Package not assigned.</p>
						<?php }?>
				  </div>
			  </div>
			  <p class="text-right"><a class="btn btn-info back-top" href="#"><i class="fa fa-arrow-up"></i></a></p>
		</div>
	</div>		
</section>
</div>