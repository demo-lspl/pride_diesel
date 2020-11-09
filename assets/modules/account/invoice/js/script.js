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

	/* $("#price_unit").keyup(function(){
		changeAmount();
		var qty = $("#inv_quantity").val();
		var amt = $(this).val();
		
		var total = parseFloat(qty) * parseFloat(amt).toFixed(2);
		var calculategst = (parseFloat(amt) * parseFloat(qty) * 10 / 100).toFixed(2);
		$(".sub-total").html(total);
		$(".gst-amount").html(calculategst);
		$(".final-total").html(parseFloat(total.toFixed(2)) + parseFloat(calculategst));		

	}); */
	
	/* $("#inv_quantity").keyup(function(){
		changeAmount();

	}); */	
	
	/* $('#example1').DataTable( {
        "order": [[ 1, "desc" ]]
    } ); */
	$(".party_name").change(function(){
	var partyid = $(".party_name").val();
	$.ajax({
        url: site_url+"account/company_address/"+partyid,
        type: "get",
        data: {value:partyid} ,
		dataType: 'json',
        success: function (response) {
			//console.log(response);
			$("input[name=party_address]").val(response[0].address);
			//$("input[name=driver_id]").val(response.address);
			
						var html = '';
                        var i;
                        for(i=0; i<response.length; i++){
                            html += '<option value='+response[i].id+'>'+response[i].name+'</option>';
                        }
                        $('#driver_id').html(html);			
			//console.log(response);
           // You will get response from your PHP page (what you echo or print)
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });
	});
	//var base_url = "<?php base_url('') ?>";
	/* $("#product_id").change(function(){
	var productID = $(this).val();
	//alert(site_url);
	$.ajax({
        url: site_url+"account/get_product_details/"+productID,
        type: "get",
        data: {productID:productID} ,
		dataType: 'json',
        success: function (response) {
			//console.log(response.price);
			$("#price_unit").val(response.price);
			$("input[name=fuel_taxes]").val(response.fuel_taxes);
		
			//console.log(response);
           // You will get response from your PHP page (what you echo or print)
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
		});
	}); */	
	
 
	/* function changeAmount(){
		var qty = $("#inv_quantity").val();
		var amt = $("#price_unit").val();
		
		var total = parseFloat(qty) * parseFloat(amt);
		var calculategst = (parseFloat(amt) * parseFloat(qty) * 13 / 100).toFixed(2);
		if(total > 0){
			$(".sub-total").html(total.toFixed(2));
			$("#sub_total").val(total.toFixed(2));
		}else{
			$(".sub-total").html('0.00');
			$("#sub_total").val('0.00');
		}		
		
		if(calculategst > 0){
			$(".gst-amount").html(calculategst);
			$("#gst").val(calculategst);
		}else{
			$(".gst-amount").html('0.00');
			$("#gst").val('0.00');
		}
		
		if(parseFloat(total.toFixed(2)) + parseFloat(calculategst) > 0){
		$("#final_total").val((parseFloat(total) + parseFloat(calculategst)).toFixed(2));
		$(".final-total").html((parseFloat(total) + parseFloat(calculategst)).toFixed(2));
		}else{
			$(".final-total").html('0.00');
		}
		
		//alert(total);	
	}
changeAmount(); */
add_filed_for_goods_descr_invoice();
function add_filed_for_goods_descr_invoice(){
		var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".add-ro"); //Fields wrapper
    var add_button      = $(".add_description_detail_button_invoice"); //Add button ID
    var x = 1; //initlal text box count
//$(document).on("click",".add_description_detail_button_invoice",function(){

    $(add_button).click(function(e){ //on add input button click
	    //e.preventDefault();
			/* var uomsArray = '';
				$.each( uoms, function( key, value ) {
				uomsArray = uomsArray+'<option value="'+value+'">'+value+'</option>';
			}); */
			$('.add_requried').prop("disabled", true);
			var company_login_id = $('#company_login_id').val();			
			var get_discount_on_off = $('#get_discount_on_off').val();
			var company_login_id = "2";
			
        if(x < max_fields){ //max input box allowed
            x++; 				

				$(wrapper).append('<div class="col-md-12 input_descr_wrap add-ro2 mailing-box mobile-view" style="margin-top:0px; "><div class="col-sm-2 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="driver_name">Driver Name <span class="required">*</span></label><select class="form-control select222" required="required" name="driver_id[]" data-id="products" data-key="id" data-fieldname="product_name" data-where="" width="100%" ><option value="0">Select Driver</option></select></div><div class="col-md-2 col-sm-12 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Unit</label><input type="text" name="unit[]" required="required" class="form-control col-md-1 year goods_descr_section   " placeholder="Unit" value="">	</div><div class="col-md-2 col-sm-12 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="hsn/sac">Product </label><select data-id="products" data-key="id" data-fieldname="product_name" name="product_id[]" required="required" class="form-control select2 select2_product get_product_val col-md-1 year goods_descr_section   " placeholder="Product" value=""><option value="0">Choose Product</option></select></div><div class="col-md-2 col-sm-12 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="quantity">QTY<span class="required">*</span></label><input type="text" name="quantity[]" required="required" class="form-control col-md-1 year goods_descr_section keyup_event qty add_qty" placeholder="Quantity" value=""></div><div class="col-md-2 col-sm-12 col-xs-12 input_descr_wrap form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="rate">Fuel Taxes<span class="required">*</span></label><input type="text" name="fuel_taxes[]" required="required" class="form-control col-md-1 year goods_descr_section   " placeholder="Fuel Taxes" value="" readonly /></div><div class="col-md-1 col-sm-12 col-xs-12 input_descr_wrap form-group"><div><label class="col-md-12 col-sm-12 col-xs-12" for="price_unit">Price Unit<span class="required">*</span></label><input type="text" name="price_unit[]" required="required" class="form-control col-md-1 year goods_descr_section   unitprice" placeholder="Price Unit" value=""></div></div><div class="col-md-1 col-sm-12 col-xs-12 input_descr_wrap form-group"><div><label class="col-md-12 col-sm-12 col-xs-12" for="price_unit">Price Unit<span class="required">*</span></label><input type="text" name="sub_total[]" required="required" class="form-control col-md-1 year goods_descr_section    subtotal" placeholder="Amount" value=""></div></div><button class="btn btn-danger remove_descr_field" type="button"><i class="fa fa-minus"></i></button></div>');
			
				/* get_multiselect_value();
				get_material_thrugh_item_code();
				kyup_function_to_remove_add_rate_qty();				
				tax_keyup_event_to_remove_tax();
				subtotal();
				get_add_more_btn_forsale_ledger();
				init_select21();
				init_select221();
				sale_ledger_id_onchange();
				party_name_ledger_id_onchange();
				add_charges_on_invoice();
				tax_calculation_for_charges();
				add_dicount_invoice_matrial(); */
			
        }
		kyup_function_to_remove_add_rate_qty();
		subtotal();
		get_multiselect_value();
		initailizeDriverSelect2();
		initailizeSelect2();
		
//$(".select2_product").select2();
    });
	
    $(".add-ro").on("click",".remove_descr_field", function(e){ //user click on remove text
        e.preventDefault();
		$(this).parent('div').remove(); x--;
		setTimeout(function(){
						$('.keyup_event').keyup();	
					}, 1000);
    });
}	
$(".select2").select2();	
	//$("#selUser").select2();
