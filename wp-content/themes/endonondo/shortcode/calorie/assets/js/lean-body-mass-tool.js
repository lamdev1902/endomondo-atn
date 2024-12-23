jQuery(function ($) {
	$('#leanBodyMass').validate({
		rules: {
			'info[weight]': {
				required: true,
				number: true,
				min: 1
			},
			'info[height][feet]': {
				required: true,
				number: true,
				min: 1
			},
			'info[height][inches]': {
				number: true,
			},
		},
		submitHandler: function (form) {
			var formData = $('#leanBodyMass').serializeArray();
			var jsonData = {};
			$('#spinner').show();
			$('.calories-box').css('background', "rgb(250 250 250 / 1)");
			$('.calories-box').css('opacity', "0.3");
			$.each(formData, function (i, field) {
				var parts = field.name.split('[');
				var currentObj = jsonData;

				for (var j = 0; j < parts.length; j++) {
					var key = parts[j].replace(']', '');

					if (j === parts.length - 1) {
						if (field.value) {
							currentObj[key] = field.value;
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
					'get_lean_body_mass_tool': true
				},
				success: function (data) {
					$('.content-top').addClass('bdbottom');
					$('.content-bottom').html(data);
					$('#spinner').hide();
					$('.calories-box').removeAttr('style');
				}
			});
			return false;
		}
	});
})

