//$('.daterange').daterangepicker();
$(document).ready(function () {
	$(".pprice").keyup(function(){
		var transid = $(this).data('transid');
		var rowid = $(this).data('rownum');
		var editedval = $(this).val();
		//alert("Work PPrice");
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
	
	$(".pname").keyup(function(){
		var transid = $(this).data('transid');
		var rowid = $(this).data('rownum');
		var editedval = $(this).val();
		//alert("Work PName");
		$.ajax({
			url: site_url + 'account/updateTransactionProductName/',
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
			var cid = $(this).data('cid');
			var cur = $('.currency').val();
			var acid = $('.companyname').val();
			var daterange = $('.daterange').val();
					if (cid === null){
						var cid = 'undefined';
					}
					if (cur === null){
						var cur = 'undefined';
					}					
					if(acid === null || acid == ""){
						acid = 'undefined';
					}
					if (daterange === ""){
						var daterange = 'undefined';
					}
			//console.log(acid);					
			$.ajax({
			url: site_url + 'account/exportTransactionByCompany/'+cid+'/'+acid+'/'+daterange+'/'+cur,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			beforeSend: function(){$('.export-msg').show();},
			data: {cid:cid, acid:acid, daterange:daterange, cur:cur},
			success: function (response) {
				//alert(cid);
			$('.export-msg').hide();
				if(response != 'notransaction'){					
					window.open("exportTransactionByCompany/"+ cid +"/" + acid+"/" + daterange+"/" + cur, '_blank');
				}
				if(response == 'notransaction'){
					alert("No transaction available for given dates");
					//return false;
				}				
			}
		});
	});
	
	$(document).on('click','.export-othersoft-xlsx',function(){	
			var cid = $(this).data('cid');
			var cur = $('.currency').val();
			var acid = $('.companyname').val();
			var daterange = $('.daterange').val();
					if (cid === null){
						var cid = 'undefined';
					}
					if (cur === null){
						var cur = 'undefined';
					}					
					if(acid === null || acid == ""){
						acid = 'undefined';
					}
					if (daterange === ""){
						var daterange = 'undefined';
					}
			//console.log(acid);					
			$.ajax({
			url: site_url + 'account/exportTransactionsOtherSoftware/'+cid+'/'+acid+'/'+daterange+'/'+cur,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			beforeSend: function(){$('.export-msg').show();},
			data: {cid:cid, acid:acid, daterange:daterange, cur:cur},
			success: function (response) {
				//alert(cid);
			$('.export-msg').hide();
				if(response != 'notransaction'){					
					window.open("exportTransactionsOtherSoftware/"+ cid +"/" + acid+"/" + daterange+"/" + cur, '_blank');
				}
				if(response == 'notransaction'){
					alert("No transaction available for given dates");
					//return false;
				}				
			}
		});
	});

	$(document).on('click','.export-us-by-retail',function(){	
			var cid = $(this).data('cid');
			var cur = $('.currency').val();
			var acid = $('.companyname').val();
			var daterange = $('.daterange').val();
					if (cid === null){
						var cid = 'undefined';
					}
					if (cur === null){
						var cur = 'undefined';
					}					
					if(acid === null || acid == ""){
						acid = 'undefined';
					}
					if (daterange === ""){
						var daterange = 'undefined';
					}
			//console.log(acid);					
			$.ajax({
			url: site_url + 'account/exportTransactionsByUSretail/'+cid+'/'+acid+'/'+daterange+'/'+cur,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			beforeSend: function(){$('.export-msg').show();},
			data: {cid:cid, acid:acid, daterange:daterange, cur:cur},
			success: function (response) {
				//alert(cid);
			$('.export-msg').hide();
				if(response != 'notransaction'){					
					window.open("exportTransactionsByUSretail/"+ cid +"/" + acid+"/" + daterange+"/" + cur, '_blank');
				}
				if(response == 'notransaction'){
					alert("No transaction available for given dates");
					//return false;
				}				
			}
		});
	});	
	
	$(document).on('click','.export-xlsx-transplus',function(){	
			var cid = $(this).data('cid');
			var cur = $('.currency').val();
			var acid = $('.companyname').val();
			var daterange = $('.daterange').val();
					if (cid === null){
						var cid = 'undefined';
					}
					if (cur === null){
						var cur = 'undefined';
					}					
					if(acid === null){
						acid = 'undefined';
						//cid = 'undefined';
					}
					if (daterange === ""){
						var daterange = 'undefined';
					}			
			$.ajax({
			url: site_url + 'account/exportTransactionByCompanyTransPlus/'+cid+'/'+acid+'/'+daterange+'/'+cur,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			beforeSend: function(){$('.export-msg').show();},
			data: {cid:cid, acid:acid, daterange:daterange, cur:cur},
			success: function (response) {
				$('.export-msg').hide();
				if(response != 'notransaction'){					
					window.open("exportTransactionByCompanyTransPlus/"+ cid +"/" + acid+"/" + daterange+"/" + cur, '_blank');
				}
				if(response == 'notransaction'){
					alert("No transaction available for given dates");
					//return false;
				}				
			}
		});
	});

	$(document).on('click','.export-xlsx-othersoft',function(){	
			var cid = $(this).data('cid');
			var cur = $('.currency').val();
			var acid = $('.companyname').val();
			var daterange = $('.daterange').val();
					if (cid === null){
						var cid = 'undefined';
					}
					if (cur === null){
						var cur = 'undefined';
					}					
					if(acid === null){
						acid = 'undefined';
						//cid = 'undefined';
					}
					if (daterange === ""){
						var daterange = 'undefined';
					}			
			$.ajax({
			url: site_url + 'account/exportTransactionByOtherSoftwares/'+cid+'/'+acid+'/'+daterange+'/'+cur,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			beforeSend: function(){$('.export-msg').show();},
			data: {cid:cid, acid:acid, daterange:daterange, cur:cur},
			success: function (response) {
				$('.export-msg').hide();
				if(response != 'notransaction'){					
					window.open("exportTransactionByOtherSoftwares/"+ cid +"/" + acid+"/" + daterange+"/" + cur, '_blank');
				}
				if(response == 'notransaction'){
					alert("No transaction available for given dates");
					//return false;
				}				
			}
		});
	});

	$(document).on('click','.export-invoices-xlsx',function(){	
			var daterange = $('.daterange').val();
			if (daterange === ""){
				var daterange = 'undefined';
			}			
			$.ajax({
			url: site_url + 'account/exportInvoicesXlsx/'+daterange,
			type: 'post',
			beforeSend: function(){$('.export-msg').show();},
			data: {daterange:daterange},
			success: function (response) {
				$('.export-msg').hide();
				if(response != 'noinvoice'){					
					window.open("exportInvoicesXlsx/"+ daterange, '_blank');
				}
				if(response == 'noinvoice'){
					alert("No invoice available for given dates");
					//return false;
				}				
			}
		});
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

	$(document).on('click','.export-single-xlsx',function(){       	
			var cardnum = $(this).data('cardnum');

			var daterange = $('.daterange').val();
					if (cardnum === null){
						var cardnum = 'undefined';
					}
					if (daterange === ""){
						var daterange = 'undefined';
					}			
			$.ajax({
			url: site_url + 'account/exportTransactionBySingleCard/'+cardnum+'/'+daterange,
			type: 'post',
			//escape : 'false',
			//contentType: false,
			//processData: false,
			beforeSend: function(){/*alert(cur);*/},
			data: {cardnum:cardnum, daterange:daterange},
			success: function (response) {
				//alert(cid);
				if(response != 'notransaction'){					
					window.open(site_url+"account/exportTransactionBySingleCard/"+ cardnum +'/'+ daterange, '_blank');
				}
				if(response == 'notransaction'){
					alert("No transaction available for given dates");
					//return false;
				}				
			}
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
  

	
// Add click event handler to button
	$( '#transactionFile' ).change( function () {
		
    var file_data = $('#transactionFile').prop('files')[0];   
    var form_data = new FormData();                  
    form_data.append('file', file_data);
	//console.log(form_data);
	//var ith = $('input[type=file]').val();
	//console.log('clicked');
		$.ajax({
			url: site_url + 'account/makeFile/',
			type: 'post',
			dataType : 'json',
			//dataType : 'text',
			//contentType: 'text/csv',
			data: form_data,
			cache: false,
			contentType: false,
			processData: false,			
			success: function (response) {
				console.log(response.type);
				//alert(response.result);
				//console.log(response);
				//$('#editor').html(  );
				//$('#editor').load();
				$('#editor').html( response.result );
				if(response.errorCount < 1){
					$('.husky-imp-btn').removeAttr('disabled');
				}
				//form_data.reset();
				//$('input[type=file]').reset();
			},
		});
 	
});


	// remove data
	$( '#transactionFile' ).click( function () {
		$('input[type=file]').val('');
		$('#editor').html('');	
	});
	
$('select').select2({
    minimumResultsForSearch: -1,
    placeholder: function(){
        $(this).data('placeholder');
    }
});

$("#search-company").select2();

});
