<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class JMMControllerCreateTable extends JControllerAdmin
{
	private $posts=array();
	function createTableStructure(){
		$result=array();
		$mainframe=JFactory::getApplication();
		$this->posts=$_POST;
		$totalfields=count($_POST['field_name']);
		$query='CREATE TABLE IF NOT EXISTS `'.$this->getTableName().'` (';
		$query.="\n";
		for($i=0;$i<$totalfields;$i++){
			$query.='`'.$this->getFieldName($i).'`'.$this->getFieldType($i).$this->getFieldLength($i).$this->getNull($i).$this->getAutoIncrements($i).$this->getFieldComments($i).',';
			//$query.="\n";
		}
		$query=rtrim($query,',');
		$query.=$this->getTableKeys();
		$query.=')'.' ENGINE='.$this->getTableType().' DEFAULT CHARSET=latin1'.$this->getTableComment().$this->getAutoIncrementCounter();
		$db = JMMCommon::getDBInstance();
		$db->setQuery($query);
		if($db->query()){
			$result['status']=true;
			$result['msg']='Table '.$this->getTableName().' Created sucessfully';
		}else{
			$result['status']=false;
			$result['msg']=$db->getErrorMsg();
		}
		echo json_encode($result);
		$mainframe->close();
	}

	/*
	 
   CREATE TABLE IF NOT EXISTS `users` (
	  `id` int(10) NOT NULL AUTO_INCREMENT,
	  `username` varchar(150) NOT NULL,
	  `lastname` varchar(172) NOT NULL,
	  PRIMARY KEY (`id`,`username`),
	  UNIQUE KEY `lastname` (`lastname`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
	 */
	
	/**
	 * Get Null 
	 */
	function getNull($fieldIndex){
		if(isset($this->posts['field_null'][$fieldIndex])){
			return ' NULL';
		}else{
			return ' NOT NULL';
		}
	}

	/**
	 * Get Auto Increments
	 */
	function getAutoIncrements($fieldIndex){
		if(isset($this->posts['field_extra'][$fieldIndex])){
			return ' AUTO_INCREMENT ';
		}
	}
	/**
	 * Get Autoincrement Counter
	 */
	function getAutoIncrementCounter(){
		if(isset($this->posts['field_extra'][0])){
			return ' AUTO_INCREMENT=1 ;';
		}
	}
	/**
	 * Get Field Name
	 */
	function getFieldName($fieldIndex){
		return $this->posts['field_name'][$fieldIndex];
	}
	/**
	 * Get Field Length
	 */
	function getFieldLength($fieldIndex){
		if($this->posts['field_length'][$fieldIndex]>0){
			return '('.$this->posts['field_length'][$fieldIndex].')';
		}
	}
	/**
	 * Get Field Type
	 */
	function getFieldType($fieldIndex){
		return ' '.$this->posts['field_type'][$fieldIndex];
	}
	/**
	 * Get Field Comments
	 */
	function getFieldComments($fieldIndex){
		if(isset($this->posts['field_comments'][$fieldIndex]) && $this->posts['field_comments'][$fieldIndex]!=''){
			return ' COMMENT '."'".$this->posts['field_comments'][$fieldIndex]."'";
		}
	}
	/**
	 * Get Table name
	 */
	function getTableName(){
		return $this->posts['tbl_name'];
	}
	/**
	 * Get Engine name
	 */
	function getTableType(){
		return $this->posts['tbl_type'];
	}	
	/**
	 * Get Engine name
	 */
	function getTableComment(){
		if(isset($this->posts['tbl_comments']) && $this->posts['tbl_comments']!=''){
			return ' COMMENT '."'".$this->posts['tbl_comments']."'";
		}
	}
	/**
	 * Get Primary Keys
	 */
	function getTableKeys(){
		$keys='';
		$primaryFields=array();
		$uniqueFields=array();
		$indexFields=array();
		$fulltextFields=array();
		foreach($this->posts['field_key'] as $key=>$value){
			switch ($value) {
				case 'primary':
					$primaryFields[]=$key;
					break;
				case 'unique':
					$uniqueFields[]=$key;
					break;
				case 'index':
					$indexFields[]=$key;
					break;
				case 'fulltext':
					$fulltextFields[]=$key;
					break;				
				default:
					# code...
					break;
			}
		}
		
		if(in_array('primary',$this->posts['field_key'])){
			$keys=', PRIMARY KEY ('.$this->getFields($primaryFields).')';
			$keys.="\n";
		}
		
		if(in_array('unique',$this->posts['field_key'])){
			$keys.=', UNIQUE KEY '.$this->getFields($uniqueFields).' ('.$this->getFields($uniqueFields).')';
			$keys.="\n";
		}
		
		if(in_array('index',$this->posts['field_key'])){
			$keys.=', KEY '.$this->getFields($indexFields).' ('.$this->getFields($indexFields).')';
			$keys.="\n";
		}
		
		if(in_array('fulltext',$this->posts['field_key'])){
			$keys.=', FULLTEXT KEY '.$this->getFields($fulltextFields).' ('.$this->getFields($fulltextFields).')';
			$keys.="\n";
		}

		return $keys;
	}

	/**
	 * Get Fields
	 */
	function getFields($arr){
		$fields='';
		foreach($arr as $index){
			$fields.='`'.$this->getFieldName($index).'`,';
		}
		return rtrim($fields,',');
	}

}