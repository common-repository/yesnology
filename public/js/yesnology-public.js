(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 */
	  $(function() {
		$('.selectLanguage').select2({
			width: 'auto'
		});
		$('.yesnology-select,.yesnology-select-nations,.yesnology-select-regions,.yesnology-select-prov').select2({
			width: 'resolve'
		});
		if ($('.yesnology-select-nations').val() === "") {
				$('.regions').hide();
				$('.prov').hide();
			} else {
				$('.regions').show();
				$('.prov').show();
			}
		$('.yesnology-select-nations').change((event) => {
			if (event.target.value === "") {
					$('.regions').hide();
					$('.prov').hide();
					$('.yesnology-select-regions').empty()
					$('.yesnology-select-prov').empty()
					$(".yesnology-select-regions").prop('required',false);
				}
				else {
					$('.yesnology-select-regions').empty();
					$('#loader-regions').show();
					$.get(yesnologyobject.ajaxurl + "/wp-json/yesnology/region/" + event.target.value, function(data, status){
						$('#loader-regions').hide();
						if (Object.keys(data).length) { 
							$('.regions').show();
							$(".yesnology-select-regions").prop('required',true);
							$('.yesnology-select-regions').append($("<option></option>")
								.attr("value", "").text(yesnologyobject.scegliRegione));
							$.each(data, function(key,value) {
								$('.yesnology-select-regions').append($("<option></option>")
							   		.attr("value", value.regionCode).text(value.name));
						  	});
						} else {
							$(".yesnology-select-regions").prop('required',false);
							$(".yesnology-select-prov").prop('required',false);
							$('.regions').hide();
							$('.prov').hide();
							$('.yesnology-select-regions').empty();
							$('.yesnology-select-prov').empty();
						}
					});
				}
	  	});
		$('.yesnology-select-regions').change((eventRegion) => {
			if (eventRegion.target.value === "") {
				$('.prov').hide();
				$('.yesnology-select-prov').empty()
				$(".yesnology-select-prov").prop('required',false);
			}
			else {
				$('.yesnology-select-prov').empty();
				$('#loader-prov').show();
				$.get(yesnologyobject.ajaxurl + "/wp-json/yesnology/prov/" + $('.yesnology-select-nations').val() + "/" + $('.yesnology-select-regions').val(), function(data, status){
					$('#loader-prov').hide();
					if (Object.keys(data).length) { 
						$('.prov').show();
						$(".yesnology-select-prov").prop('required',true);
						$('.yesnology-select-prov').append($("<option></option>")
							.attr("value", "").text(yesnologyobject.scegliProvincia));
						$.each(data, function(key,value) {
							$('.yesnology-select-prov').append($("<option></option>")
						   		.attr("value", value.regionCode).text(value.name));
					  	});
					} else {
						$('.prov').hide();
						$(".yesnology-select-prov").prop('required',false);
					}
				  });
			}
	  	});
		$('.yesnology-select-toggle').select2({
			width: '200px'
		});
		$('.yesnology-select-toggle').change((event) => {
			$('#yesnology-select-toggle-container' + event.target.id).hide();
			$('#toggle-switchy' + event.target.id).show();
			if (event.target.value === "1") $('#switch' + event.target.id).prop( "checked", true )
				else $('#switch' + event.target.id).prop( "checked", false )
	  	});
		if ($('.tel').length) {
		try {
			window.intlTelInput(document.querySelector('.tel'), {
				initialCountry: yesnologyobject.country_id
			});
		} catch {
			window.intlTelInput(document.querySelector('.tel'), {
				initialCountry: 'us'
			});
		}}
		$( ".yesnology-form" ).submit(function( event ) {
			event.preventDefault();
			$('#loader-submit').show();
			let errorData = false;
			let dataPost = [];
			let arrayValue = [];
			if ($('p.checkbox-group.required :checkbox:checked').length > 0 || !$('p.checkbox-group.required').length) {
			$("form#yesnology-form :input").each(function(){
				if ($(this).attr("name")) {
				let singleObj = {};
				singleObj.binderFieldId = $(this).attr("name");
				switch ($(this).data("type")) {
					case "date":
						singleObj.Value = $(this).val() + "T00:00:00";
						break;
					case "time":
						singleObj.Value = "0001-01-01T" + $(this).val() + ":00";
						break;
					case "range":
						singleObj.Value = parseInt($(this).val());
						console.log("range");
						console.log(parseInt($(this).val()));
						break;
					case "radio":
						if ($(this).is(":checked")) singleObj.Value = $(this).val();
						console.log("radio " + $(this).val());
						break;
					default:
						if ($(this).hasClass("yesnology-select-nations")) {
								singleObj.Value = parseInt($(this).val());
								console.log(parseInt($(this).val()));
							} else if ($(this).hasClass("yesnology-checkbox")) {
									if ($(this).is(":checked")) {
										arrayValue.push($(this).val());
										singleObj.Value = arrayValue;
										console.log(arrayValue);
										console.log(singleObj.Value);
									}
								} else if ($(this).hasClass("toggle-switchy-input") || $(this).hasClass("yesnology-checkbox-single") ) {
										if (($(this).data( "template") === 1151 && !$(this).is(":checked"))) errorData = "checkbox-error-1151"; 
										
										if ($(this).data( "template") === 1161 && $(this).is(":checked"))  errorData = "checkbox-error-1161";
										else
										if ($(this).is(":checked")) {
											singleObj.Value = $(this).val();
										}
										else {
											singleObj.Value = false;
										}
									}
									else if ($(this).val() === "true") singleObj.Value = true;
										else {
											console.log($(this).attr("name"));
											singleObj.Value = $(this).val();
										}
						break;
				}
				//if ($(this).hasClass("yesnology-checkbox") && singleObj.Value !== undefined) singleObj.Value = '["' + singleObj.Value.join('","') + '"]';
				if (singleObj.Value !== undefined) {
					let finded = false;
					dataPost.forEach((singleData) => { 
						if (singleData.binderFieldId == singleObj.binderFieldId) finded = true;
					})
					if (finded === false) dataPost.push(singleObj);
				}}
				/*
				$singleObj->binderFieldId = $key;
				$singleObj->Value = $singlePost;
				$dataPost[] = $singleObj;
				*/
			});
			const dataObj = {
				"languageId": yesnologyobject.languageId,
				"binderAnswerFieldDtos": dataPost
			}
			if (!errorData)
			$.post(yesnologyobject.ajaxurl + "/wp-json/yesnology/send_data",
  				dataObj,
  				function(data, status){
					if (data !== "") Object.values(JSON.parse(data).errors).forEach(element => { element.forEach(elementSingle => {
							alert(elementSingle);
						})}); 
						else $("#yesnology-form-page").html( yesnologyobject.yesnologyConfirmPage );
					$('#loader-submit').hide();
  				}).fail(function(error) { 
					console.log(error); 
					$('#loader-submit').hide();
				});
			else $("." + errorData).show();
			} else $(".checkbox-group-error").show();
		});
		
	  });
	 /*
	 * When the window is loaded:
	 
	 $( window ).on('load', function() {
		
	});
	 
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practice to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
