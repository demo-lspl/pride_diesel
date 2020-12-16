<!-- Main Container -->
<div class="">

<section class="content box">
	<div class="addnew-user">
		<a href="<?php echo base_url('user/import_pricelist_ca') ?>" class="btn btn-success"><i class="fa fa-upload"></i> Import Pricelist EFS</a>
		<a href="<?php echo base_url('user/import_pricelist_ca_husky') ?>" class="btn btn-success"><i class="fa fa-upload"></i> Import Pricelist Husky</a>
	</div>
	<div class="card card-default">
		<div class="card-header bg-card-header">
			<h3 class="card-title">Canada Pricing <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

			<div class="card-tools">
			  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
			  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
			</div>
		</div>
		<?php 
		$fourthSeg = $this->uri->segment(4);
		?>
		<div class="card-body pricing-table">
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary card-tabs">
              <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3"><h3 class="card-title"></h3></li>					  
                  <li class="nav-item">
                    <a class="nav-link <?php if((empty($fourthSeg)) || (!empty($fourthSeg) && $fourthSeg == 'add-on-efs')){echo "active";}?>" id="custom-tabs-two-messages-tab" data-toggle="pill" href="#add-on-efs" role="tab" aria-controls="add-on-efs" aria-selected="true">Add on EFS</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if(!empty($fourthSeg) && $fourthSeg == 'view-add-on-efs'){echo "active";} ?>" id="custom-tabs-view-add-efs" data-toggle="pill" href="#view-add-on-efs" role="tab" aria-controls="view-add-on-efs" aria-selected="false">View Add on EFS</a>
                  </li>					  
                  <li class="nav-item">
                    <a class="nav-link <?php if(!empty($fourthSeg) && $fourthSeg == 'set-fix-price'){echo "active";} ?>" id="custom-tabs-two-settings-tab" data-toggle="pill" href="#set-fix-price" role="tab" aria-controls="set-fix-price" aria-selected="false">Fix Price</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if(!empty($fourthSeg) && $fourthSeg == 'view-fix-price'){echo "active";} ?>" id="custom-tabs-view-fix-price" data-toggle="pill" href="#view-fix-price" role="tab" aria-controls="view-fix-price" aria-selected="false">View Fix Price</a>
                  </li>
				  <!-- Husky API -->
                  <li class="nav-item">
                    <a class="nav-link <?php if(!empty($fourthSeg) && $fourthSeg == 'set-add-on-husky'){echo "active";} ?>" id="custom-tabs-husky-addon1" data-toggle="pill" href="#set-add-on-husky" role="tab" aria-controls="set-add-on-husky" aria-selected="false">Add on Husky</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link <?php if(!empty($fourthSeg) && $fourthSeg == 'view-add-on-husky'){echo "active";} ?>" id="custom-tabs-husky-addon-view1" data-toggle="pill" href="#view-add-on-husky" role="tab" aria-controls="view-add-on-husky" aria-selected="false">View Add on Husky</a>
                  </li>					  
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-two-tabContent">

