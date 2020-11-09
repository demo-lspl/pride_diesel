//$(".federaltax-checkbox").
  /* $( '.federaltax-checkbox' ).on( 'click', ':checkbox', function( e ) { // attach click event
      //$( '.form-group' ).find(':text').prop( 'disabled', $(e.currentTarget).is(':checked') ); // set disabled prop equal to checked property of checkbox
	  alert("work");
  } ); */

  $('input[name="isfederaltax"]').change(function () {
    if (this.checked) {
        //alert("Thanks for checking me");
		$(".selectledgergroup").attr('disabled', true);
		//$("#federal_tax").removeAttr('disabled');
		//$("#tax_rate").attr('readonly', 'readonly');
		$("#federal_tax").removeAttr('readonly');
    }
    if (this.checked == false) {
        //alert("Thanks for checking me");
		$(".selectledgergroup").attr('disabled', false);
		$("#federal_tax").attr('readonly', 'readonly');
		//$("#tax_rate").removeAttr('readonly');
		
    }	
});

$('.tax-percent').click(function () {
	//alert("work");

	if($("#tax_rate").val() != ''){
		var oldval = $('#tax_rate').val();
		if($('#tax_rate').val().indexOf('%') == -1){
			$('#tax_rate').val(oldval+"%");
		}
	}else{
		$("#tax_rate").val('1%');
	}
	
});

$('.tax-value').click(function () {
	//alert("work");

	if($("#tax_rate").val() != ''){
		var oldval = $('#tax_rate').val();
		//if($('#tax_rate').val().indexOf('%') == -1){
			var ns = $('#tax_rate').val();
			ns = ns.replace(/([,.%])+/g, '');
			$('#tax_rate').val(ns);
		//}
	}else{
		$("#tax_rate").val('1');
	}
	
});

disablefieldsoncheck();

function disablefieldsoncheck(){
var checkstatus = $('input[name="isfederaltax"]').attr('checked');
//console.log(checkstatus);
    if (checkstatus == 'checked') {
        //alert("Thanks for checking me");
		$(".selectledgergroup").attr('disabled', true);
		//$("#federal_tax").removeAttr('disabled');
		//$("#tax_rate").attr('readonly', 'readonly');
		$("#federal_tax").removeAttr('readonly');
    }
    if (checkstatus == '') {
        //alert("Thanks for checking me");
		$(".selectledgergroup").attr('disabled', false);
		$("#federal_tax").attr('readonly', 'readonly');
		//$("#tax_rate").removeAttr('readonly');
		
    }
	}