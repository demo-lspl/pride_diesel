<!-- Main Container -->
<div class="">

<section class="content box">
	<div class="card ">

		<div class="card-header">
		<h3 class="card-title">You can import your PRICE LIST EXCEL in (xls|xlsx|csv) format only.</h3> &nbsp;<!--a href="<?php echo base_url('user/export_blank_pricelist_excel')?>"><i class="fa fa-download"></i> Download Blank Excel</a-->
		</div>		
		<div class="card-body text-center">
			<div class="row justify-content-center">
				<div class="col-sm-12">
					<div class="form-messages error">
						<?php echo validation_errors(); ?>
						<?php if($this->session->flashdata("success")){?>
						<div class="alert alert-success">      
							<?php echo $this->session->flashdata("success")?>
						</div>
						<?php } ?>
						<?php if($this->session->flashdata("error")){?>
						<div class="alert alert-error">      
							<?php echo $this->session->flashdata("error")?>
						</div>
						<?php } ?>						
					</div>
					<p class="text-center">Choose file to import</p>
					<form role="form" method="post" action="<?php echo base_url('user/import_pricelist_ca_husky/') ?>" enctype="multipart/form-data">
						<div class="form-group">
							<input type="file" name="import_pricelist" />
							<button type="submit" name="import" class="btn btn-primary pricelist-sub-btn">Import</button>
						</div>			  
					</form>
				  </div>
			  </div>
		</div>
	</div>		
</section>
</div>