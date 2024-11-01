(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practice to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 $( window ).on('load', function() {
		if ($('#yesnology_companySelect option').length === 1) $('#loginSuccess').html('<p style="color: red">Not logged in!</p>');
		$("#yesnology_binder").change(function() {
			let binderValue = "[ynlgy_binder binder='" + $("#yesnology_binder").val() + "']"
			$('#shortcode').html('<h3>' + yesnologyobject.yesnologyShortcodeOk + '</h3><input id="shortcodeValue" readonly value="' + binderValue + '" class="admin-input" style="margin: 5px"><button onclick="var content=document.getElementById(\'shortcodeValue\'); content.select(); content.setSelectionRange(0, 99999); navigator.clipboard.writeText(\'content.value\').then(() => {alert(\'Copied\');},  () => {alert(\'Not copied\');});">' + yesnologyobject.yesnologyCopyButton + '</button>')
		  });
		$("#yesnology_companySelect").change(function() {
			$('#yesnology_tenatId').val(JSON.parse($("#yesnology_companySelect").val()).tenantId)
			$('#yesnology_companyId').val(JSON.parse($("#yesnology_companySelect").val()).companyId)
		  });
	});
   
})( jQuery );
