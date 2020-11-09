//$(function () {
$("#example1").DataTable({
  "responsive": true,
  "autoWidth": false,
  "order": [[ 0, "desc" ]]
});

$('.select2').select2();	
//disablefieldsoncheck();
$('input[name="fix_price"]').change(function () {
    if (this.checked) {
		$(".fix-product-div").slideDown();
    }
    if (this.checked == false) {
		//$('.select2').select2('0');
		//$('.invoice-edit-block').remove();
		$('.product-select2').removeAttr('required');
		$('.fix-product_amt').attr("value", "");
		$('input[name="amount[]"]').removeAttr('required');
		$('.product-select2').select2().val("0").trigger("change");
		
		$(".fix-product-div").slideUp();
    }	
});
//$('.select2').select2().val("0").trigger("change");

if ($(".fix_price").is(':checked')){
    $(".fix-product-div").show();
}

add_filed_for_fix_price_product();
function add_filed_for_fix_price_product(){
	var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".add-ro"); //Fields wrapper
    var add_button      = $(".add_more_fix_price_product"); //Add button ID
    var x = 1; //initlal text box count

    $(add_button).click(function(e){ //on add input button click

			$('.add_requried').prop("disabled", true);
			
        if(x < max_fields){ //max input box allowed
            x++; 				

				$(wrapper).append('<div class="col-md-12 input_descr_wrap add-ro2 mailing-box mobile-view no-padding-left no-padding-right scend-tr" style="margin-top:0px; "><div class="col-sm-6 col-xs-12 form-group"><label class="col-md-6 col-sm-12 col-xs-12" for="product_name">Product<span class="required">*</span></label><select data-id="products" data-key="id" data-fieldname="product_name" class="form-control select222" required name="product_id[]" data-id="products" data-key="id" data-fieldname="product_name" data-where="" width="100%" value=""><option value="0">Select Product</option></select></div><div class="col-md-6 col-sm-12 col-xs-12 form-group"><label class="col-md-12 col-sm-12 col-xs-12" for="">Amount</label><input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46" name="amount[]" required="required" class="form-control col-md-1" placeholder="Amount" >	</div><button class="btn btn-danger remove_extra_product_field" type="button"><i class="fa fa-minus"></i></button></div>');
			initailizeProductSelect2();
        }
		
	//$(".select222").select2();
    });
	
    $(".add-ro").on("click",".remove_extra_product_field", function(e){ //user click on remove text
        e.preventDefault();
		$(this).parent('div').remove(); x--;
		setTimeout(function(){
						$('.keyup_event').keyup();	
					}, 1000);
    });
}

