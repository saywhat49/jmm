var JQ = jQuery.noConflict();
JQ('document').ready(function(){
JQ('.loading-icon').hide();
/**	
 * On Click Save As Canned Query
 */
JQ('#save_as_canned_query').live('click',(function(){
	var title=prompt("Enter Canned Query Title");
	if (title!=null && title!=""){
		JQ('.loading-icon').show();
		var query=JQ('#query').val();
		var dbname=JQ('#currentdb').val();
		var r=Math.random();
		JQ.post('index.php',{option:'com_jmm',task:'saveCannedQuery',title:title,dbname:dbname,query:query,r:r},function(response){
			JQ('.loading-icon').hide();
			var data=JSON.parse(response);
			if(data.status){
				alert(data.msg);
			}else{
				alert(data.msg);				
			}
		});
	}
}));
/**
 * On Click Save as Site Table
 */
JQ('#save_as_site_table').live('click',(function(){
	var title=prompt("Enter Canned Query Title");
	if (title!=null && title!=""){
		JQ('.loading-icon').show();
		var query=JQ('#query').val();
		var dbname=JQ('#currentdb').val();
		JQ.post('index.php',{option:'com_jmm',task:'saveSiteTable',title:title,dbname:dbname,query:query,r:r},function(response){
			JQ('.loading-icon').hide();
			var data=JSON.parse(response);
			if(data.status){
				alert(data.msg);
			}else{
				alert(data.msg);				
			}
		});
	}
	
}));


/**
 * DOM Ready Ends
 */

});