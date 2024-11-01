jQuery(function($) {
	'use strict';
	$('#wotb_type_of_link').change(function(){
		var val = $(this).val();
		var val2 = $('#wotb_html_place').val();
		if(val == '1'){
			$('#preview_link1').show();
			$('#preview_link2').hide();
			if(val2 == '1' || val2 == '3'){
				$('#wotb_link_style_div').show();
			}else{
				$('#wotb_link_style_div').hide();
			}
		}else if(val == '2'){
			$('#preview_link2').show();
			$('#preview_link1').hide();
			$('#wotb_link_style_div').hide();
		}
	});

	$('#wotb_html_place').change(function(){
		var val2 = $('#wotb_type_of_link').val();
		var val = $(this).val();
		if(val2 == '1'){
			if(val == '1' || val == '3'){
				$('#wotb_link_style_div').show();
			}else{
				$('#wotb_link_style_div').hide();
			}
		}


	});


	$('#wotb_places').select2({
		tags: true,
		multiple: true,
		minimumInputLength: 3,
		maximumSelectionLength: 3,
	    ajax: {
	        url: 'http://api.geonames.org/searchJSON?style=medium&orderby=population&maxRows=10&username=wotwot&featureClass=H&featureClass=L&featureClass=P&featureClass=S&featureClass=T&featureClass=V',
	        dataType: 'json',
	        type: "GET",
		    data: function (params) {
		      var query = {
		        name_startsWith: params.term,
		      }
		      return query;
		    },

			processResults: function (data) {
				return {
					results: $.map(data.geonames, function (item) {
						if(item.countryName == '' || item.adminName1 == ''){
						} else {
						
						return {
							text: item.name +', ' + item.adminName1 + ", " + item.countryName,
							id: item.name+':'+item.geonameId,
						}
					}
						return results
				})
			};
	    },
	    }

	});

});