// Initialize Driver select2	 
function initailizeDriverSelect2(){
	var company_id = $('.party_name').val();
	$(".select222").select2({
         ajax: { 
           url: site_url+'driver/all_drivers/'+company_id,
           //type: "post",
           dataType: 'json',
           delay: 250,
		   //contentType: 'application/json; charset=utf-8',
           data: function (params) {
			   //console.log(params);
              return {
                searchTerm: params.term, // search term
				//page: params.page
                //table: $(this).attr("data-id"),
                //field: $(this).attr("data-key"),
                //fieldname: $(this).attr("data-fieldname"),
              };
           },
           processResults: function (response) {
			   //console.log(response);
			   //alert(JSON.stringify(response));
              return {
                 results: response
              };
			  			  
           },
           cache: true
         }/* ,
		  minimumInputLength: 1 */
     });
}	 
// Initialize Product select2
function initailizeSelect2(){
      $(".select2_product").select2({
         ajax: { 
           url: site_url+'account/get_all_products',
           //type: "post",
           dataType: 'json',
           delay: 250,
		   //contentType: 'application/json; charset=utf-8',
           data: function (params) {
			   //console.log(params);
              return {
                searchTerm: params.term, // search term
				//page: params.page
                //table: $(this).attr("data-id"),
                //field: $(this).attr("data-key"),
                fieldname: $(this).attr("data-fieldname"),
              };
           },
           processResults: function (response) {
			   //console.log(response);
			   //alert(JSON.stringify(response));
              return {
                 results: response
              };
			  			  
           },
           cache: true
         }/* ,
		  minimumInputLength: 1 */
     });	

}
get_multiselect_value();
kyup_function_to_remove_add_rate_qty();
subtotal();
//kyup_function_to_remove_add_rate_qty();
//Add charges Details 

	function get_multiselect_value(){ 
		$('.get_product_val').change(function(){		
		//console.log('this==>>',$(this).closest('.input_descr_wrap').find('.goods_descr_section').val('abc'));
		 var product_select_this =  $(this);
		
		  var selected_id = $(this).val();	
			console.log(selected_id);
				$.ajax({
				   type: "POST",
				   url: site_url+'inventory/product_by_id/'+selected_id,
				   //data: {id:selected_id},
				   beforeSend: function(){
						//alert(selected_id);
					},	
				   success: function(result) {
					  var obj = jQuery.parseJSON(result);
					 //console.log(result);
					 //alert(JSON.stringify(obj));
					  /* var specification_var = obj.specification;
					  var hsn_code_var = obj.hsn_code;
					  var uom_var = obj.uom;

					  var uom_name = obj.uom;
						 var uom_id = obj.uomid;
					
					  var cess_var = obj.cess;


					  var valuation_type = obj.valuation_type;
					  console.log('Check It==>',valuation_type); */
					
					  //var quantity = obj.opening_balance;
					  var quantity = 1;
					  var rate = obj.price;
					  var tax = obj.tax;
					   //alert(tax);
					  var mat_idds = obj.id;
					  var mat_material_code = obj.material_code;
					  var TotalAmount = rate*quantity;
					  
					  var closing_balance = obj.closing_balance;
					    if(closing_balance == 0){
						// $(matrial_select_this).closest('.input_descr_wrap').find("input[name='quantity[]']").attr("disabled", "disabled"); 
						 // $('.chrk_mat_qty').attr("disabled", "disabled");
						//$('#mat_msg').html('This Material Not Available');		
					  }else{
						 // $(matrial_select_this).closest('.input_descr_wrap').find("input[name='quantity[]']").removeAttr("disabled"); 
						//  $('.chrk_mat_qty').removeAttr("disabled"); 
						//  $('#mat_msg').html('');
					  }
					 
					 //console.log('this==>>',$(matrial_select_this).closest('.input_descr_wrap ').find("input[name='tax[]']").val(tax));
					/* $(matrial_select_this).closest('.input_descr_wrap').find("input[name='mat_idd_name']").val(mat_idds);
					$(matrial_select_this).closest('.input_descr_wrap').find("input[name='item_code[]']").val(mat_material_code);
					$(matrial_select_this).closest('.input_descr_wrap').find("input[name='descr_of_goods[]']").val(specification_var);
					$(matrial_select_this).closest('.input_descr_wrap').find("input[name='hsnsac[]']").val(hsn_code_var); */
					$(product_select_this).closest('.input_descr_wrap').find("input[name='quantity[]']").val(1); 
					$(product_select_this).closest('.input_descr_wrap').find("input[name='price_unit[]']").val(rate);
					
					$(product_select_this).closest('.input_descr_wrap').find("input[name='fuel_taxes[]']").val(tax);
						/* if(cess_var != '' ||  cess_var != null){
							$(matrial_select_this).closest('.input_descr_wrap').find("input[name='cess[]']").val(cess_var);
							$(matrial_select_this).closest('.input_descr_wrap').find("input[name='cess_all_total[]']").val(cess_var);
							$(matrial_select_this).closest('.input_descr_wrap').find("input[name='valuation_type[]']").val(valuation_type);
						}else if(cess_var == '' ||  cess_var == null){
							$(matrial_select_this).closest('.input_descr_wrap').find("input[name='cess[]']").val('');
							$(matrial_select_this).closest('.input_descr_wrap').find("input[name='valuation_type[]']").val('');
							$(matrial_select_this).closest('.input_descr_wrap').find("input[name='cess_all_total[]']").val('');
							$(matrial_select_this).closest('.input_descr_wrap').find("input[name='cess_tax_calculation[]']").val('');
						} */
					//$(matrial_select_this).closest('.input_descr_wrap').find("input[name='sale_amount']").val(tax);
					//$(product_select_this).closest('.input_descr_wrap').find("input[name='sub_total[]']").val(TotalAmount);
					//$(matrial_select_this).closest('.input_descr_wrap').find('select[name="UOM[]"] option[value="' + uom_var + '"]').prop('selected',true);
					
				//		$("#"+closestId+"").find('.uom1').val(dataObj.uom);

          		//		$("#"+closestId+"").find('.uom').val(dataObj.uomid);

       

					//$(matrial_select_this).closest('.input_descr_wrap').find("input[name='UOM1[]']").val(uom_name);

					//$(matrial_select_this).closest('.input_descr_wrap').find("input[name='UOM[]']").val(uom_id);
					subtotal();
					
					setTimeout(function(){
						$('.keyup_event').keyup();	
					}, 1000);
					
				 }
			 });
		}); 
		
		/* $('.remove_descr_field').on('click',function(){
			setTimeout(function(){
				$('.keyup_event').keyup();
			}, 1000);
		}); */	
		
		
	
	}