// Initialize Product select2	 
function initailizeProductSelect2(){
	var company_id = $('.party_name').val();
	$(".select222").select2({
         ajax: { 
           url: site_url+'user/all_products',
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

/* function handleChange(input) {
	//if (input.value < 0) input.value = 0;
	//if (input.value > 1000) input.value = 1000;
	if (input.value > 1000){
		alert('Max $1000 allowed');
	}
} */

$('#amount').on('keyup', function(){
	var amoutVal = $(this).val();
	if (amoutVal > 1000){
		$(this).val(1000.00);
		alert('Max $1000 allowed');
	}
});

/* $('#amount').keypress(function(event) {
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
}); */


// https://github.com/k-ivan/Tags
(function() {

  'use strict';

  // Helpers
  function $$(selectors, context) {
    return (typeof selectors === 'string') ? (context || document).querySelectorAll(selectors) : [selectors];
  }
  function $(selector, context) {
    return (typeof selector === 'string') ? (context || document).querySelector(selector) : selector;
  }
  function create(tag, attr) {
    var element = document.createElement(tag);
    if(attr) {
      for(var name in attr) {
        if(element[name] !== undefined) {
          element[name] = attr[name];
        }
      }
    }
    return element;
  }
  function whichTransitionEnd() {
    var root = document.documentElement;
    var transitions = {
      'transition'       : 'transitionend',
      'WebkitTransition' : 'webkitTransitionEnd',
      'MozTransition'    : 'mozTransitionEnd',
      'OTransition'      : 'oTransitionEnd otransitionend'
    };

    for(var t in transitions){
      if(root.style[t] !== undefined){
        return transitions[t];
      }
    }
    return false;
  }
  function oneListener(el, type, fn, capture) {
    capture = capture || false;
    el.addEventListener(type, function handler(e) {
      fn.call(this, e);
      el.removeEventListener(e.type, handler, capture)
    }, capture);
  }
  function hasClass(cls, el) {
    return new RegExp('(^|\\s+)' + cls + '(\\s+|$)').test(el.className);
  }
  function addClass(cls, el) {
    if( ! hasClass(cls, el) )
      return el.className += (el.className === '') ? cls : ' ' + cls;
  }
  function removeClass(cls, el) {
    el.className = el.className.replace(new RegExp('(^|\\s+)' + cls + '(\\s+|$)'), '');
  }
  function toggleClass(cls, el) {
    ( ! hasClass(cls, el)) ? addClass(cls, el) : removeClass(cls, el);
  }

  function Tags(tag) {

    var el = $(tag);

    if(el.instance) return;
    el.instance = this;

    var type = el.type;
    var transitionEnd = whichTransitionEnd();

    var tagsArray = [];
    var KEYS = {
      ENTER: 13,
      COMMA: 188,
      BACK: 8
    };
    var isPressed = false;

    var timer;
    var wrap;
    var field;

    function init() {

      // create and add wrapper
      wrap = create('div', {
        'className': 'tags-container',
      });
      field = create('input', {
        'type': 'text',
        'className': 'tag-input',
        'placeholder': el.placeholder || ''
      });

      wrap.appendChild(field);

      if(el.value.trim() !== '') {
        hasTags();
      }

      el.type = 'hidden';
      el.parentNode.insertBefore(wrap, el.nextSibling);

      wrap.addEventListener('click', btnRemove, false);
      wrap.addEventListener('keydown', keyHandler, false);
      wrap.addEventListener('keyup', backHandler, false);
    }

    function hasTags() {
      var arr = el.value.trim().split(',');
      arr.forEach(function(item) {
        item = item.trim();
        if(~tagsArray.indexOf(item)) {
          return;
        }
        var tag = createTag(item);
        tagsArray.push(item);
        wrap.insertBefore(tag, field);
      });
    }

    function createTag(name) {
      var tag = create('div', {
        'className': 'tag',
        'innerHTML': '<span class="tag__name">' + name + '</span>'+
                     '<button class="tag__remove">&times;</button>'
      });
//       var tagName = create('span', {
//         'className': 'tag__name',
//         'textContent': name
//       });
//       var delBtn = create('button', {
//         'className': 'tag__remove',
//         'innerHTML': '&times;'
//       });
      
//       tag.appendChild(tagName);
//       tag.appendChild(delBtn);
      return tag;
    }

    function btnRemove(e) {
      e.preventDefault();
      if(e.target.className === 'tag__remove') {
        var tag  = e.target.parentNode;
        var name = $('.tag__name', tag);
        wrap.removeChild(tag);
        tagsArray.splice(tagsArray.indexOf(name.textContent), 1);
        el.value = tagsArray.join(',')
      }
      field.focus();
    }

    function keyHandler(e) {

      if(e.target.tagName === 'INPUT' && e.target.className === 'tag-input') {

        var target = e.target;
        var code = e.which || e.keyCode;

        if(field.previousSibling && code !== KEYS.BACK) {
          removeClass('tag--marked', field.previousSibling);
        }

        var name = target.value.trim();

        // if(code === KEYS.ENTER || code === KEYS.COMMA) {
        if(code === KEYS.ENTER) {

          target.blur();

          addTag(name);

          if(timer) clearTimeout(timer);
          timer = setTimeout(function() { target.focus(); }, 10 );
        }
        else if(code === KEYS.BACK) {
          if(e.target.value === '' && !isPressed) {
            isPressed = true;
            removeTag();
          }
        }
      }
    }
    function backHandler(e) {
      isPressed = false;
    }

    function addTag(name) {

      // delete comma if comma exists
      name = name.toString().replace(/,/g, '').trim();

      if(name === '') return field.value = '';

      if(~tagsArray.indexOf(name)) {

        var exist = $$('.tag', wrap);

        Array.prototype.forEach.call(exist, function(tag) {
          if(tag.firstChild.textContent === name) {

            addClass('tag--exists', tag);

            if(transitionEnd) {
              oneListener(tag, transitionEnd, function() {
                removeClass('tag--exists', tag);
              });
            } else {
              removeClass('tag--exists', tag);
            }


          }

        });

        return field.value = '';
      }

      var tag = createTag(name);
      wrap.insertBefore(tag, field);
      tagsArray.push(name);
      field.value = '';
      el.value += (el.value === '') ? name : ',' + name;
    }

    function removeTag() {
      if(tagsArray.length === 0) return;

      var tags = $$('.tag', wrap);
      var tag = tags[tags.length - 1];

      if( ! hasClass('tag--marked', tag) ) {
        addClass('tag--marked', tag);
        return;
      }

      tagsArray.pop();

      wrap.removeChild(tag);

      el.value = tagsArray.join(',');
    }

    init();

    /* Public API */

    this.getTags = function() {
      return tagsArray;
    }

    this.clearTags = function() {
      if(!el.instance) return;
      tagsArray.length = 0;
      el.value = '';
      wrap.innerHTML = '';
      wrap.appendChild(field);
    }

    this.addTags = function(name) {
      if(!el.instance) return;
      if(Array.isArray(name)) {
        for(var i = 0, len = name.length; i < len; i++) {
          addTag(name[i])
        }
      } else {
        addTag(name);
      }
      return tagsArray;
    }

    this.destroy = function() {
      if(!el.instance) return;

      wrap.removeEventListener('click', btnRemove, false);
      wrap.removeEventListener('keydown', keyHandler, false);
      wrap.removeEventListener('keyup', keyHandler, false);

      wrap.parentNode.removeChild(wrap);

      tagsArray = null;
      timer = null;
      wrap = null;
      field = null;
      transitionEnd = null;

      delete el.instance;
      el.type = type;
    }
  }

  window.Tags = Tags;

})();

// Use
var tags = new Tags('.tagged');
window.onload=function(){
	document.getElementById('get').addEventListener('click', function(e) {
	  e.preventDefault();
	  alert(tags.getTags());
	});
	document.getElementById('clear').addEventListener('click', function(e) {
	  e.preventDefault();
	  tags.clearTags();
	});
	document.getElementById('add').addEventListener('click', function(e) {
	  e.preventDefault();
	  tags.addTags('New');
	});
	document.getElementById('addArr').addEventListener('click', function(e) {
	  e.preventDefault();
	  tags.addTags(['Steam Machines', 'Nintendo Wii U', 'Shield Portable']);
	});
	document.getElementById('destroy').addEventListener('click', function(e) {
	  e.preventDefault();
	  if(this.textContent === 'destroy') {
		tags.destroy();
		this.textContent = 'reinit';
	  } else {
		this.textContent = 'destroy';
		tags = new Tags('.tagged');
	  }
	  
	});
}
