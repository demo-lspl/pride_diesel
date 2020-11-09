<!-- Main Container -->
<div class="">

	<section class="content box">
		<div class="addnew-user">
			<!--a href="<?php echo base_url('user/create_users_pdf/') ?>" class="btn btn-success"><i class="fa fa-file"></i> Export PDF</a-->
			<!--a href="<?php echo base_url('user/exportUser') ?>" class="btn btn-info"><i class="fa fa-excel"></i> Eport User</a>
			<form method="post" action="<?php echo base_url('user/importUser')?>" enctype="multipart/form-data">
				<input type="file" name="uploadFile" />
				<input type="submit" value="Import" />
			</form-->
		</div>
	<div class="">
	<?php if($this->session->flashdata("success")){?>
	<div class="alert alert-success">      
		<?php echo $this->session->flashdata("success")?>
	</div>
	<?php } ?>
			<div class="card card-default">
			<div class="card-header bg-card-header">
				<h3 class="card-title">Non Invoiced</h3>

				<div class="card-tools">
				  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
				  <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
				</div>
			</div>
			<div class="card-body">			
                <table id="" class="table table-bordered table-striped table-hover">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th>Money Code</th>
                    <th>Amount</th>
                    <th>Reason</th>
                    <th>Created</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
				  <?php if(!empty($moneyCodeTrans) && count($moneyCodeTrans)>0){foreach($moneyCodeTrans as $moneyCodeTransRows): ?>
                  <tr>
                    <td><?= $moneyCodeTransRows->id?></td>
                    <td><?= $moneyCodeTransRows->moneyCode?></td>
                    <td><?= $moneyCodeTransRows->amount?></td>
                    <td><?= $moneyCodeTransRows->notes?></td>
                    <td><?= $moneyCodeTransRows->date_created?></td>
                    <td><a href="<?php echo base_url('user/moneyCodeTransDelete/').$moneyCodeTransRows->id ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a></td>
                  </tr>
				  <?php endforeach; }else{ ?>
				<tr>
					<td colspan="5">No record found</td>
				</tr>	
				  <?php } ?>	
                  </tbody>
                </table>
				<br />
				<?php if(!empty($moneyCodeTrans) && count($moneyCodeTrans)>0){ ?>
					<p class="text-center"><a href="<?php echo base_url().'user/generateMoneyInvoice/'.$this->uri->segment(3)?>" class="btn btn-primary">Generate Invoice</a></p>
				<?php } ?>
			</div>
			</div>
		</div>		
	</section>
</div> 