function kyup_function_to_remove_add_rate_qty(){	
	//$.noConflict();
$('.keyup_event').keyup(function(){	
	var product_select_this_val =  $(this);
	    //when discount added and change quantity 
		//$(this).closest('.input_descr_wrap').find("select[name='disctype[]']").prop('selectedIndex',0);
		//$(this).closest('.input_descr_wrap').find("input[name='price_unit[]']").val('');
		//$(this).closest('.input_descr_wrap').find("input[name='discamt[]']").val('');
		//$(this).closest('.input_descr_wrap').find("input[name='discamt[]']").attr('readonly',true);
		 //when discount added and change quantity
		var theQuantity = $(this).closest('.input_descr_wrap').find("input[name='quantity[]']").val();
		//var thePrice = $(this).closest('.input_descr_wrap').find("input[name='fuel_taxes[]']").val();
		var thePrice = $(this).closest('.input_descr_wrap').find("input[name='price_unit[]']").val();
		//var thetax = $(this).closest('.input_descr_wrap').find("input[name='tax[]']").val();
		//$(this).closest('.input_descr_wrap').find("input[name='added_tax']").val(thetax);
		//var thePrice = 0.2565;
		//$(this).closest('.input_descr_wrap').find("input[name='sub_total[]']").val("2255");
		//var with_quantity_price = parseFloat(theQuantity * thePrice);
		//$(".subtotal").val(parseFloat(thePrice).toFixed(4));		
		//var percent_on_total = thetax * with_quantity_price/100;
		//alert(theQuantity+thePrice);
		var thetax = 13;
		var with_quantity_price = theQuantity * thePrice;
		var percent_on_total = thetax * with_quantity_price/100;
		
		var Total_with_tax = parseFloat(with_quantity_price);
		var valueww = Total_with_tax.toFixed(2);
		

		
		setTimeout(function(){
			var grand_total_sum = 0;
			$("input[name='sub_total[]']").each(function() {
				grand_total_sum += Number($(this).val());
			});
			
			var grand_total_sum = grand_total_sum.toFixed(2);
			
  var subtotal1 = 0;
  $("input[name='sub_total[]']").each(function() {
     subtotal1 += parseFloat($(this).val());
  });
  //$('#subtotal').val(subtotal);		
  $('.sub-total').html(subtotal1.toFixed(2));
  $("#sub_total1").val(subtotal1.toFixed(2));  
  var gst_amount = subtotal1.toFixed(2) * 13 /100;
  $('.gst-amount').html(gst_amount.toFixed(2));
  $("#gst").val(gst_amount.toFixed(2));
  var grandSum = subtotal1 + gst_amount;
  $(".grand_total_txt").html(grandSum.toFixed(2));
  $("#final_total").val(grandSum.toFixed(2));  
			//alert(grand_total_sum);
			//var charges_total = $('.total_charges_cls').val();
			//alert(charges_total);
			/* if(charges_total != '' || charges_total != '0.00'){
				
				var testee = (+charges_total) + (+grand_total_sum);
				
				$(".grand_total").val(testee);
				
			}else{ */
				
				/* //var grandTotal = parseFloat(percent_on_total).toFixed(2);
				//var grandTotalSum = parseFloat(grandTotal) + parseFloat(grand_total_sum);
				//$(".sub-total").html(grand_total_sum);
				$("#sub_total").val(subtotal1.toFixed(2));
				//$(".gst-amount").html(parseFloat(percent_on_total).toFixed(2));
				$("#gst").val(gst_amount.toFixed(2));
				//$(".grand_total_txt").html(grandTotalSum.toFixed(2));
				$(".grand_total").val(grandSum.toFixed(2)); */
			//}
		}, 1000);
		$(this).closest('.input_descr_wrap').find("input[name='sub_total[]']").val(valueww);		
		//console.log(valueww);
});
}


