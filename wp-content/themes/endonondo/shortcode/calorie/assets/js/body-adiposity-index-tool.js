jQuery(function($) {
	$('#baiCalculate').validate({
		rules: {
		    'info[height][feet]':  {
				required: true,
				number: true,
				min: 1
		    },
		    'info[height][inches]':  {
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
			var formData = $('#baiCalculate').serializeArray();
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
				  'get_body_adiposity_index_tool':true 
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

