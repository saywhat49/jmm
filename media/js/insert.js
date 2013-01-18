var JQ = jQuery.noConflict();
JQ('document').ready(function(){
/**
 * Enable DateTime Picker
 */
JQ('.datetime').datetimepicker({
	dateFormat:"yy-mm-dd",
	formatTime:"HH:mm:ss",
	showSecond: true
});

/**
 * Enable DateTime Picker
 */
JQ('.timestamp').datetimepicker({
	dateFormat:"yy-mm-dd",
	formatTime:"HH:mm:ss",
	showSecond: true
});



});