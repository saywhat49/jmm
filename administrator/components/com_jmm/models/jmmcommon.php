<?php
class JMMCommon {
	public $db;
	public $tbl;
	public $dbname;
	public $action;
	/*################### Print Object ################*/
	function printObj($obj) {
		echo '<pre>';
		print_r($obj);
		echo '</pre><hr />';

	}
	/**
	 * Get Tables From Database
	 */
	 function getTablesFromDB($db=null){
	 	if(!isset($db)){
	 		$db=JFactory::getDBO();
	 	}
		$query="SHOW TABLE STATUS";
		$db->setQuery($query);
		$rows=$db->loadAssocList();
		$cols=array();
		foreach($rows as &$row){
			$cols[]=$row['Name'];
		}
		return $cols;
	 }
	 
	 
	/**
	 * Get Cloumn Lists From Tablename
	 */
	 function getCloumnsFromTable($table,$db=null){
	 	if(!isset($db)){
	 		$db=JFactory::getDBO();
	 	}
		$query="SHOW COLUMNS FROM `$table`";
		$db->setQuery($query);
		$rows=$db->loadAssocList();
		$cols=array();
		foreach($rows as &$row){
			$cols[]=$row['Field'];
		}
		return $cols;
	 }
	 
	/**
	 * Show Databases
	 */
	 
	 function showDatabaseLists($db=null){
	 	if(!isset($db)){
	 		$db=JFactory::getDBO();
	 	}
		$query="SHOW DATABASES";
		$db->setQuery($query);
		$rows=$db->loadAssocList();
		for($i=0;$i<count($rows);$i++){
			$row=&$rows[$i];
			foreach($row as $key=>&$val){				
				$val='<a href="index.php?option=com_jmm&view=tables&dbname='.$val.'">'.$val.'</a>';
			}
		}
		return $rows;
	 }	 
	/**
	 * Show Tables Lists
	 */
	 function showTableLists($db=null){
	 	if(!isset($db)){
	 		$db=JFactory::getDBO();
	 	}
		$query="SHOW TABLE STATUS";
		$db->setQuery($query);
		$rows=$db->loadAssocList();
		foreach ($rows as &$row) {
			$tblName=$row['Name'];
			$row['Action']='<a href="index.php?option=com_jmm&view=tables&action=structure&&tbl='.$tblName.'">Structure</a>';
			$row['Action'].='<a href="index.php?option=com_jmm&view=tables&action=browse&&tbl='.$tblName.'">Browse</a>';
			$row['Name']='<a href="index.php?option=com_jmm&view=tables&action=browse&tbl='.$tblName.'">'.$tblName.'</a>';
		}
		/*
		for($i=0;$i<count($rows);$i++){
			$row=&$rows[$i];
			foreach($row as $key=>&$val){
				$row['Structure']='<a href="index.php?option=com_jmm&view=tables&action=structure&&tbl='.$val.'">Structure</a>';
				$row['Browse']='<a href="index.php?option=com_jmm&view=tables&action=browse&tbl='.$val.'">Browse</a>';
				//$val='<a href="index.php?option=com_jmm&view=tables&action=browse&tbl='.$val.'">'.$val.'</a>';
			}
		}
		*/
		
		return $rows;
	 }
	 
	 /**
	  * Show Table Structure
	  */
	  
	  function showTableStructure($table,$db=null){
	  	if(!isset($db)){
	 		$db=JFactory::getDBO();
	 	}
		$query="DESC $table";
		$db->setQuery($query);
		$rows=$db->loadAssocList();
		for($i=0;$i<count($rows);$i++){
			$row=&$rows[$i];
			foreach($row as $key=>&$val){
				//$row['Browse']='<a href="index.php?option=com_jmm&view=tables&action=structure&&tbl='.$val.'">Edit</a>';
				//$row['Structure']='<a href="index.php?option=com_jmm&view=tables&action=browse&tbl='.$val.'">Delete</a>';
			}
		}
		return $rows;
	  }
	 /**
	  * Display Data From Table
	  */
	  function showDataFromTable($table,$db=null){
	  	if(!isset($db)){
	 		$db=JFactory::getDBO();
	 	}
		$query="SELECT * FROM $table";
		$db->setQuery($query);
		$rows=$db->loadAssocList();
		for($i=0;$i<count($rows);$i++){
			$row=&$rows[$i];
			foreach($row as $key=>&$val){
				//$row['Browse']='<a href="index.php?option=com_jmm&view=tables&action=structure&&tbl='.$val.'">Edit</a>';
				//$row['Structure']='<a href="index.php?option=com_jmm&view=tables&action=browse&tbl='.$val.'">Delete</a>';
			}
		}
		return $rows;
	  }
	/*############## Get Model ############*/
	function getModel($modelName, $prefix = null, $backend = true) {
		if (!isset($prefix)) {
			$prefix = 'POINTSModel';
		}
		JLoader::import('joomla.application.component.model');
		if ($backend) {
			JLoader::import($modelName, JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_points' . DS . 'models');
		} else {
			JLoader::import($modelName, JPATH_SITE . DS . 'components' . DS . 'com_points' . DS . 'models');
		}
		$model = JModel::getInstance($modelName, $prefix);
		return $model;

	}

}
