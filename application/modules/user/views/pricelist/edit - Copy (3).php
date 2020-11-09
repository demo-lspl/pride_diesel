<!-- Main Container -->
<div class="">

<section class="content box">
	<div class="addnew-user">
	<a href="<?php echo base_url('user/import_pricelist') ?>" class="btn btn-success"><i class="fa fa-upload"></i> Import Pricelist</a>
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
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3"><h3 class="card-title"></h3></li>
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-two-home-tab" data-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">Retail</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-view-retail-price" data-toggle="pill" href="#custom-tabs-retail-price" role="tab" aria-controls="custom-tabs-retail-price" aria-selected="false">View Retail Price</a>
                  </li>				  
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-profile-tab" data-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" aria-selected="false">Retail+Cost %age</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-view-retail-cost" data-toggle="pill" href="#custom-tabs-retail-cost" role="tab" aria-controls="custom-tabs-retail-price" aria-selected="false">View Retail+Cost %age</a>
                  </li>					  
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-messages-tab" data-toggle="pill" href="#custom-tabs-two-messages" role="tab" aria-controls="custom-tabs-two-messages" aria-selected="false">Add on EFS</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-view-add-efs" data-toggle="pill" href="#custom-tabs-add-efs" role="tab" aria-controls="custom-tabs-add-efs" aria-selected="false">View Add on EFS</a>
                  </li>					  
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-two-settings-tab" data-toggle="pill" href="#custom-tabs-two-settings" role="tab" aria-controls="custom-tabs-two-settings" aria-selected="false">Fix Price</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-view-fix-price" data-toggle="pill" href="#custom-tabs-fix-price" role="tab" aria-controls="custom-tabs-fix-price" aria-selected="false">View Fix Price</a>
                  </li>					  
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-two-tabContent">
<!-------------------------------- Retail Price Wise data --------------------------------------- -->				
                  <div class="tab-pane fade show active" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
					</div>
					<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
						<input type="hidden" name="retail_prices" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Product</th>
								<?php $companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>
							</tr>
							<?php 
							$i = 0;
							$j = 0;
							$totProduct = count($usProducts);							
							foreach($usProducts as $productRows): ?>
							<tr>							
								<td><input type="text" class="form-control form-control-no-border" size="6" name="rp_product_name[]" value="<?= trim($productRows->product) ?>" readonly /></td>								
								<?php
								
								$priceDescrDecode = json_decode($pricelist->retail_price);
								if(!empty($priceDescrDecode)){
									$totalValues = count($priceDescrDecode);
								}else{
									$totalValues = 0;
								}
								$pname = trim($productRows->product);
								?>
								
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type); 	
								?>
								<td>
								<?php
								if(!empty($priceDescrDecode)){	
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
									//if($dynamicrows->$pname == $pname){

										//pre($dynamicrows->$pname[$i]->$compTypes);
										//pre($compTypes);
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes)){ ?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $dynamicrows->$pname[$i]->$compTypes[$i]  ?>" />
								<?php }else{?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="" />									
								<?php }?>
								<?php } //die;
								} } ?>
								<?php if($j >= $totalValues){
										echo '<input type="text" class="form-control" name="'.strtolower($companyTypeResultRows->company_type).'[]" value="" />';
								} 
									 ?>
								</td>		
								<?php	} ?>
							
							</tr>
							<?php $j++; endforeach; ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="rp_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>				  
					  </form>
                  </div>
				<div class="tab-pane fade" id="custom-tabs-retail-price" role="tabpanel" aria-labelledby="custom-tabs-view-retail-price">
						<form method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
						<input type="hidden" name="retail_prices_edit" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Gas Station</th>
								<th>City</th>
								<th>State</th>
								<th>Product</th>
								<th>Retail Price</th>
								<th>Fuel Price</th>
								<?php $cnt =0;$companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>								
							</tr>
							<?php if(!empty($dailyPriceList)){ foreach($dailyPriceList as $dailyPriceListRows):
							$priceListEdited = $this->db->get('pricelist_edit_us')->row();
							$retailPriceDecode = json_decode($priceListEdited->retail_price);
							//pre(count((array)$retailPriceDecode));die;
							$pname = $dailyPriceListRows->product;
							$j = 0;
							$retailPriceabs = $dailyPriceListRows->retail_price;
							?>
							<input type="hidden" name="retail_prices_edit_product[]" value="<?php echo $dailyPriceListRows->product; ?>" />
							
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<td><?= $dailyPriceListRows->retail_price ?></td>
								<td><?= $dailyPriceListRows->pride_price ?></td>
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type);
								if(!empty($retailPriceDecode)){
									$retailPriceCount = count((array)$retailPriceDecode);
								}else{
									$retailPriceCount = 0;
								}	
								?><td>
								<?php if(!empty($retailPriceDecode)){
								//if(array_key_exists($dailyPriceListRows->id, $retailPriceDecode)){
									
								foreach($retailPriceDecode as $k=>$dynamicrows){
									//pre($dynamicrows->$pname[$i]->state[$i]);
									if(str_replace(' ', '-', trim($dailyPriceListRows->id)) == $k){
								if(array_key_exists($pname, $dynamicrows)){
									/* if($dynamicrows->$pname[$i]->gas_station[$i] == $dailyPriceListRows->gas_station && $dynamicrows->$pname[$i]->state[$i] == $dailyPriceListRows->state){ */
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){
								/* if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){		
									$ctabs = $dynamicrows->$pname[$i]->$compTypes[$i];
								}else{
									$ctabs = '0.00';
								}
									$amtAfterDec = floatval($retailPriceabs) - floatval($ctabs); */
									$amtAfterDec = $dynamicrows->$pname[$i]->$compTypes[$i];
								?>	<input type="hidden" name="retail_prices_edit_id[]" value="<?php echo $dailyPriceListRows->id; ?>" />
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $amtAfterDec;  ?>"  />
								<?php }else{?>
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value=""  />									
								<?php }?>
								<?php /* } */} }
																}/* if(empty($dailyPriceListRows->product))
								{echo '<input type="text" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';} */} //}?>
								<?php if($j >= $retailPriceCount){
										echo '<input type="text" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';
										}
									 ?>	</td>							
								<?php	} ?>								
							</tr>
							<?php $j++;endforeach;} ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="rp_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>						
						</form>
				</div>
