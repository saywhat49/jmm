
<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.jmodel');
class JMMModelTable extends JModel
{

	var $_total = null;
	var $_pagination = null;
	/**
	 * [__construct description]
	 */
	  function __construct(){
        parent::__construct();
        $mainframe = JFactory::getApplication();
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
  }
  /**
   * Get Site table details
   * @param  [type] $siteTableId [description]
   * @return [type]              [description]
   */
  private function getSiteTableDetails($siteTableId=null){
  	$siteTableId=intval($siteTableId);
  	$db=JFactory::getDBO();
	$db->setQuery("SELECT * FROM #__jmm_sitetables WHERE `id`='$siteTableId' AND `published`='1'");
	$row = $db -> loadObject();
	return $row;
  }

	/**
	 * [getTotal description]
	 * @return [type] [description]
	 */
    function getTotal(){
        if (empty($this->_total)) {
  			$params=JFactory::getApplication()->getParams();
        	$siteTableId=$params->get('site_table_id');
        	$siteTableDetails=$this->getSiteTableDetails($siteTableId);
		  	$dbname=$siteTableDetails->dbname;
			$query=$siteTableDetails->query;
			$db=JMMCommon::getDBInstance(null,null,null,null, $dbname,null);
			$db->setQuery($query);	
            $this->_total =count($db->loadObjectList());      
        }
        return $this->_total;
  	}

  	/**
  	 * [getPagination description]
  	 * @return [type] [description]
  	 */
  	function getPagination(){
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
  	}
  /**
   * [getItems description]
   * @return [type] [description]
   */
  function getItems(){
  	$params=JFactory::getApplication()->getParams();
	$siteTableId=$params->get('site_table_id');
	$table_pagination=$params->get('table_pagination');
	$no_record_per_page=$params->get('no_record_per_page');
	$this->setState('limit',$no_record_per_page);
	$siteTableDetails=$this->getSiteTableDetails($siteTableId);
  	$dbname=$siteTableDetails->dbname;
	$query=$siteTableDetails->query;
	$db=JMMCommon::getDBInstance(null,null,null,null, $dbname,null);

	if($table_pagination){
		$query.=' LIMIT '.$this->getState('limitstart').','.$this->getState('limit');
	}
	$db->setQuery($query);		
	$items = $db -> loadAssocList();
	return $items;
  }

}