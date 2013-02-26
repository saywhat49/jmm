
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
  public function getSiteTableDetails($siteTableId=null){
    if(isset($siteTableId)){
      $siteTableId=intval($siteTableId);
    }else{
      $params=JFactory::getApplication()->getParams();
      $siteTableId=(int)$params->get('site_table_id');
      if(!isset($siteTableId)){
        $siteTableId=JRequest::getInt('site_table_id');
      }
    }  	
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
        $siteTableId=(int)$params->get('site_table_id');
      if(!isset($siteTableId)){
        $siteTableId=JRequest::getInt('site_table_id');
      }
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
	$siteTableId=(int)$params->get('site_table_id');
  if(!isset($siteTableId)){
    $siteTableId=JRequest::getInt('site_table_id');
  }
	$table_pagination=(int) $params->get('table_pagination');
  if(!isset($table_pagination)){
    $table_pagination=JRequest::getInt('table_pagination',0);
  }
	$no_record_per_page=(int) $params->get('no_record_per_page');
  if(!isset($no_record_per_page)){
    $no_record_per_page=JRequest::getInt('no_record_per_page',10);
  }
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

  function getColumns(){
    $rows=$this->getItems();
    $cols=$rows[0];
    return array_keys($cols);
  }

}