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
					<?php if($this->session->flashdata("success_msg")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success_msg")?>
					</div>
					<?php } ?>
				</div>

				<!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('sales_person/edit/').$this->uri->segment(3) ?>">
					<div class="">					
					  <div class="form-group">
						<label for="InputName">Name</label>
						<input type="text" name="name" class="form-control" id="InputName" placeholder="Name" value="<?php echo set_value('name', $exceutive->name)?>">
						<div class="error-single"><?php echo form_error('name'); ?></div>
					  </div>
					<div class="form-group">
					  <button type="submit" class="btn btn-primary">Submit</button>
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