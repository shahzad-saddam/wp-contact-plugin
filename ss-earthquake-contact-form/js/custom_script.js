jQuery(function() {
	jQuery('#hayir').click(function(){
	   jQuery('#textareareason').show();     
	});
	jQuery('#evet').click(function(){
	    jQuery('#textareareason').hide();     
	});


jQuery.validator.addMethod("regexp", function (value, element) {
    return this.optional(element) || /^\d{3}-\d{7}$/.test(value);
}, '  Lütfen telefon numaranızı alan koduyla birlikte, başında 0 olmadan 10 haneli olarak giriniz.');


	jQuery( "#ss-form" ).validate({
	  rules: {
	    agent_name: {
	      required: true,
	    },
	    registration_num: {
	      required: true,
	    },
	    province: {
	    	required: true,
	    },
	    city: {
	    	required: true,
	    },
	    address: {
	    	required: true,
	    },
	    phone: {
	    	required: true,
	    	 digits: true,
	    	minlength: 10,
	      	maxlength: 10
	    },
	    email: {
	    	required: true,
	    	email: true	
	    },
	    contact_person: {
	    	required: true,
	    },
	    competent_person_cell: {
	    	required: true,
	    	digits: true,
	    	minlength: 10,
	        maxlength: 10,
	    },

	  }
	});



	jQuery.ajax({
        type:"POST",
        url: wnm_custom.ajax_url,
        success: function(html){
            jQuery('#province').html(html);
        }
    });


    jQuery('#province').change(function(){

    	var province = jQuery('#province').val();
    	jQuery.ajax({
	        type:"POST",
	        url: wnm_custom.ajax_url,
	        data: "province="+province,
	        success: function(html){
	            jQuery('#city').html(html);
	        }
	    });

    });

    jQuery.extend(jQuery.validator.messages, {
    required: "Bu alanı boş bırakamazsınız.",
    remote: "Please fix this field.",
    email: "Lütfen geçerli bir eposta adresi giriniz.",
    url: "Please enter a valid URL.",
    date: "Please enter a valid date.",
    dateISO: "Please enter a valid date (ISO).",
    number: "Please enter a valid number.",
    digits: "Lütfen telefon numaranızı alan koduyla birlikte, başında 0 olmadan 10 haneli olarak giriniz.",
    creditcard: "Please enter a valid credit card number.",
    equalTo: "Please enter the same value again.",
    accept: "Please enter a value with a valid extension.",
    maxlength: jQuery.validator.format("Lütfen telefon numaranızı alan koduyla birlikte, başında 0 olmadan 10 haneli olarak giriniz."),
    minlength: jQuery.validator.format("Lütfen telefon numaranızı alan koduyla birlikte, başında 0 olmadan 10 haneli olarak giriniz."),
    rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
    min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
});
});
