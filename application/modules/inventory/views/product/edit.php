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
					  <!--div class="form-group">
						<label for="InputPrice">Price</label>
						<input type="text" name="price" class="form-control" id="InputPrice" placeholder="Price" value="<?php //echo set_value('price', $product->price);?>">
					  </div-->

					  <div class="form-group">
						<label>Taxes Aplicable</label><br />
						<?php isset($product->tax)?$taxes = json_decode($product->tax):''; ?>
						<label for="InputLunch" class="checkbox-inline"><input type="checkbox" name="taxes[]" class="" id="InputLunch" <?php if(!empty($taxes) && in_array('pst', $taxes) == true):echo 'checked';endif;?> value="pst"> PST</label>
						<label for="InputLunch" class="checkbox-inline"><input type="checkbox" name="taxes[]" class="" id="InputLunch" <?php if(!empty($taxes) && in_array('gst', $taxes) == true):echo 'checked';endif;?> value="gst"> GST</label>
						<label for="InputLunch" class="checkbox-inline"><input type="checkbox" name="taxes[]" class="" id="InputLunch" <?php if(!empty($taxes) && in_array('qst', $taxes) == true):echo 'checked';endif;?> value="qst"> QST</label>						
					  </div>					  
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