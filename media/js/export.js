var JQ = jQuery.noConflict();
JQ('document').ready(function(){
JQ('.loading-icon').hide();
/**	
 * On Click Export to CSV
 */
JQ('#export_as_csv').live('click',(function(){
	var id=JQ(this).parent().parent().attr('id');
	var filename=JQ('#'+id+' td:eq(2)').html();
	var dbname=JQ('#'+id+' td:eq(3)').html();
	var query=JQ('#'+id+' td:eq(4)').html();
		JQ('.loading-icon').show();
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
}));
/**	
 * On Click Export to CSV
 */
JQ('#export_as_csv_from_table').live('click',(function(){
	var filename=JQ(this).attr('filename');
	var dbname=JQ(this).attr('dbname');
	var query=JQ(this).attr('query');
		JQ('.loading-icon').show();
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
}));




});