<!--------------------------------- Retail + Cost Percentage Wise data ---------------- -------------------->
                  <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
					</div>
					<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
						<input type="hidden" name="retail_cost_percent_prices" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Product</th>
								<?php $companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>
							</tr>
							<?php 
							$i = 0;
							$j = 0;
							$totProduct = count($usProducts);							
							foreach($usProducts as $productRows): ?>
							<tr>							
								<td><input type="text" class="form-control form-control-no-border" size="6" name="rcpp_product_name[]" value="<?= trim($productRows->product) ?>" readonly /></td>								
								<?php
								
								$priceDescrDecode = json_decode($pricelist->retail_cost_percent);
								if(!empty($priceDescrDecode)){
									$totalValues = count($priceDescrDecode);
								}else{
									$totalValues = 0;
								}
								$pname = trim($productRows->product);
								?>
								
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type); 	
								?>
								<td>
								<?php
								if(!empty($priceDescrDecode)){	
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
									//if($dynamicrows->$pname == $pname){

										//pre($dynamicrows->$pname[$i]->$compTypes);
										//pre($compTypes);
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes)){ ?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $dynamicrows->$pname[$i]->$compTypes[$i]  ?>" />
								<?php }else{?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="" />									
								<?php }?>
								<?php } //die;
								} } ?>
								<?php if($j >= $totalValues){
										echo '<input type="text" class="form-control" name="'.strtolower($companyTypeResultRows->company_type).'[]" value="" />';
								} 
									 ?>
								</td>		
								<?php	} ?>
							
							</tr>
							<?php $j++; endforeach; ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="rcpp_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>				  
					  </form>
                  </div>
				<div class="tab-pane fade" id="custom-tabs-retail-cost" role="tabpanel" aria-labelledby="custom-tabs-view-retail-cost">
						<form method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
						<input type="hidden" name="retail_cost_percent_prices_edit" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Gas Station</th>
								<th>City</th>
								<th>State</th>
								<th>Product</th>
								<th>Retail Price</th>
								<th>Fuel Price</th>
								<?php $cnt =0;$companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>								
							</tr>
							<?php if(!empty($dailyPriceList)){ foreach($dailyPriceList as $dailyPriceListRows):
							$priceListEdited = $this->db->get('pricelist_edit_us')->row();
							$retailPriceDecode = json_decode($priceListEdited->retail_cost_percent);
							//pre(count((array)$retailPriceDecode));die;
							$pname = $dailyPriceListRows->product;
							$j = 0;
							$retailPriceabs = $dailyPriceListRows->retail_price;
							?>
							<input type="hidden" name="retailCost_prices_edit_product[]" value="<?php echo $dailyPriceListRows->product; ?>" />
							
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<td><?= $dailyPriceListRows->retail_price ?></td>
								<td><?= $dailyPriceListRows->pride_price ?></td>
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type);
								if(!empty($retailPriceDecode)){
									$retailPriceCount = count((array)$retailPriceDecode);
								}else{
									$retailPriceCount = 0;
								}	
								?><td>
								<?php if(!empty($retailPriceDecode)){
								//if(array_key_exists($dailyPriceListRows->id, $retailPriceDecode)){
									
								foreach($retailPriceDecode as $k=>$dynamicrows){
									//pre($k);
									if($dailyPriceListRows->id == $k){
								if(array_key_exists($pname, $dynamicrows)){
								
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){
								/* if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){		
									$ctabs = $dynamicrows->$pname[$i]->$compTypes[$i];
								}else{
									$ctabs = '0.00';
								}
									$amtAfterDec = floatval($retailPriceabs) - floatval($ctabs); */
									$amtAfterDec = $dynamicrows->$pname[$i]->$compTypes[$i];
									
								?>	<input type="hidden" name="retailCost_prices_edit_id[]" value="<?php echo $dailyPriceListRows->id; ?>" />
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $amtAfterDec;  ?>"  />
								<?php }else{?>
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value=""  />									
								<?php }?>
								<?php /* } */} }//die;
																}/* if(empty($dailyPriceListRows->product))
								{echo '<input type="text" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';} */} //}?>
								<?php if($j >= $retailPriceCount){
										echo '<input type="text" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';
										}
									 ?>	</td>							
								<?php	} ?>								
							</tr>
							<?php $j++;endforeach;} ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="rcpp_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>						
						</form>
				</div>
