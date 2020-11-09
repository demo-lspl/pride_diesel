//$('.daterange').daterangepicker();
$(document).ready(function () {
	$("input").keyup(function(){
		var transid = $(this).data('transid');
		var rowid = $(this).data('rownum');
		var editedval = $(this).val();
		$.ajax({
			url: site_url + 'account/updateTransactionPrice/',
			type: 'post',
			data: {transid:transid, rowid:rowid, editedval:editedval},
			success: function (response) {
				// Perform operation on the return value
				//alert(response);
			}
		});
	});
	
	/* Export Transactions EXCEL/CSV **/
	$(".export-csv").click(function(e){
		e.preventDefault();
		//alert("Avaals");
		var cid = $(this).data('cid');
		var acid = $('.select2').val();
		var daterange = $('.daterange').val();
		$.ajax({
			url: site_url + 'account/exportTransactionsByCompany/',
			type: 'post',
			//escape : 'false',
			//contentType: 'text/csv',
			data: {cid:cid, acid:acid, daterange:daterange},
			success: function (response) {
			//alert(response);
			if(response == "false"){
				alert("No transaction found.");
				return false;
			}
            var blob = new Blob([response], { type: 'application/vnd.ms-excel' });
            var downloadUrl = URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = downloadUrl;
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
			var yyyy = today.getFullYear();			
            a.download = "transactionsData"+dd+mm+yyyy+".csv";
            document.body.appendChild(a);
            a.click(); 
			}
		});
	});
	$(document).on('click','.export-xlsx',function(){       
			/* var first_input = $('#name_email_order_search').val();
			var date_range = $('#reportrangeorder').val();        
			var order_status = $('#order_status').val();  */  
			var cid = $(this).data('cid');
			var acid = $('.select2').val();
			var daterange = $('.daterange').val();
			$.ajax({
			url: site_url + 'account/exportTransactionByCompany/'+cid+'/'+acid+'/'+daterange,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			data: {cid:cid, acid:acid, daterange:daterange},
			success: function (response) {
				//alert(response);

				if(response != 'notransaction'){	
					if($('.select2').val() === ""){
						acid = 'undefined';
						cid = 'undefined';
					}
					window.open("exportTransactionByCompany/"+ cid +"/" + acid+"/" + daterange, '_blank');
				}
				if(response == 'notransaction'){
					alert("No transaction available for given dates");
					//return false;
				}				
			}
		});			
			/* if(cid == ""){
				cid = 'undefined';
			}
			if(acid == ""){
				acid = 'undefined';
			}
			if(daterange == ""){
				daterange = '';
			} */
		
			
	});	
	$(".export-xlsx1").click(function(e){
		//e.preventDefault();
		//alert("Avaals");
		var cid = $(this).data('cid');
		var acid = $('.select2').val();
		var daterange = $('.daterange').val();
		$.ajax({
			url: site_url + 'account/exportTransactionByCompany/'+cid,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			data: {cid:cid, acid:acid, daterange:daterange},
			success: function (response) {
			//alert(response);
			// if(response == "false"){
				// alert("No transaction found.");
				// return false;
			// }
            /* var blob = new Blob([response], { type: 'application/vnd.ms-excel' });
            //var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            var downloadUrl = URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = downloadUrl;
			var today = new Date();
			var dd = String(today.getDate()).padStart(2, '0');
			var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
			var yyyy = today.getFullYear();			
            a.download = "transactionsData"+dd+mm+yyyy+".xlsx";
            document.body.appendChild(a);
            a.click(); */
/*  var bytes = new Uint8Array(filedata.FileContents); 
 var blob = new Blob([bytes], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
 var link = document.createElement(‘a’);
 link.href = window.URL.createObjectURL(blob);
 link.download = "Report.xlsx";
 link.click(); */			
			
			}
		});
	});	
});	

document.addEventListener('DOMContentLoaded', function() {
   // your code here
   	var totalTrans = $(".tot-trans").val();
	if(totalTrans > 1){
		$(".showCount").html("Non Invoiced Transactions ("+totalTrans+")");
	}else{
		$(".showCount").html("Non Invoiced Transaction ("+totalTrans+")");
	}
}, false);

$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});