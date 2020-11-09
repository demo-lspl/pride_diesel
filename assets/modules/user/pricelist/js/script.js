add_filed_for_price_descr();
function add_filed_for_price_descr(){
	var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".add-ro"); //Fields wrapper
    var add_button      = $(".add_more_price_descr"); //Add button ID
    var x = 1; //initlal text box count
//$(document).on("click",".add_description_detail_button_invoice",function(){

    $(add_button).click(function(e){ //on add input button click
	    //e.preventDefault();
			$('.add_requried').prop("disabled", true);
			var company_login_id = $('#company_login_id').val();			
			var get_discount_on_off = $('#get_discount_on_off').val();
			var company_login_id = "2";
			
        if(x < max_fields){ //max input box allowed
            x++; 				

				$(wrapper).append('<div class="col-md-12 input_descr_wrap add-ro2 mailing-box mobile-view no-padding-left no-padding-right" style="margin-top:0px; "><div class="col-sm-6 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="driver_name">Company Type <span class="required">*</span></label><select class="form-control company_type" required="required" name="company_id[]" data-id="company_types" data-key="id" data-fieldname="company_type" data-where="" width="100%" ><option value="0">Select Company</option></select></div><div class="col-md-6 col-sm-12 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="descriptions">Price</label><input type="text" name="price[]" required="required" class="form-control col-md-1" placeholder="Price" value="">	</div><button class="btn btn-danger remove_descr_field" type="button"><i class="fa fa-minus"></i></button></div>');

				initailizeCompanyTypeSelect2();
			
        }

		
	//$(".company_type").select2();
    });
	
    $(".add-ro").on("click",".remove_descr_field", function(e){ //user click on remove text
        e.preventDefault();
		$(this).parent('div').remove(); x--;
		setTimeout(function(){
						$('.keyup_event').keyup();	
					}, 1000);
    });
}
initailizeCompanyTypeSelect2();
$(".company_type").select2();
// Initialize Company Type select2	 
function initailizeCompanyTypeSelect2(){

	$(".company_type").select2({
         ajax: { 
           url: site_url+'user/company_types/',
           //type: "post",
           dataType: 'json',
           delay: 250,
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
         }
     });
}	