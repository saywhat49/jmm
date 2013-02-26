<?php
mysql_connect("localhost","root","rootwdp");
mysql_select_db("foneonca_tempdev1");
$jtStartIndex=(int)$_REQUEST['jtStartIndex'];
$jtPageSize=(int)$_REQUEST['jtPageSize'];
$jtSorting=$_REQUEST['jtSorting'];
if($jtPageSize==0){
	$jtPageSize=25;
}
$sql="SELECT * FROM gp_users as u LEFT JOIN gp_user_profile as up ON u.id=up.user_id";
/**
 * Calculating Total Num of Record
 */
$result=mysql_query($sql);
$total=mysql_num_rows($result);
/**
 * Total Calculated
 */
$sql.=" ORDER BY $jtSorting LIMIT $jtStartIndex,$jtPageSize";
$result=mysql_query($sql);
$rows=array();
while($row=mysql_fetch_array($result)){
$rows[]=$row;
}
$arr['Result']="OK";
$arr['TotalRecordCount']=$total;
$arr['Records']=$rows;
echo json_encode($arr);