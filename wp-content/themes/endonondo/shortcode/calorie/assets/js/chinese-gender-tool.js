jQuery(function($) {
	jQuery.validator.addMethod("checkDate", function(value, element) {
		var dueDate = $('#dueDatepicker').datepicker('getDate');
        var dobDate = $('#dobDatepicker').datepicker('getDate');

        if (dueDate < dobDate) {
            return false; 
        }else {
			return true;
		}
	}, "Please check your Month of Conception");

	jQuery.validator.addMethod("checkAge", function(value, element) {
        var dobDate = $('#dobDatepicker').datepicker('getDate');
		var today = new Date();
		var minDOB = new Date();
		minDOB.setFullYear(minDOB.getFullYear() - 18); 
		
		if (dobDate > today || dobDate > minDOB) {
			return false; 
		}else {
			return true;
		}
	}, "Please check your Mother's Date of Birth");
	var date = new Date();
	var today  = date.toLocaleDateString('en-US', {
		month: 'long',
		day: 'numeric',
		year: 'numeric'
	});

	$('#dueDatepicker').datepicker({
		dateFormat: 'MM d, yy',
		changeYear: true,
		defaultDate: new Date(),
		minDate: new Date('1990-01-01')
	})

	$('#dueDatepicker').val(today);

	var minAge = new Date('1990-01-01').toLocaleDateString('en-US', {
		month: 'long',
		day: 'numeric',
		year: 'numeric'
	});
	$('#dobDatepicker').datepicker({
		dateFormat: 'MM d, yy',
		changeYear: true,
		minDate: new Date('1990-01-01')
	})

	$('#dobDatepicker').val(minAge);


	$('#chineseGender').validate({
		rules: {
		    dd:  {
				required: true,
				date: true,
				checkDate: true
		    },
		    dob:  {
				required: true,
				date: true,
				checkAge: true
		    }
	  	},
		  submitHandler: function(form) {
			var formData = $('#chineseGender').serializeArray();
			var jsonData = {};
            $('.content-bottom').empty();
            $('.container').css('position', 'relative');
            $('.wrapper').css('background', "rgb(250 250 250 / 1)");
            $('.wrapper').css('opacity', "0.3");
            $('#spinner').show();

			$.each(formData, function(i, field) {
				var parts = field.name.split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
					if(field.value)
					{
						var dateObj = new Date(field.value);
						var year = dateObj.getFullYear();
						var month = dateObj.getMonth() + 1; 
						var day = dateObj.getDate();

						var formattedDate = year + "-" + month + "-" + day;
						currentObj[key] = formattedDate;
					}
					} else {
						currentObj[key] = currentObj[key] || {};
						currentObj = currentObj[key];
					}
				}
			});
			$.ajax({
			  url: 'https://www.endomondo.com/',
			  type: 'GET', 
			  cache: false,
			  dataType: "json",
			  data: {
				  jsonData,
				  'get_chinese_gender_tool':true 
			  },
			  success: function(data) {
				  $('.content-top').addClass('bdbottom');
				  $('.content-bottom').html(data);
				 $('#spinner').hide();
                $('.container').removeAttr('style');
                $('.wrapper').removeAttr('style');
			  }
		  });
		  return false;
		}
	});
})

