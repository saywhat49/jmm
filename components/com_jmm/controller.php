<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
class JMMController extends JController {

	public function getItems(){		
		$arr=array();
		$arr['Result']="FALSE";
		$arr['TotalRecordCount']=0;
		$arr['Records']=array();
		$siteTableId=JRequest::getInt('site_table_id',0);
		$model=$this->getModel('Table');
		$siteTableDetails=$model->getSiteTableDetails($siteTableId);
		$jtStartIndex=JRequest::getInt('jtStartIndex',0);
		$jtPageSize=JRequest::getInt('jtPageSize',25);
		$jtSorting=JRequest::getString('jtSorting',null);	
		if(isset($siteTableDetails)){
			$dbname=$siteTableDetails->dbname;
			$db=JMMCommon::getDBInstance(null,null,null,null, $dbname,null);
			$query=$siteTableDetails->query;
			$db->setQuery($query);
			$db->query();
			$total=$db->getNumRows();
			if(!empty($jtSorting)){
				$query.=" ORDER BY $jtSorting";
			}
			$query.=" LIMIT $jtStartIndex,$jtPageSize";	
			$db->setQuery($query);	
			$items = $db -> loadAssocList();
			$arr['Result']="OK";
			$arr['TotalRecordCount']=$total;
			$arr['Records']=$items;
		}
		echo json_encode($arr);
		JFactory::getApplication()->close();
	}

}
