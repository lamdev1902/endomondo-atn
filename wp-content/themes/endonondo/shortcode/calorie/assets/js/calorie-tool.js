jQuery(function($) {
	calorie_tool_json();
})
function calorie_tool_json() {
	$('input[name="calorie_receip"').on('change',function() {
		console.log($(this).val());
		if($(this).val() == '3') $('.calories-form .input-body').css('opacity','1');
		else $('.calories-form .input-body').css('opacity','0');
	});
	$('body').on('click','.show-wgain',function() {
		console.log('clicked');
		$('.result-gain').toggleClass('lhide');
		 $(this).text( ($(this).text() == 'Show info for weight gain' ? 'Hide info for weight gain' : 'Show info for weight gain') );
		 return false;
	});
	
	$('#clorieToolForm').validate({
		rules: {
		    calorie_age:  {
				required: true,
				number: true,
				min: 15,
				max: 80
		    },
		    calorie_weight:  {
				required: true,
				number: true,
				min: 1
		    },
		    calorie_height_ft:  {
				required: true,
				number: true,
				min: 1
		    },
		    calorie_height_in:  {
				required: true,
				number: true,
				min: 1
		    },
		    calorie_fat:  {
				number: true,
				min: 1
		    }
	  	},
	  	messages: {
	  		rsl_balance: {
		      required: 'Please enter the value',
		      number: 'Value is numeric'
		    },
		    rsl_monthly:  {
		    	required: 'Please enter the value',
		      number: 'Value is numeric'
		    },
		    rsl_interest:  {
				required: 'Please enter the value',
				number: 'Value is numeric'
		    }
	  	},
	  	submitHandler: function(form) {
            $('#spinner').show();
			$('.calories-box').css('background', "rgb(250 250 250 / 1)");
            $('.calories-box').css('opacity', "0.3");
	  		$.ajax({
				url:'https://www.endomondo.com/',
				type: 'GET', 
				cache: false,
				dataType: "json",
				data: {
					calorie_gender: $('input[name="calorie_gender"]:checked').val(),
					calorie_age: $('input[name="calorie_age"]').val(), 
					calorie_weight: $('input[name="calorie_weight"]').val(),  
					calorie_height_ft: $('input[name="calorie_height_ft"]').val(),  
					calorie_height_in: $('input[name="calorie_height_in"]').val(),  
					calorie_level: $('select[name="calorie_level"]').val(),  
					calorie_unit: $('input[name="calorie_unit"]:checked').val(),  
					calorie_receip: $('input[name="calorie_receip"]:checked').val(),  
					calorie_fat: $('input[name="calorie_fat"]').val(),  
					'get_calorie_tool':true 
				},
				success: function(data) {
					$('.fillResult').html(data);
					$('#spinner').hide();
					$('.calories-box').removeAttr('style');
					var off = $('.fillResult').offset().top;
					$('html,body').animate({ scrollTop: off }, 600);
				},
				
			});
			return false;
	  	}
	});
}