<!-------------------------------- According To EFS Wise data --------------------------------------- -->			
                  <div class="tab-pane fade" id="custom-tabs-two-messages" role="tabpanel" aria-labelledby="custom-tabs-two-messages-tab">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
					</div>
					<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
						<input type="hidden" name="add_on_efs" value="1" />					
						<table class="table table-bordered">
							<tr>
								<th>Product</th>
								<?php $companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>
							</tr>
							<?php 
							$i = 0;
							$j = 0;
							$totProduct = count($usProducts);							
							foreach($usProducts as $productRows): ?>
							<tr>							
								<td><input type="text" class="form-control form-control-no-border" size="6" name="aoe_product_name[]" value="<?= trim($productRows->product) ?>" readonly /></td>								
								<?php
								
								$priceDescrDecode = json_decode($pricelist->add_on_efs);
								if(!empty($priceDescrDecode)){
									$totalValues = count($priceDescrDecode);
								}else{
									$totalValues = 0;
								}
								$pname = trim($productRows->product);
								?>
								
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type); 	
								?>
								<td>
								<?php
								if(!empty($priceDescrDecode)){	
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
									//if($dynamicrows->$pname == $pname){

										//pre($dynamicrows->$pname[$i]->$compTypes);
										//pre($compTypes);
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes)){ ?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $dynamicrows->$pname[$i]->$compTypes[$i]  ?>" />
								<?php }else{?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="" />									
								<?php }?>
								<?php } //die;
								} } ?>
								<?php if($j >= $totalValues){
										echo '<input type="text" class="form-control" name="'.strtolower($companyTypeResultRows->company_type).'[]" value="" />';
								} 
									 ?>
								</td>		
								<?php	} ?>
							
							</tr>
							<?php $j++; endforeach; ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="aoe_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>				  
					  </form> 
                  </div>
				<div class="tab-pane fade" id="custom-tabs-add-efs" role="tabpanel" aria-labelledby="custom-tabs-view-add-efs">
						<form method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
						<input type="hidden" name="aoe_prices_edit" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Gas Station</th>
								<th>City</th>
								<th>State</th>
								<th>Product</th>
								<th>Retail Price</th>
								<th>Fuel Price</th>
								<?php $cnt =0;$companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>								
							</tr>
							<?php if(!empty($dailyPriceList)){ foreach($dailyPriceList as $dailyPriceListRows):
							$priceListEdited = $this->db->get('pricelist_edit_us')->row();
							$retailPriceDecode = json_decode($priceListEdited->add_on_efs);
							//pre(count((array)$retailPriceDecode));die;
							$pname = $dailyPriceListRows->product;
							$j = 0;
							$retailPriceabs = $dailyPriceListRows->retail_price;
							?>
							<input type="hidden" name="aoe_prices_edit_product[]" value="<?php echo $dailyPriceListRows->product; ?>" />
							
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<td><?= $dailyPriceListRows->retail_price ?></td>
								<td><?= $dailyPriceListRows->pride_price ?></td>
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type);
								if(!empty($retailPriceDecode)){
									$retailPriceCount = count((array)$retailPriceDecode);
								}else{
									$retailPriceCount = 0;
								}	
								?><td>
								<?php if(!empty($retailPriceDecode)){
								//if(array_key_exists($dailyPriceListRows->id, $retailPriceDecode)){
									
								foreach($retailPriceDecode as $k=>$dynamicrows){
									//pre($dynamicrows->$pname[$i]->state[$i]);
									if(str_replace(' ', '-', trim($dailyPriceListRows->id)) == $k){
								if(array_key_exists($pname, $dynamicrows)){
									/* if($dynamicrows->$pname[$i]->gas_station[$i] == $dailyPriceListRows->gas_station && $dynamicrows->$pname[$i]->state[$i] == $dailyPriceListRows->state){ */
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){
								/* if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){		
									$ctabs = $dynamicrows->$pname[$i]->$compTypes[$i];
								}else{
									$ctabs = '0.00';
								}
									$amtAfterDec = floatval($retailPriceabs) - floatval($ctabs); */
									$amtAfterDec = $dynamicrows->$pname[$i]->$compTypes[$i];
								?>	<input type="hidden" name="aoe_prices_edit_id[]" value="<?php echo $dailyPriceListRows->id; ?>" />
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $amtAfterDec;  ?>"  />
								<?php }else{?>
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value=""  />									
								<?php }?>
								<?php /* } */} }
																}/* if(empty($dailyPriceListRows->product))
								{echo '<input type="text" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';} */} //}?>
								<?php if($j >= $retailPriceCount){
										echo '<input type="text" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';
										}
									 ?>	</td>							
								<?php	} ?>								
							</tr>
							<?php $j++;endforeach;} ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="aeo_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>						
						</form>
				</div>
				<!------------------ Fix Price Wise data -------- -->				
                  <div class="tab-pane fade" id="custom-tabs-two-settings" role="tabpanel" aria-labelledby="custom-tabs-two-settings-tab">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
					</div>
					<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
						<input type="hidden" name="fix_price" value="1" />					
						<table class="table table-bordered">
							<tr>
								<th>Product</th>
								<?php $companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>
							</tr>
							<?php 
							$i = 0;
							$j = 0;
							$totProduct = count($usProducts);							
							foreach($usProducts as $productRows): ?>
							<tr>							
								<td><input type="text" class="form-control form-control-no-border" size="6" name="fp_product_name[]" value="<?= trim($productRows->product) ?>" readonly /></td>								
								<?php
								
								$priceDescrDecode = json_decode($pricelist->fix_price);
								if(!empty($priceDescrDecode)){
									$totalValues = count($priceDescrDecode);
								}else{
									$totalValues = 0;
								}
								$pname = trim($productRows->product);
								?>
								
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type); 	
								?>
								<td>
								<?php
								if(!empty($priceDescrDecode)){	
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
									//if($dynamicrows->$pname == $pname){

										//pre($dynamicrows->$pname[$i]->$compTypes);
										//pre($compTypes);
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes)){ ?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $dynamicrows->$pname[$i]->$compTypes[$i]  ?>" />
								<?php }else{?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="" />									
								<?php }?>
								<?php } //die;
								} } ?>
								<?php if($j >= $totalValues){
										echo '<input type="text" class="form-control" name="'.strtolower($companyTypeResultRows->company_type).'[]" value="" />';
								} 
									 ?>
								</td>		
								<?php	} ?>
							
							</tr>
							<?php $j++; endforeach; ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="fp_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>				  
					  </form>  
                  </div>
				<div class="tab-pane fade" id="custom-tabs-fix-price" role="tabpanel" aria-labelledby="custom-tabs-view-fix-price">
						<form method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
						<input type="hidden" name="fp_prices_edit" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Gas Station</th>
								<th>City</th>
								<th>State</th>
								<th>Product</th>
								<th>Retail Price</th>
								<th>Fuel Price</th>
								<?php $cnt =0;$companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>								
							</tr>
							<?php if(!empty($dailyPriceList)){ foreach($dailyPriceList as $dailyPriceListRows):
							$priceListEdited = $this->db->get('pricelist_edit_us')->row();
							$retailPriceDecode = json_decode($priceListEdited->fix_price);
							//pre(count((array)$retailPriceDecode));die;
							$pname = $dailyPriceListRows->product;
							$j = 0;
							$retailPriceabs = $dailyPriceListRows->retail_price;
							?>
							<input type="hidden" name="fp_prices_edit_product[]" value="<?php echo $dailyPriceListRows->product; ?>" />
							
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<td><?= $dailyPriceListRows->retail_price ?></td>
								<td><?= $dailyPriceListRows->pride_price ?></td>
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type);
								if(!empty($retailPriceDecode)){
									$retailPriceCount = count((array)$retailPriceDecode);
								}else{
									$retailPriceCount = 0;
								}	
								?><td>
								<?php if(!empty($retailPriceDecode)){
								//if(array_key_exists($dailyPriceListRows->id, $retailPriceDecode)){
									
								foreach($retailPriceDecode as $k=>$dynamicrows){
									//pre($dynamicrows->$pname[$i]->state[$i]);
									if(str_replace(' ', '-', trim($dailyPriceListRows->id)) == $k){
								if(array_key_exists($pname, $dynamicrows)){
									/* if($dynamicrows->$pname[$i]->gas_station[$i] == $dailyPriceListRows->gas_station && $dynamicrows->$pname[$i]->state[$i] == $dailyPriceListRows->state){ */
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){
								/* if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){		
									$ctabs = $dynamicrows->$pname[$i]->$compTypes[$i];
								}else{
									$ctabs = '0.00';
								}
									$amtAfterDec = floatval($retailPriceabs) - floatval($ctabs); */
									$amtAfterDec = $dynamicrows->$pname[$i]->$compTypes[$i];
								?>	<input type="hidden" name="fp_prices_edit_id[]" value="<?php echo $dailyPriceListRows->id; ?>" />
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $amtAfterDec;  ?>"  />
								<?php }else{?>
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value=""  />									
								<?php }?>
								<?php /* } */} }
																}/* if(empty($dailyPriceListRows->product))
								{echo '<input type="text" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';} */} //}?>
								<?php if($j >= $retailPriceCount){
										echo '<input type="text" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';
										}
									 ?>	</td>							
								<?php	} ?>								
							</tr>
							<?php $j++;endforeach;} ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="fp_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>						
						</form>
				</div>				  
                </div>
              </div>
              <!-- /.card -->
            </div>
          </div>
        </div>		
			<div class="row justify-content-center">
				<div class="col-sm-12">

				  </div>
			  </div>
		</div>
	</div>		
</section>
</div>