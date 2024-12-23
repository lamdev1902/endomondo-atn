jQuery(function($) {
	calorie_tool_json();

	$(document).ready(function () {
		$('select[name="unit"]').on('change', function () {
			var selectedValue = $(this).val(); 
			
			$('input[name="rsunit"][value="' + selectedValue + '"]').prop('checked', true);
		});

	});
})
function calorie_tool_json() {
	$('#rmCalculate').validate({
		rules: {
		    'info[weight]':  {
				required: true,
				number: true,
				min: 1
		    },
		    'info[rep]':  {
				required: true,
				number: true,
				min: 1,
                max:10
		    },
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
            var formData = $('#rmCalculate').serializeArray();
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
					'get_repmax_tool':true 
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