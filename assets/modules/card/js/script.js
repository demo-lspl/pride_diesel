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
  
	$(document).on('click','.export-xlsx',function(){	
			var cid = $(this).data('cid');
			if (cid === null){
				var cid = 'undefined';
			}			
			$.ajax({
			url: site_url + 'card/exportCardsByCompany/'+cid,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			beforeSend: function(){/*alert(cur);*/},
			data: {cid:cid},
			success: function (response) {
				//alert(response);
				if(response == 'nocards'){
					alert("No record found.");
					//return false;
				}else{
					window.open(site_url+"card/exportCardsByCompany/"+ cid , '_blank');
				}				
			}
		});	
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
