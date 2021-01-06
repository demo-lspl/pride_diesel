<!-- Main Container -->
<div class="">

	<section class="content box">
	<div class="">
	<?php if($this->session->flashdata("success")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success")?>
	</div>
	<?php } ?>
			<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">EFS/Husky Credentials</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body"><?php //print_r($efs);	?>
				<div class="row">
					<div class="col-md-12 col-sm-12">
					<div class="row">
						<div class="col-md-6 col-sm-6">
							<?php
							if(!empty($efs['id'])){$id = $efs['id'];}else{$id = null;}
							?>
							<form action="<?php echo base_url('settings/edit/').$id; ?>" method="post" >
								<fieldset class="scheduler-border">
								 <legend class="scheduler-border">EFS Credentials</legend>
								 <input type="hidden" name="company" value="efs" />
								<div class="form-group">
									<input class="form-control" type="text" name="efs_username" placeholder="User Name" value="<?= (empty($efs['username']))?$efsNew->efs_username:$efs['username']; ?>" autocomplete="off" />
								</div>
								<div class="form-group">
									<input class="form-control" type="text" name="efs_password" placeholder="Password" value="<?= (empty($efs['password']))?$efsNew->efs_username:$efs['password']; ?>" autocomplete="off" />
								</div>
								<input type="submit" name="efs" class="btn btn-primary" />
								</fieldset>	
							</form>
						</div>
						<div class="col-md-6 col-sm-6">
							<?php
							if(!empty($husky['id'])){$id = $husky['id'];}else{$id = null;}
							?>
							<form action="<?php echo base_url('settings/edit/').$id; ?>" method="post" >
								<fieldset class="scheduler-border">
								 <legend class="scheduler-border">HUSKY Credentials</legend>
								 <input type="hidden" name="company" value="husky" />
								<div class="form-group">
									<input class="form-control" type="text" name="husky_username" placeholder="User Name" value="<?= (empty($husky['username']))?$huskyNew->husky_username:$husky['username']; ?>" autocomplete="off" />
								</div>
								<div class="form-group">
									<input class="form-control" type="text" name="husky_password" placeholder="Password" value="<?= (empty($husky['password']))?$huskyNew->husky_password:$husky['password']; ?>" autocomplete="off" />
								</div>
								<input type="submit" name="efs" class="btn btn-primary" />
								</fieldset>	
							</form>
						</div>	
                       </div>						
					</div>				
				</div>
			</div>
			</div>
		</div>		
	</section>
</div> 