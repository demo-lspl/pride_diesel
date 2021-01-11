<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Pride Diesel</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />
	<?php foreach($css as $styles){ ?>
		<link rel="stylesheet" href="<?php echo base_url() . $styles; ?>" type="text/css" /><?php
	} ?>
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <script type="text/javascript" src="//cdn.jsdelivr.net/jquery/1/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>

    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" /> 
</head>
<body class="hold-transition sidebar-mini">

<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
     
    </ul>
		  <?php $userSessDetails = $this->session->userdata('userdata'); ?>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
		<?php $getMoneyCode = $this->db->select('moneyCode')->where('companyId', $userSessDetails->id)->order_by('id', 'DESC')->get('money_codes')->row(); ?>
		<h4 class="money-code"><?php echo (!empty($getMoneyCode->moneyCode))? "Money Code(".$getMoneyCode->moneyCode.")":''; ?></h4>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
		 <span class="badge badge-warning "><?php //echo $_SESSION['name']; ?></span>
         <i class="fa fa-caret-down" aria-hidden="true"></i>
         
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
         <div class="dropdown-divider"></div>
		 <div class="loggedin-user"><i class="fas fa-user"></i> Hello, <?= ucwords($userSessDetails->company_name)?></div>
          <a href="<?php echo base_url();?>auth/logout" class="dropdown-item">
            <i class="fas fa-sign-out-alt"></i> Sign Out
           
			 <!--a href="<?php //echo base_url();?>auth/logout"><span class="float-right text-muted text-sm">Sign Out</span></a-->
          </a>
        
        </div>
      </li>
	 
      <!--li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li-->
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="javascript:void()" class="brand-link">
      <img src="<?php echo base_url(); ?>assets/images/logo.png"
           alt="AdminLTE Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: 1">
      <span class="brand-text font-weight-light">Pride Diesel</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <!--div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php //echo base_url(); ?>assets/images/testimage.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div-->

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview">
            <a href="<?php echo base_url('dashboard')?>" class="nav-link <?php if($this->uri->segment(1) == 'dashboard'){echo 'active';} ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
           
          </li>

		<?php if($userSessDetails->role == 'admin'){?>
          <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'index' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'company_index' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'edit' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'company_edit'){echo 'menu-open';} ?>">
            <a href="#" class="nav-link ">
              <i class="nav-icon far fa-user"></i>
              <p>
                Company Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('user/index') ?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'index' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'edit'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Companies</p>
                </a>
              </li>			
              <li class="nav-item">
                <a href="<?php echo base_url('user/company_index') ?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'company_index' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'company_edit'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Company Type</p>
                </a>
              </li>
              <!--li class="nav-item">
                <a href="<?php echo base_url('user/daily_pricing') ?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'pricelist_index' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'daily_pricing'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Daily Pricing US</p>
                </a>
              </li>			  
              <li class="nav-item">
                <a href="<?php echo base_url('user/import_pricelist') ?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'pricelist_index' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'import_pricelist'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Import Price List</p>
                </a>
              </li-->			  
             
            </ul>
          </li>
		  
          <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'edit_pricelist_ca' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'edit_pricelist' || $this->uri->segment(2) == 'import_pricelist_ca' || $this->uri->segment(2) == 'import_pricelist' || $this->uri->segment(2) == 'import_pricelist_ca_husky'){echo 'menu-open';} ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-money-bill-alt"></i>
              <p>
                Daily Pricing
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('user/edit_pricelist_ca/1') ?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'pricelist_index' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'edit_pricelist_ca' || $this->uri->segment(2) == 'import_pricelist_ca' || $this->uri->segment(2) == 'import_pricelist_ca_husky'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Set Price CA</p>
                </a>
              </li>			  
              <li class="nav-item">
                <a href="<?php echo base_url('user/edit_pricelist/1') ?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'pricelist_index' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'edit_pricelist' || $this->uri->segment(2) == 'import_pricelist'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Set Price List US</p>
                </a>
              </li>				  
			</ul>
		  </li>		  

          <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'card'){echo 'menu-open';} ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa-credit-card"></i>
              <p>
                Card Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('card/index') ?>" class="nav-link <?php if($this->uri->segment(1) == 'card' && $this->uri->segment(2) == 'index' || $this->uri->segment(1) == 'card' && $this->uri->segment(2) == 'edit'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cards </p>
                </a>
              </li>

              <!--li class="nav-item">
                <a href="<?php echo base_url('card/import') ?>" class="nav-link <?php if($this->uri->segment(1) == 'card' && $this->uri->segment(2) == 'import'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Import Card</p>
                </a>
              </li-->
		  
			</ul>
		  </li>
		  <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'money_code_issued' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'money_codes' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'moneyCodeInvoice' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'moneyCodeTrans' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'money_code_issue' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'moneyCodeInvoices'){echo 'menu-open';} ?>">
			<a href="#" class="nav-link ">
			  <i class="nav-icon fas fa fa-wallet"></i>
			  <p>Issue Money Code
			  <i class="fas fa-angle-left right"></i>
			  </p>
			  
			</a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('user/money_code_issued/')?>" class="nav-link <?php if($this->uri->segment(2) == 'money_code_issued' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'money_code_issue' && empty($this->uri->segment(4))){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Generate Money Code</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo base_url('user/money_codes/')?>" class="nav-link <?php if($this->uri->segment(2) == 'money_codes' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'money_code_issue' && !empty($this->uri->segment(4))){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Old Money Codes</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url('user/moneyCodeInvoice/')?>" class="nav-link <?php if($this->uri->segment(2) == 'moneyCodeInvoice' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'moneyCodeTrans'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Generate Invoice</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url('user/moneyCodeInvoices/')?>" class="nav-link <?php if($this->uri->segment(2) == 'moneyCodeInvoices' ){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Invoices</p>
                </a>
              </li>				  
			</ul>		   
		  </li>
		  
		<?php } ?>
		  <?php if($userSessDetails->role == 'company' || $userSessDetails->role == 'admin'){?>
          <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'driver'){echo 'menu-open';} ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa fa-truck"></i>
              <p>
                Driver Management
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('driver/index') ?>" class="nav-link <?php if($this->uri->segment(1) == 'driver' && $this->uri->segment(2) == 'index' ){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View Drivers </p>
                </a>
              </li>			
              <li class="nav-item">
                <a href="<?php echo base_url('driver/edit') ?>" class="nav-link <?php if($this->uri->segment(1) == 'driver' && $this->uri->segment(2) == 'edit' && $this->uri->segment(3) == null){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Driver </p>
                </a>
              </li>				  
			</ul>
		  </li>			  
		  <?php } ?>	  

		  <?php if($userSessDetails->role == 'admin'){?>
          <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'gas_station'){echo 'menu-open';} ?>">
            <a href="<?php echo base_url('services')?>" class="nav-link">
              <i class="nav-icon fas fa fa-gas-pump"></i>
              <p>
                Gas Station Manage
				<i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('gas_station/index') ?>" class="nav-link <?php if($this->uri->segment(1) == 'gas_station' && $this->uri->segment(2) == 'index'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Gas Stations</p>
                </a>
              </li>			
              <li class="nav-item">
                <a href="<?php echo base_url('gas_station/edit') ?>" class="nav-link <?php if($this->uri->segment(1) == 'gas_station' && $this->uri->segment(2) == 'edit'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add Gas Station</p>
                </a>
              </li>
			</ul>	
          </li>		  
		  
		  <!--	Accounts Menu Start	-->
		  <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'ledgers' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'invoice' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'tax' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'transactions'){echo 'menu-open';} ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon far fa fa-paper-plane"></i>
              <p>
                Accounts
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('account/ledgers') ?>" class="nav-link <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'ledgers' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'ledger_edit' && $this->uri->segment(1) == 'account' || $this->uri->segment(2) == 'transaction_view_by_cid' && $this->uri->segment(1) == 'account' || $this->uri->segment(2) == 'invoice_view'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Ledger</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url('account/invoice') ?>" class="nav-link <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'invoice' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'invoice_view'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Invoices</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url('account/tax') ?>" class="nav-link <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'tax' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'edit_tax'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tax Structure</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url('account/transactions') ?>" class="nav-link <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'transactions' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'card_transactions' ){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Transactions</p>
                </a>
              </li>			  
			  
			</ul>
		  </li>

		  <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'inventory'){echo 'menu-open';} ?>">
            <a href="<?php echo base_url('inventory/index') ?>" class="nav-link">
              <i class="nav-icon far fa fa-file"></i>
              <p>
                Inventory
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('inventory/products') ?>" class="nav-link <?php if($this->uri->segment(1) == 'inventory' && $this->uri->segment(2) == 'products'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Products</p>
                </a>
              </li>
			</ul>	
		  </li>
		  <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'sales_person' && $this->uri->segment(2) == 'index'){echo 'menu-open';} ?>">
            <a href="<?php echo base_url('sales_person/index') ?>" class="nav-link">
              <i class="nav-icon far fa fa-user-tie"></i>
              <p>
                Sales Person
              </p>
            </a>	
		  </li>
		  <li class="nav-item ">
            <a href="<?php echo base_url('agents/index') ?>" class="nav-link <?php if($this->uri->segment(1) == 'agents' && $this->uri->segment(2) == 'index' || $this->uri->segment(1) == 'agents' && $this->uri->segment(2) == 'edit'){echo 'active';} ?>">
              <i class="nav-icon far fa fa-user"></i>
              <p>
                Create User
              </p>
            </a>	
		  </li>		  
		  <?php } ?>

		<?php	if($userSessDetails->role == 'company'){?>
          <li class="nav-item has-treeview">
            <a href="<?php echo base_url('card/get_my_card/')?>" class="nav-link <?php if($this->uri->segment(1) == 'card'){echo 'active';} ?>">
              <i class="nav-icon fas fa fa-credit-card"></i>
              <p>
                My Cards
              </p>
            </a>
           
          </li>
		  <?php
			$getUserPricing = $this->db->select('usa_pricing, cad_pricing, cad_pricing_husky, allowMoneyCode')->where('id', $userSessDetails->id)->get('users')->row();
			if($getUserPricing->allowMoneyCode == 1){	
		  ?>
		  <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'issueCode' || $this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'issuedCodes'){echo 'menu-open';} ?>">
			<a href="#" class="nav-link ">
			  <i class="nav-icon fas fa fa-wallet"></i>
			  <p>Issue Money Code
			  <i class="fas fa-angle-left right"></i>
			  </p>
			  
			</a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo base_url('user/issueCode/')?>" class="nav-link <?php if($this->uri->segment(2) == 'issueCode'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Generate Money Code</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo base_url('user/issuedCodes/')?>" class="nav-link <?php if($this->uri->segment(2) == 'issuedCodes'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Old Money Codes</p>
                </a>
              </li>
		  
			</ul>		   
		  </li>		  
		  <?php
		}
			if($getUserPricing->cad_pricing != 'no'){
		  ?>
		  <li class="nav-item has-treeview">
            <a href="<?php echo base_url('user/company_CApricelist_view/')?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'company_CApricelist_view'){echo 'active';} ?>">
              <i class="nav-icon fas fa fa-list"></i>
              <p>
                Price List Canada
              </p>
            </a>
           
          </li>	
		<?php } if($getUserPricing->usa_pricing != 'no'){?>	
		  <li class="nav-item has-treeview">
            <a href="<?php echo base_url('user/company_pricelist_view/')?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'company_pricelist_view'){echo 'active';} ?>">
              <i class="nav-icon fas fa fa-list"></i>
              <p>
                Price List US
              </p>
            </a>
           
          </li>
		<?php } ?>
		<?php  if($getUserPricing->cad_pricing_husky == 'add_on_husky'){?>	
		  <li class="nav-item has-treeview">
            <a href="<?php echo base_url('user/company_husky_pricelist_view/')?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'company_husky_pricelist_view'){echo 'active';} ?>">
              <i class="nav-icon fas fa fa-list"></i>
              <p>
                Price List Husky
              </p>
            </a>
           
          </li>
		<?php } ?>		
          <li class="nav-item has-treeview">
            <a href="<?php echo base_url('account/company_transactions')?>" class="nav-link <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'company_transactions'){echo 'active';} ?>">
              <i class="nav-icon fas fa fa-file-excel"></i>
              <p>
                Transactions
              </p>
            </a>
           
          </li>
          <li class="nav-item has-treeview">
            <a href="<?php echo base_url('account/invoice_pdf')?>" class="nav-link <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'invoice_pdf'){echo 'active';} ?>">
              <i class="nav-icon fas fa fa-file"></i>
              <p>
                Invoices
              </p>
            </a>
           
          </li>		  
          <li class="nav-item has-treeview">
            <a href="<?php echo base_url('user/edit_profile/').$userSessDetails->id?>" class="nav-link <?php if($this->uri->segment(1) == 'user' && $this->uri->segment(2) == 'edit_profile'){echo 'active';} ?>">
              <i class="nav-icon fas fa fa-user"></i>
              <p>
                Profile
              </p>
            </a>
           
          </li>			  
		  <?php }?>
		  <?php if($userSessDetails->role == 'admin'){ ?>
		  <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'cad_rebate_calc' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'usa_rebate_calc' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'husky_rebate'){echo 'menu-open';} ?>">
			<a href="#" class="nav-link ">
			  <i class="nav-icon fas fa fa-wallet"></i>
			  <p>Reporting
			  <i class="fas fa-angle-left right"></i>
			  </p>
			  
			</a>
            <ul class="nav nav-treeview">

              <li class="nav-item">
                <a href="<?php echo base_url('account/cad_rebate_calc/')?>" class="nav-link <?php if($this->uri->segment(2) == 'cad_rebate_calc'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>CAD Rebate EFS</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url('account/usa_rebate_calc')?>" class="nav-link <?php if($this->uri->segment(2) == 'usa_rebate_calc'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>USA Rebate EFS</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url('account/husky_rebate')?>" class="nav-link <?php if($this->uri->segment(2) == 'husky_rebate'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Husky Rebate</p>
                </a>
              </li>	
			  <li class="nav-item">
                <a href="<?php echo base_url('account/sale_comission')?>" class="nav-link <?php if($this->uri->segment(2) == 'sale_comission'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Sale Person Commission</p>
                </a>
              </li>		
		  
			</ul>		   
		  </li>	

		  <li class="nav-item ">
            <a href="<?php echo base_url('settings/index') ?>" class="nav-link <?php if($this->uri->segment(1) == 'settings' && $this->uri->segment(2) == 'index'){echo 'active';} ?>">
              <i class="nav-icon far fa fa-cog"></i>
              <p>
                Third Party Settings
              </p>
            </a>	
		  </li>		  
<?php } ?>		  

 <?php if($userSessDetails->role == 'sales'){ ?>
		  <li class="nav-item has-treeview <?php if($this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'company_commission' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'usa_rebate_calc' || $this->uri->segment(1) == 'account' && $this->uri->segment(2) == 'husky_rebate'){echo 'menu-open';} ?>">
			<a href="#" class="nav-link ">
			  <i class="nav-icon fas fa fa-wallet"></i>
			  <p>Commission
			  <i class="fas fa-angle-left right"></i>
			  </p>
			  
			</a>
            <ul class="nav nav-treeview">

              <li class="nav-item">
                <a href="<?php echo base_url('account/company_commission/')?>" class="nav-link <?php if($this->uri->segment(2) == 'company_commission'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Company</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo base_url('account/get_com_cards')?>" class="nav-link <?php if($this->uri->segment(2) == 'get_com_cards'){echo 'active';} ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Cards</p>
                </a>
              </li>
			</ul>		   
		  </li>	
<?php } ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
 
  <!-- /.content-wrapper -->
<div class="content-wrapper"> 
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?php if(!empty($title)){echo $title;}?></h1>
          </div>
          <div class="col-sm-6">
            <!--ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">User Profile </li>
            </ol-->
			<?php		
				if(isset($breadcrumbs)){
					echo $breadcrumbs;
				}
				else{ ?>
					<div class="alert alert-error">
						<button class="close" data-dismiss="alert"></button>
						Warning: Breadcrumbs Not Defined in the Module
					</div><?php
				}	?>			
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
