<!-- Main Container -->
<div class="">

	<section class="content box">
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
				<div class="col-sm-6">
				<div class="form-messages error">
					<?php echo validation_errors(); ?>
					<?php if($this->session->flashdata("success")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success")?>
					</div>
					<?php } ?>
				</div>
				
				<?php //print_r($company);//foreach($userdata as $userdatas){$values = $userdatas;}// ?>
				  <!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('user/edit_pricelist/').$this->uri->segment(3) ?>">
					<div class="form-group">
						<label for="tax_type">Product</label>
						<select name="product_type" class="form-control select2" style="width: 100%;" >
							<option value="">Select Product</option>	
							<?php foreach($products as $productRows): ?>
								<option <?php if($productRows->id == $pricelist->product_id){echo "selected";} ?> value="<?php echo $productRows->id; ?>"><?php echo $productRows->product_name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					
			<div class="invoice-edit-block">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap no-padding-left no-padding-right">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro" >
							<div class="col-md-12 input_descr_wrap label-box mobile-view2 no-padding-left no-padding-right">
								
								<div class="col-md-6 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Company type </label>
								</div>
								<div class="col-md-6 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Price</label>
								</div>	

							</div>
							<?php if(empty($pricelist->price_descr)){ ?>
									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box no-padding-left no-padding-right">
												
									<div class="col-md-6 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Company Type <span class="required">*</span></label>
										<select name="company_id[]" class="company_type form-control">
											<option value="">Select Company</option>
											<?php
											if(!empty($companyTypeResult)){ ?>
											<?php foreach($companyTypeResult as $companyType): ?>
												<option <?php //if($companyType->id == $pricelist->product_id){echo "selected";} ?> value="<?php echo $companyType->id; ?>"><?php echo ucwords($companyType->company_type); ?></option>
											<?php endforeach; ?>	
											<?php }?>										
										</select>										
									</div>
									<div class="col-md-6 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Price</label>
										<input type="text" name="price[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Price" value="">
									</div>
								
								</div>
                                <div class="col-sm-12 btn-row"><button class="btn btn-primary add_more_price_descr" type="button">Add</button></div>															
						<?php  } ?>
						<!--/div-->

				<?php if(!empty($pricelist) && $pricelist->price_descr != ''){
					$priceListDetail = json_decode($pricelist->price_descr);
					if(!empty($priceListDetail)){
						$i = 0;
						foreach($priceListDetail as $priceListDetails){
					?>

									<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box <?php if($i==0){ echo 'btn-3 ';}if($i==1){ echo 'scend-tr ';}else{ echo 'edit-row1 scend-tr';}?> no-padding-left no-padding-right">
												
									<div class="col-md-6 col-sm-12 col-xs-12 form-group">															
										<label class="col-md-12 col-sm-12 col-xs-12" for="matrialname">Company Type <span class="required">*</span></label>
										<select name="company_id[]" class="company_type form-control">
											<option value="">Select Company</option>
											<?php $this->db->where('company_id', $partyID); 
												$query = $this->db->get('drivers')->result();
											if(!empty($companyTypeResult)){ ?>
											<?php foreach($companyTypeResult as $companyTypeResults): ?>
												<option <?php /*echo $driverNames->id ."==". $priceListDetails->driver_id;*/if($companyTypeResults->id == $priceListDetails->company_id){echo "selected";} ?> value="<?php echo $companyTypeResults->id; ?>"><?php echo ucwords($companyTypeResults->company_type); ?></option>
											<?php endforeach; ?>	
											<?php }?>										
										</select>										
										
											
									</div>
									<div class="col-md-6 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Price</label>
										<input type="text" name="price[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Price" value="<?php if(!empty($priceListDetails)) echo $priceListDetails->price; ?>">
									</div>	
									
								

										<?php if($i==0){
												echo '<div class="col-sm-12 btn-row"><button class="btn btn-primary add_more_price_descr" type="button">Add</button></div>';
											}else{	
												echo '<button class="btn btn-danger remove_descr_field" type="button"> <i class="fa fa-minus"></i></button>';
											} ?>					
								</div>
					<?php $i++; } } }?>
					</div>
			</div>		
			</div>		
			</div>		
			</div>						


					<div class="form-group">
					  <button type="submit" class="btn btn-primary pricelist-sub-btn">Submit</button>
					</div>				  
					<!-- /.card-body -->


				  </form>
				  </div>
				  </div>
			</div>
		</div>		
	</section>
</div>