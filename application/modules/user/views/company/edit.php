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
					<?php //echo validation_errors(); ?>
					<?php if($this->session->flashdata("success")){?>
					<div class="alert alert-success">      
						<?php echo $this->session->flashdata("success")?>
					</div>
					<?php } ?>
				</div>
				
				<?php //print_r($company);//foreach($userdata as $userdatas){$values = $userdatas;}// ?>
				  <!-- form start -->
				<form role="form" method="post" action="<?php echo base_url('user/company_edit/').$this->uri->segment(3) ?>">
					<div class="">
					  <div class="form-group">
						<label for="InputCompanyType">Company Type</label>
						<input type="text" name="company_type" class="form-control" id="InputCompanyType" placeholder="Company Type" value="<?php echo set_value('company_type', $company->company_type)?>" <?php if($company->company_type == 'Bronze'){echo "readonly";}?>>
						<div class="error-single"><?php echo form_error('company_type'); ?></div>
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