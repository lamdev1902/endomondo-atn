jQuery(function($) {
    var date = new Date();

        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;

        var today = year + "-" + month + "-" + day;   

        $("[name=ageat]").attr("value", today);


        var optionMonDate = '.dateMonthInput option:eq(0)';
        var optionMonAge = '.ageMonthInput option:eq(' + (month - 1) + ')';
        $(optionMonDate).attr('selected', 'selected');
        $(optionMonAge).attr('selected', 'selected');

        var lastDayOfMonth = new Date(year, month, 0);

        var numberOfDays = lastDayOfMonth.getDate();

       
        for(var i = 1; i <= numberOfDays; i++)
        {
            var optionDate = $('<option></option>');
            var optionAge = $('<option></option>');

            optionDate.attr('value', i);
            optionDate.text(i);
            $('.dayDateInput').append(optionDate);

            optionAge.attr('value', i);
            optionAge.text(i);
            $('.ageDateInput').append(optionAge);
            
        }
        var optionDayDate = '.dayDateInput option:eq(0)';
        var optionDayAge = '.ageDateInput option:eq(' + (day - 1) + ')';
        $(optionDayDate).attr('selected', 'selected');
        $(optionDayAge).attr('selected', 'selected');

        $("[name=year-age]").val(year);

        $('[name=date-birth]').change(function(){
            var result = getTime(1);
    
            changeTime(result, "dob");
        })
        $('[name=mon-birth]').change(function(){
            var result = getTime(1);
    
            changeTime(result, "dob");
            
        })
        $('[name=year-birth]').change(function(){
            var result = getTime(1);
    
            changeTime(result, "dob");
            
        })
        $('[name=date-age]').change(function(){
            var result = getTime(2);
    
            changeTime(result, "ageat");
            
        })
        $('[name=mon-age]').change(function(){
            var result = getTime(2);
    
            changeTime(result, "ageat");
            
        })
    
        $('[name=year-age]').change(function(){
            var result = getTime(2);
    
            changeTime(result, "ageat");

        })
	$('#ageCalculate').validate({
		rules: {
            'mon-age': {
				required: true,
				number: true,
                min: 1,
                max: 12
            },
            'date-age': {
				required: true,
				number: true,
                min: 1
            },
            'year-age': {
				required: true,
				number: true
            },
            'mon-birth': {
				required: true,
				number: true,
                min: 1,
                max: 12
            },
            'date-birth': {
				required: true,
				number: true,
                min: 1
            },
            'year-birth': {
				required: true,
				number: true
            },
		    dob:  {
				required: true,
				date: true
		    },
		    ageat:  {
				required: true,
				date: true,
		    }
	  	},
		  submitHandler: function(form) {
			var formData = $('#ageCalculate').serializeArray();
            
            formData = formData.filter(function(item) {
                return item.name == 'dob' || item.name == 'ageat';
            });
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
				  'get_age_tool':true 
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

function changeTime(time, element)
{
    var day = time[0].day;
    var month = time[0].month;
    var year = time[0].year;

    var today = year + "-" + month + "-" + day;   
    
    var elementName = "[name="+element+"]";
    $(elementName).attr("value", today);
}

function getTime($type)
{
    var result = [];
    if($type == 1)
    {
        var day = $('[name=date-birth]').val();
        var mon = $('[name=mon-birth]').val();
        var year = $('[name=year-birth]').val();
    }else {
        var day = $('[name=date-age]').val();
        var mon = $('[name=mon-age]').val();
        var year = $('[name=year-age]').val();
    }
    
    result.push(
        {
            "day": day,
            "month": mon,
            "year": year
        }
    )
    return result;
}