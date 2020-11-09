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

	$('#exportBlankCardExcel').click(function(){
		window.location = $('#exportBlankCardExcel').attr('href');
		 //alert( "Handler for .click() called." );
	});	
	
	$('#expo').click(function(){
		window.location = $('#exportCards').attr('href');
		 //alert( "Handler for .click() called." );
	});

	//Add rows for tax type and amount
	add_filed_for_goods_descr_tax_type();
	function add_filed_for_goods_descr_tax_type(){
		var max_fields      = 10; //maximum input boxes allowed
		var wrapper         = $(".add-ro"); //Fields wrapper
		var add_button      = $(".add_description_detail_button_tax"); //Add button ID
		var x = 1; //initlal text box count

		$(add_button).click(function(e){ //on add input button click
			//e.preventDefault();

				$('.add_requried').prop("disabled", true);			
				
			if(x < max_fields){ //max input box allowed
				x++; 				

					$(wrapper).append('<div class="col-md-12 input_descr_wrap add-ro2 mailing-box mobile-view no-padding-left no-padding-right" style="margin-top:0px; "><div class="col-md-6 col-sm-12 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Tax Type</label><input type="text" name="tax_type[]" required="required" class="form-control col-md-1" placeholder="Tax Type" value="">	</div><div class="col-md-6 col-sm-12 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="quantity">Tax Amount<span class="required">*</span></label><input type="text" name="tax_amount[]" required="required" class="form-control col-md-1" placeholder="Tax Amount" value=""></div><button class="btn btn-danger remove_descr_field" type="button"><i class="fa fa-minus"></i></button></div>');
				
			}

		});
		
		$(".add-ro").on("click",".remove_descr_field", function(e){ //user click on remove text
			e.preventDefault();
			$(this).parent('div').remove(); x--;
			setTimeout(function(){
							$('.keyup_event').keyup();	
						}, 1000);
		});
	}