$(document).ready(function() {
  // Handler for .load() called.
  //subtotal();
  //add_filed_for_goods_descr_invoice();
  
});  
	
function subtotal() {
	var tot = 0;
	var sale_amount2 = 0;
	var added_tax = 0;	
	var added_tax_total = 0;
	var g_total2 = 0;

  var subtotal1 = 0;
  $("input[name='sub_total[]']").each(function() {
     subtotal1 += parseFloat($(this).val());
  });
  //$('#subtotal').val(subtotal);		
  $('.sub-total').html(subtotal1.toFixed(2));
  $("#sub_total1").val(subtotal1.toFixed(2));  
  var gst_amount = subtotal1.toFixed(2) * 13 /100;
  $('.gst-amount').html(gst_amount.toFixed(2));
  $("#gst").val(gst_amount.toFixed(2));
  var grandSum = subtotal1 + gst_amount;
  $(".grand_total_txt").html(grandSum.toFixed(2));
  $("#final_total").val(grandSum.toFixed(2));	
	/* $("input[name='sub_total[]']").each(function(){
		tot += parseFloat($(this).val());
	});
	var g_total2 = tot.toFixed(2);	
	$(".grand_total").val("work"); */
	//console.log(tot);
	/* $(".total_amount_save").val(g_total2);	
	$("input[name='sale_amount']").each(function(){
		sale_amount2 += parseFloat($(this).val());
	});
	var amount_without_tax = sale_amount2.toFixed(2);	
	$(".sub-total").val(amount_without_tax);
	$("input[name='added_tax_Row_val[]']").each(function(){
		added_tax += parseFloat($(this).val());
		 added_tax_total = added_tax.toFixed(2);	
	});	
		$(".tax_class").val(added_tax_total);	
		
	 var result_divide = parseInt(added_tax_total) / parseInt(2);
	 $(".tax_class1").val(result_divide);
	 $(".tax_class2").val(result_divide); */
}  
    

	/* $(function () {
    //Date range picker
    $('#reservationdate').datetimepicker({
        format: 'L'
    });	
	}); */
  //$( function() {
    //$("#datepicker" ).datepicker();
  //} );	
//})

//});