<!-------------------------------- Set Add On EFS price ----------------------------------------->			
                <div class="tab-pane fade <?php if((empty($fourthSeg)) || (!empty($fourthSeg) && $fourthSeg == 'add-on-efs')){echo "show active";}?>" id="add-on-efs" role="tabpanel" aria-labelledby="custom-tabs-two-messages-tab">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
					</div>
					<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist_ca/').$this->uri->segment(3)."/add-on-efs" ?>">
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
							//$us3Products = array('ULSD'=>'ULSD', 'ULSR'=>'ULSR', 'DEFD'=>'DEFD');							
							$us3Products = array('ULSD'=>'ULSD');							
							foreach($us3Products as $productRows): ?>
							<tr>							
								<td><input type="text" class="form-control form-control-no-border" size="6" name="aoe_product_name[]" value="<?= $productRows ?>" readonly /></td>								
								<?php
								
								$priceDescrDecode = json_decode($pricelist->add_on_efs);
								if(!empty($priceDescrDecode)){
									$totalValues = count($priceDescrDecode);
								}else{
									$totalValues = 0;
								}
								$pname = $productRows;
								?>
								
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type); 	
								?>
								<td>
								<?php
								if(!empty($priceDescrDecode)){	
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes)){ ?>
									<input type="number" step="any" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $dynamicrows->$pname[$i]->$compTypes[$i]  ?>" />
								<?php }else{?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="" />									
								<?php }?>
								<?php } 
								} } ?>
								<?php if($j >= $totalValues){
										echo '<input type="number" step="any" class="form-control" name="'.strtolower($companyTypeResultRows->company_type).'[]" value="" />';
								} 
									 ?>
								</td>		
								<?php	} ?>
							
							</tr>
							<tr>
								<td><input type="text" class="form-control form-control-no-border" size="6" name="aoe_defd[]" value="DEFD" readonly /></td>
								<?php
								$defPricelistEditCA = json_decode($dailyEditPriceList[0]->defd_price);
								
								foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type);
								if(!empty($defPricelistEditCA->DEFD->$compTypes[0])){
								?>
								<td>
									<input class="form-control" type="text" name="defd_<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?= $defPricelistEditCA->DEFD->$compTypes[0] ?>" />
								</td>
								<?php	
								}else{	
								?>								
								<td>
									<input class="form-control" type="text" name="defd_<?= strtolower($companyTypeResultRows->company_type) ?>[]" />
								</td>
								<?php
								}								
									}	
								?>								
							</tr>
							<?php $j++; endforeach; ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="aoe_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>				  
					  </form> 
                </div>
				<div class="tab-pane fade <?php if(!empty($fourthSeg) && $fourthSeg === 'view-add-on-efs'){echo "show active";}?>" id="view-add-on-efs" role="tabpanel" aria-labelledby="custom-tabs-view-add-efs">
						<?php 
							$getCreatedDate = $this->db->select('date_created')->where('id', 1)->get('retail_pricing_ca')->row();
							$dateCreated = null;
							if(is_object($getCreatedDate) && !empty($getCreatedDate)){
								$dateCreated = date('Y-m-d', strtotime($getCreatedDate->date_created));
							}
						?>
						<p><strong>Last Updated: </strong><?= $dateCreated ?></p>
						<form method="post" action="<?php echo base_url('user/edit_pricelist_ca/').$this->uri->segment(3)."/view-add-on-efs" ?>" class="view-form">
						<input type="hidden" name="aoe_prices_edit" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Gas Station</th>
								<th>City</th>
								<th>State</th>
								<th>Product</th>
								<!--th>Retail Price</th-->
								<th>Fuel Price</th>
								<?php $cnt =0;$companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>								
							</tr>
							<?php 
							if(!empty($dailyPriceList)){ foreach($dailyPriceList as $dailyPriceListRows):
							$priceListEdited = $this->db->get('pricelist_edit_ca')->row();
							$retailPriceDecode = json_decode($priceListEdited->add_on_efs);
							$pname = $dailyPriceListRows->product;
							$j = 0;
							//$retailPriceabs = $dailyPriceListRows->retail_price;
							?>
							<input type="hidden" name="aoe_prices_edit_product[]" value="<?php echo $dailyPriceListRows->product; ?>" />
							<input type="hidden" name="aoe_prices_edit_gas_station[]" value="<?php echo $dailyPriceListRows->gas_station; ?>" />							
							<input type="hidden" name="aoe_prices_edit_state[]" value="<?php echo $dailyPriceListRows->state; ?>" />							
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<!--td><?= $dailyPriceListRows->retail_price ?></td-->
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
								foreach($retailPriceDecode as $k=>$dynamicrows){
									if(str_replace(' ', '-', trim($dailyPriceListRows->id)) == $k){
								if(array_key_exists($pname, $dynamicrows)){
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){
									$amtAfterDec = $dynamicrows->$pname[$i]->$compTypes[$i];
								?>	<input type="hidden" name="aoe_prices_edit_id[]" value="<?php echo $dailyPriceListRows->id; ?>" />
									<input type="number" step="any" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $amtAfterDec;  ?>"  />
								<?php }else{?>
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value=""  />									
								<?php }?>
								<?php } }
																}} ?>
								<?php if($j >= $retailPriceCount){
										echo '<input type="number" step="any" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';
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
						<p class="text-right"><a class="btn btn-info back-top" href="#"><i class="fa fa-arrow-up"></i></a></p>
				</div>
<!--########################## Set Fix Price ################################################-->				
                <div class="tab-pane fade <?php if(!empty($fourthSeg) && $fourthSeg === 'set-fix-price'){echo "active show";}?>" id="set-fix-price" role="tabpanel" aria-labelledby="custom-tabs-two-settings-tab">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
					</div>
					
					<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist_ca/').$this->uri->segment(3)."/set-fix-price" ?>">
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
							$us3Products = array('ULSD'=>'ULSD');						
							foreach($us3Products as $productRows): ?>
							<tr>							
								<td><input type="text" class="form-control form-control-no-border" size="6" name="fp_product_name[]" value="<?= $productRows ?>" readonly /></td>								
								<?php
								
								$priceDescrDecode = json_decode($pricelist->fix_price);
								if(!empty($priceDescrDecode)){
									$totalValues = count($priceDescrDecode);
								}else{
									$totalValues = 0;
								}
								$pname = $productRows;
								?>
								
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type); 	
								?>
								<td>
								<?php
								if(!empty($priceDescrDecode)){	
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes)){ ?>
									<input type="number" step="any" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $dynamicrows->$pname[$i]->$compTypes[$i]  ?>" />
								<?php }else{?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="" />									
								<?php }?>
								<?php } 
								} } ?>
								<?php if($j >= $totalValues){
										echo '<input type="number" step="any" class="form-control" name="'.strtolower($companyTypeResultRows->company_type).'[]" value="" />';
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
				<div class="tab-pane fade <?php if(!empty($fourthSeg) && $fourthSeg === 'view-fix-price'){echo "show active";}?>" id="view-fix-price" role="tabpanel" aria-labelledby="custom-tabs-view-fix-price">
						<?php 
							$getCreatedDate = $this->db->select('date_created')->where('id', 1)->get('retail_pricing_ca')->row();
							$dateCreated = null;
							if(is_object($getCreatedDate) && !empty($getCreatedDate)){
								$dateCreated = date('Y-m-d', strtotime($getCreatedDate->date_created));
							}
						?>
						<p><strong>Last Updated: </strong><?= $dateCreated ?></p>
						<form method="post" action="<?php echo base_url('user/edit_pricelist_ca/').$this->uri->segment(3)."/view-fix-price" ?>" class="view-form">
						<input type="hidden" name="fp_prices_edit" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Gas Station</th>
								<th>City</th>
								<th>State</th>
								<th>Product</th>
								<!--th>Retail Price</th-->
								<th>Fuel Price</th>
								<?php $cnt =0;$companyTypes = '';foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
								<th><?= $companyTypeResultRows->company_type ?></th>

								<?php endforeach; ?>								
							</tr>
							<?php if(!empty($dailyPriceList)){ foreach($dailyPriceList as $dailyPriceListRows):
							$priceListEdited = $this->db->get('pricelist_edit_ca')->row();
							$retailPriceDecode = json_decode($priceListEdited->fix_price);
							$pname = $dailyPriceListRows->product;
							$j = 0;
							//$retailPriceabs = $dailyPriceListRows->retail_price;
							?>
							<input type="hidden" name="fp_prices_edit_product[]" value="<?php echo $dailyPriceListRows->product; ?>" />
							<input type="hidden" name="fp_prices_edit_station[]" value="<?php echo $dailyPriceListRows->gas_station; ?>" />
							<input type="hidden" name="fp_prices_edit_state[]" value="<?php echo $dailyPriceListRows->state; ?>" />							
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<!--td><?= $dailyPriceListRows->retail_price ?></td-->
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
								foreach($retailPriceDecode as $k=>$dynamicrows){
									if(str_replace(' ', '-', trim($dailyPriceListRows->id)) == $k){
								if(array_key_exists($pname, $dynamicrows)){
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){
									$amtAfterDec = $dynamicrows->$pname[$i]->$compTypes[$i];
								?>	<input type="hidden" name="fp_prices_edit_id[]" value="<?php echo $dailyPriceListRows->id; ?>" />
									<input type="number" step="any" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $amtAfterDec;  ?>"  />
								<?php }else{?>
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value=""  />									
								<?php }?>
								<?php } }
								}} ?>
								<?php if($j >= $retailPriceCount){
										echo '<input type="number" step="any" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';
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
						<p class="text-right"><a class="btn btn-info back-top" href="#"><i class="fa fa-arrow-up"></i></a></p>
				</div>
<!--======================== Set price Husky ====================================== -->			
                <div class="tab-pane fade <?php if(!empty($fourthSeg) && $fourthSeg === 'set-add-on-husky'){echo "show active";}?>" id="set-add-on-husky" role="tabpanel" aria-labelledby="custom-tabs-husky-addon1">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
					</div>
					<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist_husky_ca/').$this->uri->segment(3)."/set-add-on-husky" ?>">
						<input type="hidden" name="add_on_husky" value="1" />					
						<table class="table table-bordered">
							<tr>
								<th>Product</th>
								<?php 
								$companyTypes = '';
								foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
									<th><?= $companyTypeResultRows->company_type ?></th>
								<?php endforeach; ?>
							</tr>
							<?php 
							$i = 0;
							$j = 0;
							//$us3Products = array('ULSD'=>'ULSD', 'ULSR'=>'ULSR', 'DEFD'=>'DEFD');							
							$us3Products = array('ULSD'=>'ULSD');							
							foreach($us3Products as $productRows): ?>
							<tr>							
								<td><input type="text" class="form-control form-control-no-border" size="6" name="aoh_product_name[]" value="<?= $productRows ?>" readonly /></td>								
								<?php
								
								$priceDescrDecode = json_decode($pricelistHusky->add_on_husky);
								if(!empty($priceDescrDecode)){
									$totalValues = count($priceDescrDecode);
								}else{
									$totalValues = 0;
								}
								$pname = $productRows;
								?>
								
								<?php foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type); 	
								?>
								<td>
								<?php
								if(!empty($priceDescrDecode)){	
								foreach($priceDescrDecode as $k=>$dynamicrows){
								if(array_key_exists($pname, $dynamicrows)){
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes)){ ?>
									<input type="number" step="any" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $dynamicrows->$pname[$i]->$compTypes[$i]  ?>" />
								<?php }else{?>
									<input type="text" class="form-control" name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="" />									
								<?php }?>
								<?php } 
								} } ?>
								<?php if($j >= $totalValues){
										echo '<input type="number" step="any" class="form-control" name="'.strtolower($companyTypeResultRows->company_type).'[]" value="" />';
								} 
									 ?>
								</td>		
								<?php	} ?>
							
							</tr>
							<tr>
								<td><input type="text" class="form-control form-control-no-border" size="6" name="aoh_defd[]" value="DEFD" readonly /></td>
								<?php
								$defPricelistEditCA = json_decode($dailyEditPriceList[0]->defd_price);
								
								foreach($companyTypeResult as $companyTypeResultRows){
								$compTypes = strtolower($companyTypeResultRows->company_type);
								if(!empty($defPricelistEditCA->DEFD->$compTypes[0])){
								?>
								<td>
									<input class="form-control" type="text" name="defd_<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?= $defPricelistEditCA->DEFD->$compTypes[0] ?>" />
								</td>
								<?php	
								}else{	
								?>								
								<td>
									<input class="form-control" type="text" name="defd_<?= strtolower($companyTypeResultRows->company_type) ?>[]" />
								</td>
								<?php
								}								
									}	
								?>								
							</tr>
							<?php $j++; endforeach; ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="aoh_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>				  
					  </form> 
                </div>
				<div class="tab-pane fade <?php if(!empty($fourthSeg) && $fourthSeg === 'view-add-on-husky'){echo "show active";}?>" id="view-add-on-husky" role="tabpanel" aria-labelledby="custom-tabs-husky-addon-view1">
						<?php 
							$getCreatedDate = $this->db->select('date_created')->where('id', 1)->get('retail_pricing_husky_ca')->row();
							$dateCreated = null;
							if(is_object($getCreatedDate) && !empty($getCreatedDate)){
								$dateCreated = date('Y-m-d', strtotime($getCreatedDate->date_created));
							}
						?>
						<p><strong>Last Updated: </strong><?= $dateCreated ?></p>
						<form method="post" action="<?php echo base_url('user/edit_pricelist_husky_ca/').$this->uri->segment(3)."/view-add-on-husky" ?>" class="view-form">
						<input type="hidden" name="aoh_prices_edit" value="1" />
						<table class="table table-bordered">
							<tr>
								<th>Gas Station Id</th>
								<th>Gas Station Name</th>
								<th>State</th>
								<th>Product</th>
								<!--th>Retail Price</th-->
								<th>Fuel Price</th>
								<?php 
								$cnt =0;
								$companyTypes = '';
								foreach($companyTypeResult as $key=>$companyTypeResultRows): ?>
									<th><?= $companyTypeResultRows->company_type ?></th>
								<?php endforeach; ?>							
							</tr>
							<?php 
							if(!empty($dailyPriceListHusky)){ foreach($dailyPriceListHusky as $dailyPriceListRows):
							$priceListEdited = $this->db->get('pricelist_edit_ca_husky')->row();
							$retailPriceDecode = json_decode($priceListEdited->add_on_husky);
							$pname = $dailyPriceListRows->product;
							$j = 0;
							//$retailPriceabs = $dailyPriceListRows->retail_price;
							?>
							<input type="hidden" name="aoh_prices_edit_product[]" value="<?php echo $dailyPriceListRows->product; ?>" />
							<input type="hidden" name="aoh_prices_edit_gas_station[]" value="<?php echo $dailyPriceListRows->gas_station; ?>" />							
							<input type="hidden" name="aoh_prices_edit_state[]" value="<?php echo $dailyPriceListRows->state; ?>" />							
							<tr>
								<td><?= $dailyPriceListRows->gas_station ?></td>
								<td><?= $dailyPriceListRows->city ?></td>
								<td><?= $dailyPriceListRows->state ?></td>
								<td><?= $dailyPriceListRows->product ?></td>
								<!--td><?= $dailyPriceListRows->retail_price ?></td-->
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
								foreach($retailPriceDecode as $k=>$dynamicrows){
									if(str_replace(' ', '-', trim($dailyPriceListRows->id)) == $k){
								if(array_key_exists($pname, $dynamicrows)){
								?>
								<?php if(!empty($dynamicrows->$pname[$i]->$compTypes[$i])){
									$amtAfterDec = $dynamicrows->$pname[$i]->$compTypes[$i];
								?>	<input type="hidden" name="aoh_prices_edit_id[]" value="<?php echo $dailyPriceListRows->id; ?>" />
									<input type="number" step="any" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value="<?php echo $amtAfterDec;  ?>"  />
								<?php }else{?>
									<input type="text" class="form-control " name="<?= strtolower($companyTypeResultRows->company_type) ?>[]" value=""  />									
								<?php }?>
								<?php } }
																}} ?>
								<?php if($j >= $retailPriceCount){
										echo '<input type="number" step="any" class="form-control " name="'.strtolower($companyTypeResultRows->company_type).'[]" value=""  />';
										}
									 ?>	</td>							
								<?php	} ?>								
							</tr>
							<?php $j++;endforeach;} ?>
						</table>
						<div class="form-group">
						  <button type="submit" name="aeh_submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
						</div>						
						</form>
						<p class="text-right"><a class="btn btn-info back-top" href="#"><i class="fa fa-arrow-up"></i></a></p>
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