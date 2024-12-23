jQuery(function($) {
	$('input[name="info[gender]"]').change(function(){
		if($(this).val() == 2) {
			$('.hip').removeClass('inactive')
		}else {
			$('.hip').addClass('inactive')
		}
	});
	$('#armyBodyFat').validate({
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
			'info[neck][feet]':  {
				required: true,
				number: true,
				min: 1
		    },
		    'info[neck][inches]':  {
				number: true,
		    },
			'info[waist][feet]':  {
				required: true,
				number: true,
				min: 1
		    },
		    'info[waist][inches]':  {
				number: true,
		    },
			'info[hip][feet]':  {
				required: true,
				number: true,
				min: 1
		    },
		    'info[hip][inches]':  {
				number: true,
		    }
	  	},
		  submitHandler: function(form) {
		  $('#spinner').show();
			var formData = $('#armyBodyFat').serializeArray();
			var jsonData = {};
	
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
				  'get_army_body_fat_tool':true 
			  },
			  success: function(data) {
				  $('.content-top').addClass('bdbottom');
				  $('.content-bottom').html(data);
				  $('#spinner').hide();
			  }
		  });
		  return false;
		}
	});
})

