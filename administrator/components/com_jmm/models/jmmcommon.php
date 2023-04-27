<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
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
	 * Get Database Default Settings
	 */
	static function getDBInstance($driver = null, $host = null, $user = null, $password = null, $dbname = null, $prefix = null) {
		$app = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_jmm');
		$dbsettings = $params -> get('dbsettings');
		if ($dbsettings == 1) {
			$driver = $app -> getCfg('dbtype');
			$host = $params -> get('dbhost');
			$user = $params -> get('dbusername');
			$password = $params -> get('dbpass');
			if (isset($_REQUEST['dbname'])) {
				$dbname = JRequest::getVar('dbname');
			} else {
				$dbname = $params -> get('dbname');
			}
			$prefix = $params -> get('dbprefix');
		} else {
			if (!isset($driver)) {
				$driver = $app -> getCfg('dbtype');
			}
			if (!isset($host)) {
				$host = $app -> getCfg('host');
			}
			if (!isset($user)) {
				$user = $app -> getCfg('user');
			}
			if (!isset($password)) {
				$password = $app -> getCfg('password');
			}
			if (!isset($dbname)) {
				if (isset($_REQUEST['dbname'])) {
					$dbname = JRequest::getVar('dbname');
				} else {
					$dbname = $app -> getCfg('db');
				}

			}
			if (!isset($prefix)) {
				$prefix = $app -> getCfg('dbprefix');
			}
		}
		/**
		 * If User Use Custom DB Configuration
		 */

		$option = array();
		$option['driver'] = $driver;
		$option['host'] = $host;
		$option['user'] = $user;
		$option['password'] = $password;
		$option['database'] = $dbname;
		$option['prefix'] = $prefix;
		$db = JDatabase::getInstance($option);

		if ($dbname == '') {
			$dbLists=self->getDataBaseLists($db);
			if(count($dbLists)>0){
				JFactory::getApplication() -> redirect('index.php?option=com_jmm&view=tables&dbname='.$dbLists[0],'DataBase Switched to '.$dbLists[0]);
			}
			
		}
		return $db;

	}

	/**
	 * Get Tables From Database
	 */
	static function getTablesFromDB($db = null) {
		if (!isset($db)) {
			$db = JMMCommon->getDBInstance();
		}
		$query = "SHOW TABLE STATUS";
		$db -> setQuery($query);
		$rows = $db -> loadAssocList();
		$cols = array();
		foreach ($rows as &$row) {
			$cols[] = $row['Name'];
		}
		return $cols;
	}

	/**
	 * Get Cloumn Lists From Tablename
	 */
	static function getCloumnsFromTable($table, $db = null) {
		if (!isset($db)) {
			$db = JMMCommon->getDBInstance();
		}
		$query = "SHOW COLUMNS FROM `$table`";
		$db -> setQuery($query);
		$rows = $db -> loadAssocList();
		$cols = array();
		foreach ($rows as &$row) {
			$cols[] = $row['Field'];
		}
		return $cols;
	}

	/**
	 * List Databases
	 */
	static function getDataBaseLists($db = null) {
		if (!isset($db)) {
			$db = JMMCommon->getDBInstance();
		}
		$query = "SHOW DATABASES";
		$db -> setQuery($query);
		$rows = $db -> loadAssocList();
		$database = array();
		for ($i = 0; $i < count($rows); $i++) {
			$row = &$rows[$i];
			foreach ($row as $key => &$val) {
				$database[] = $val;
			}
		}
		return $database;
	}

	/**
	 * Show Databases
	 */

	 static function showDatabaseLists($db = null) {
		if (!isset($db)) {
			$db = JMMCommon::getDBInstance();
		}
		$query = "SHOW DATABASES";
		$db -> setQuery($query);
		$rows = $db -> loadAssocList();
		for ($i = 0; $i < count($rows); $i++) {
			$row = &$rows[$i];
			foreach ($row as $key => &$val) {
				$val = '<a href="index.php?option=com_jmm&view=tables&dbname=' . $val . '">' . $val . '</a>';
			}
		}
		return $rows;
	}

	/**
	 * Show Tables Lists
	 */
	static function showTableLists($db = null) {
		if (!isset($db)) {
			$db = JMMCommon->getDBInstance();
		}
		if (isset($_REQUEST['dbname'])) {
			$dbname = JRequest::getVar('dbname');
			$urlString = '&dbname=' . $dbname;
			$defaultdbName='dbname="'.$dbname.'"';
		} else {
			$urlString = '';
			$defaultdbName='';
		}
		$query = "SHOW TABLE STATUS";
		$db -> setQuery($query);
		$rows = $db -> loadAssocList();
		foreach ($rows as &$row) {
			$tblName = $row['Name'];
			$row['Structure'] = '<a class="btn btn-success" href="index.php?option=com_jmm&view=tables&action=structure&&tbl=' . $tblName . $urlString . '">Structure</a>';
			$row['Browse']= '<a class="btn btn-success" href="index.php?option=com_jmm&view=tables&action=browse&&tbl=' . $tblName . $urlString . '">Browse</a>';
			$row['Export']= '<input type="button" class="btn btn-success" id="export_as_csv_from_table" value="Export as CSV" filename="'.$tblName.'" query="SELECT * FROM `'.$tblName.'`" '.$defaultdbName.'>';
			$row['Name'] = '<a href="index.php?option=com_jmm&view=tables&action=browse&tbl=' . $tblName . $urlString . '">' . $tblName . '</a>';
		}

		return $rows;
	}

	/**
	 * Show Table Structure
	 */

	 static function showTableStructure($table=null, $db = null) {
		if (!isset($db)) {
			$db = JMMCommon->getDBInstance();
		}
		if(!isset($table)){
			return false;
		}
		$query = "DESC $table";
		$db -> setQuery($query);
		$rows = $db -> loadAssocList();
		for ($i = 0; $i < count($rows); $i++) {
			$row = &$rows[$i];
			foreach ($row as $key => &$val) {
				//$row['Browse']='<a href="index.php?option=com_jmm&view=tables&action=structure&&tbl='.$val.'">Edit</a>';
				//$row['Structure']='<a href="index.php?option=com_jmm&view=tables&action=browse&tbl='.$val.'">Delete</a>';
			}
		}
		return $rows;
	}

	/**
	 * Display Data From Table
	 */
	static function showDataFromTable($table, $db = null) {
		if (!isset($db)) {
			$db = JMMCommon->getDBInstance();
		}
		$query = "SELECT * FROM $table";
		$db -> setQuery($query);
		$rows = $db -> loadAssocList();
		for ($i = 0; $i < count($rows); $i++) {
			$row = &$rows[$i];
			foreach ($row as $key => &$val) {
				//$row['Browse']='<a href="index.php?option=com_jmm&view=tables&action=structure&&tbl='.$val.'">Edit</a>';
				//$row['Structure']='<a href="index.php?option=com_jmm&view=tables&action=browse&tbl='.$val.'">Delete</a>';
			}
		}
		return $rows;
	}

	/**
	 * Lists Canned Queries
	 */

	static function listCannedQueries($db = null) {
		if (!isset($db)) {
			//$db = JMMCommon->getDBInstance();
			$db = JFactory::getDBO();
		}
		$selecteddb = '';
		if (isset($_REQUEST['dbname'])) {
			$selecteddb = JRequest::getVar('dbname');
		} else {
			$selecteddb = JFactory::getApplication() -> getCfg('db');
		}
		$query = "SELECT * FROM `#__jmm_canned_queries` WHERE `dbname`='$selecteddb' AND `published`=1 ORDER BY `id` DESC";
		$db -> setQuery($query);
		$rows = $db -> loadObjectList();
		return $rows;
	}

	/**
	 * Lists Site Tables Queries
	 */

	static function listSiteTables($db = null) {
		if (!isset($db)) {
			//$db = JMMCommon->getDBInstance();
			$db = JFactory::getDBO();
		}
		$selecteddb = '';
		if (isset($_REQUEST['dbname'])) {
			$selecteddb = JRequest::getVar('dbname');
		} else {
			$selecteddb = JFactory::getApplication() -> getCfg('db');
		}
		$query = "SELECT * FROM `#__jmm_sitetables` WHERE `dbname`='$selecteddb' AND `published`=1 ORDER BY `id` DESC";
		$db -> setQuery($query);
		$rows = $db -> loadObjectList();
		return $rows;
	}

	/**
	 * Get Model
	 */

	static function getModel($modelName, $prefix = null, $backend = true) {
		if (!isset($prefix)) {
			$prefix = 'JMMModel';
		}
		JLoader::import('joomla.application.component.model');
		if ($backend) {
			JLoader::import($modelName, JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jmm' . DS . 'models');
		} else {
			JLoader::import($modelName, JPATH_SITE . DS . 'components' . DS . 'com_jmm' . DS . 'models');
		}
		$model = JModelLegacy::getInstance($modelName, $prefix);
		return $model;

	}

}
