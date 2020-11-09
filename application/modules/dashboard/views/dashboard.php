<!-- Main Container -->
<div class="">

	<section class="content box">
		<div id="">
			<?php $userSessDetails = $this->session->userdata('userdata') ?>
			<!--h1>Welcome to Pride Diesel! <?php echo ucwords($userSessDetails->role); ?></h1-->
			
		</div>
		<?php if($userSessDetails->role == 'admin'){?>
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= (!empty($cardCount))?$cardCount:'0'; ?></h3>

                <p>Total Cards</p>
              </div>
              <div class="icon">
                <i class="fas fa-credit-card"></i>
              </div>
              <a href="<?php echo base_url('card/index') ?>" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= (!empty($gasStations))?$gasStations:'0'; ?><!--sup style="font-size: 20px">%</sup--></h3>

                <p>Gas Stations</p>
              </div>
              <div class="icon">
                <i class="fa fa-gas-pump"></i>
              </div>
              <a href="<?php echo base_url('gas_station') ?>" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= (!empty($userCount))?$userCount:'0'; ?></h3>

                <p>Company Registrations</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-plus"></i>
              </div>
              <a href="<?php echo base_url('user/index') ?>" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo (!empty($invoiceCount))?$invoiceCount:'0'; ?></h3>

                <p>Invoices</p>
              </div>
              <div class="icon">
                <i class="fas fa-file"></i>
              </div>
              <a href="<?php echo base_url('account/invoice') ?>" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
		<?php } ?>
		<?php if($userSessDetails->role == 'company'){?>
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small card -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= (!empty($cardCount))?$cardCount:'0'; ?></h3>

                <p>Total Cards</p>
              </div>
              <div class="icon">
                <i class="fas fa-credit-card"></i>
              </div>
              <a href="<?php echo base_url('card/get_my_card') ?>" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= (!empty($driverCount))?$driverCount:'0'; ?><!--sup style="font-size: 20px">%</sup--></h3>

                <p>Drivers</p>
              </div>
              <div class="icon">
                <i class="fa fa-truck"></i>
              </div>
              <a href="<?php echo base_url('driver/index') ?>" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= (!empty($invoiceCount))?$invoiceCount:'0'; ?></h3>

                <p>Invoices</p>
              </div>
              <div class="icon">
                <i class="fas fa-file"></i>
              </div>
              <a href="<?php echo base_url('account/invoice_pdf') ?>" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <!--div class="col-lg-3 col-6">
            
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>

                <p>Unique Visitors</p>
              </div>
              <div class="icon">
                <i class="fas fa-chart-pie"></i>
              </div>
              <a href="#" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div-->
          <!-- ./col -->
        </div>
        <!-- /.row -->
		<?php } ?>			
	</section>
</div>