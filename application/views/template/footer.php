 </div>
 <footer class="main-footer" style="clear:both;">
    <div class="float-right d-none d-sm-block">
     
    </div>
	<!--.$_SERVER['HTTP_HOST'] -->
    <strong><?php echo "&copy; ". date('Y');?> <a href="javascript:void();">Pride Diesel </a>.</strong> All rights
    reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<script>		var site_url = '<?php echo base_url(); ?>';
					
	</script>		<?php
			$i = 0;
			foreach($js as $scripts){ 			if($i == 0){ 			}else{} 		?>
		<script src="<?php echo base_url() . $scripts; ?>"></script>
			<?php $i++;
		} ?>

</body>
</html>
