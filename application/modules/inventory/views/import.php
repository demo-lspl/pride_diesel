<!-- Main Container -->
<div class="">

	<section class="content box">
	<?php if($this->session->flashdata("success_msg")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success_msg")?>
	</div>
	<?php } ?>
	
	<div class="card">
		<div class="card-header">
		<h3 class="card-title">You can import your CARDS EXCEL in (csv|xls|xlsx) format only.</h3> &nbsp;<a href="<?php echo base_url('card/exportBlankCardsExcel')?>"><i class="fa fa-download"></i> Download Blank Excel</a>
		</div>
		<div class="card-body text-center">			  
			<p class="text-center">Choose file to import</p>
			<form class="text-center" method="post" action="<?php echo base_url('card/importCard')?>" enctype="multipart/form-data">
				<input class="" type="file" name="uploadFile" />
				<input type="submit" class="btn btn-primary" value="Import Cards" />
			</form>
		</div>
	</div>
	</section>
</div>
  
  
  <script type="text/javascript">

 
  </script>  