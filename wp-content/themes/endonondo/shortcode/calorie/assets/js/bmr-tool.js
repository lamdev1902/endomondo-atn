jQuery(function($) {
	calorie_tool_json();

	jQuery.validator.addMethod("bodyfat", function(value, element) {	
		if($('input[name="receip"]:checked').val() == 3){
			var check = value != "" ? true : false;

			return check;
		}
		return true;
	}, "Please enter the value");
})
function calorie_tool_json() {
	$('input[name="receip"]').on('change',function() {
		if($(this).val() == '3') $('.calories-form .input-body').css('opacity','1');
		else $('.calories-form .input-body').css('opacity','0');
	});
	
	$("#btnClear").on('click', function(){
		$("input[name='info[age]").val('');
		$("input[name='info[weight]").val('');
		$("input[name='info[height][feet]").val('');
		$("input[name='info[height][inches]").val('');
	})
	$('#bmrCalculate').validate({
		rules: {
		    'info[age]':  {
				required: true,
				number: true,
				min: 15,
				max: 80
		    },
		    'info[weight]':  {
				required: true,
				number: true,
				min: 1
		    },
		    'info[height][feet]':  {
				required: true,
				number: true,
				min: 1
		    },
		    'info[height][inches]':  {
				number: true,
		    },
			'info[body-fat]': {
				bodyfat: true
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
            var formData = $('#bmrCalculate').serializeArray();
			var jsonData = {};
			$('.fillResult').empty();
			$('#spinner').show();
			$('.calories-custom').css('position', 'relative');
			$('.calories-box').css('background', "rgb(250 250 250 / 1)");
            $('.calories-box').css('opacity', "0.3");
            $.each(formData, function(i, field) {
				var parts = field.name.split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
					if(field.value)
					{
						currentObj[key] = field.value;
					}
					} else {
						currentObj[key] = currentObj[key] || {};
						currentObj = currentObj[key];
					}
				}
			});
	  		$.ajax({
				url:'https://www.endomondo.com/',
				type: 'GET', 
				cache: false,
				dataType: "json",
				data: {
					jsonData,  
					'get_bmr_tool':true 
				},
				success: function(data) {
					$('.fillResult').html(data);
					$('#spinner').hide();
					$('.calories-custom').removeAttr('style');
					$('.calories-box').removeAttr('style');
					var off = $('.fillResult').offset().top;
					$('html,body').animate({ scrollTop: off }, 600);
				}
			});
			return false;
	  	}
	});
}