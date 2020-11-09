<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Product <?php if(!empty($this->uri->segment(3))){echo "#".$this->uri->segment(3);} ?></h3>

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
				
				<!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('inventory/edit_product/').$this->uri->segment(3) ?>">
					<div class="">
					  <div class="form-group">
						<label for="InputProductName">Product Name</label>
						<input type="text" name="product_name" class="form-control" id="InputProductName" placeholder="Product Name" value="<?php echo set_value('product_name', $product->product_name);?>">
					  </div>
					  <div class="form-group">
						<label for="InputPrice">Price</label>
						<input type="text" name="price" class="form-control" id="InputPrice" placeholder="Price" value="<?php echo set_value('price', $product->price);?>">
					  </div>
					  <!--div class="form-group">
						<label for="InputTax">Tax</label>
						<input type="text" name="tax" class="form-control" id="InputTax" placeholder="Tax" value="<?php //echo set_value('tax', $product->tax);?>">
					  </div-->
			<div class="invoice-edit-block margin-bottom-css">
				<div class="col-md-12 col-sm-12 col-xs-12 input_fields_wrap no-padding-left no-padding-right ">
					
					<div class="panel panel-default">

						<div class="panel-body goods_descr_wrapper">			
							<div class="item form-group add-ro" >
							<div class="col-md-12 input_descr_wrap label-box mobile-view2 no-padding-left no-padding-right">
								
								<div class="col-md-6 col-xs-12 item form-group">
									<label class="col-md-12 col-sm-12 col-xs-12" for="">Tax Type </label>
								</div>
								<div class="col-md-6 col-xs-12 item form-group">
									<label style="border-right: 1px solid #c1c1c1" class="col-md-12 col-sm-12 col-xs-12" for="">Amount</label>
								</div>	
							
							</div>
							<?php if(empty($product->tax)){ ?>
								<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box no-padding-left no-padding-right">

									<div class="col-md-6 col-sm-12 col-xs-12 form-group">	
									   <label class="col-md-12 col-sm-12 col-xs-12" for="tax_type">Tax Type</label>
										<input type="text" name="tax_type[]" required="required" class="form-control col-md-1" placeholder="Tax Type" value="">
									</div>														
													
									<div class="col-md-6 col-sm-12 col-xs-12 form-group ">	 
										<label class="col-md-12 col-sm-12 col-xs-12" for="tax_amount">Tax Amount<span class="required">*</span></label>
										<input id="inv_quantity" type="text" name="tax_amount[]" required="required" class="form-control col-md-1 year goods_descr_section keyup_event qty add_qty" placeholder="Tax Amount" >
									</div>
									
								</div>
                                <div class="col-sm-12 btn-row" style="margin-left:0px;"><button class="btn btn-primary add_description_detail_button_tax float-right" type="button">Add</button>
								</div>															
						<?php  } ?>
						<!--/div-->
						
						

				<?php //$partyID = $product->tax; ?>
				<?php if(!empty($product) && $product->tax != ''){
					$taxDetail = json_decode($product->tax);
					//print_r($taxDetail);
					if(!empty($taxDetail)){
						$i = 0;
						foreach($taxDetail as $taxDetails){
					?>

						<div class="col-md-12 input_descr_wrap middle-box mobile-view mailing-box no-padding-left no-padding-right   <?php if($i==0){ echo 'btn-3 ';}if($i==1){ echo 'scend-tr ';}else{ echo 'edit-row1 scend-tr';}?>">

							<div class="col-md-6 col-sm-12 col-xs-12 form-group">	
							   <label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Tax Type</label>
								<input type="text" name="tax_type[]" required="required" class="form-control col-md-1 year goods_descr_section " placeholder="Tax Type" value="<?php if(!empty($taxDetails)) echo $taxDetails->tax_type; ?>">
							</div>														
												
							<div class="col-md-6 col-sm-12 col-xs-12 form-group ">	 
								<label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Tax Amount<span class="required">*</span></label>
								<input  type="text" name="tax_amount[]" required="required" class="form-control col-md-1 year goods_descr_section keyup_event qty add_qty" placeholder="Tax Amount" value="<?php if(!empty($taxDetails)) echo $taxDetails->tax_amount; ?>">									
							</div>

							<?php if($i==0){
									echo '<div class="col-sm-12 btn-row"><button class="btn btn-primary add_description_detail_button_tax float-right" type="button">Add</button></div>';
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
          <div class="clearfix"></div>
					<div class="form-group">
					  <button type="submit" class="btn btn-primary">Save</button>
					</div>				  
					</div>
					<!-- /.card-body -->


				  </form>
				  </div>
				  </div>
			</div>
		</div>		
	</section>
</div>