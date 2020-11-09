  $(function () {
    $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
	  "order": [[ 0, "desc" ]]
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
	
	$('.select1').select2();	
	$('.select2').select2();	
	$('.selectledgergroup').select2();	
  });
//$(document).ready(function(){
	$('#exportBlankCardExcel').click(function(){
		window.location = $('#exportBlankCardExcel').attr('href');
		 //alert( "Handler for .click() called." );
	});	
	
	$('#expo').click(function(){
		window.location = $('#exportCards').attr('href');
		 //alert( "Handler for .click() called." );
	});
