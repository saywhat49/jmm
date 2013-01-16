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
				var option='<option value="'+data.row.query+'">'+data.row.title+'</option>';
				JQ('#opt-canned').prepend(option);
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
		var r=Math.random();
		JQ.post('index.php',{option:'com_jmm',task:'saveSiteTable',title:title,dbname:dbname,query:query,r:r},function(response){
			JQ('.loading-icon').hide();
			var data=JSON.parse(response);
			if(data.status){
				var option='<option value="'+data.row.query+'">'+data.row.title+'</option>';
				JQ('#opt-stbl').prepend(option);
				alert(data.msg);
			}else{
				alert(data.msg);				
			}
		});
	}
	
}));

/**	
 * On Click Export to CSV
 */
JQ('#export_as_csv').live('click',(function(){
	var filename=prompt("Enter File Name","csvFileName");
	if (filename!=null && filename!=""){
		JQ('.loading-icon').show();
		var query=JQ('#query').val();
		var dbname=JQ('#currentdb').val();
		var r=Math.random();
		JQ.post('index.php',{option:'com_jmm',task:'export.csv',filename:filename,dbname:dbname,query:query,r:r},function(response){
			JQ('.loading-icon').hide();
			try{				
				var data=JSON.parse(response);
			}catch(e){
				alert("Exception is Parsing JSON");
				return false;
			}
			if(data.status){
				var downloadURL=decodeURI(data.download_url);
				window.location=downloadURL;
				console.log(downloadURL);
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