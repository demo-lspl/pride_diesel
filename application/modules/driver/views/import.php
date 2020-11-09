<!-- Main Container -->
<div class="">

	<section class="content box">
	<?php if($this->session->flashdata("success_msg")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success_msg")?>
	</div>
	<?php } ?>
			<div class="row justify-content-center">
				<div class="col-md-6">
					<div class="form-center">
					<p class="text-center">Choose file to import</p>
					<form class="text-center" method="post" action="<?php echo base_url('card/importCard')?>" enctype="multipart/form-data">
						<input class="" type="file" name="uploadFile" />
						<input type="submit" class="btn btn-primary" value="Import Cards" />
					</form>
					</div>
				</div>
			</div>
	</section>
</div>
  
  
  <script type="text/javascript">

 
  </script>  