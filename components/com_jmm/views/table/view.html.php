<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewTable extends JView
{
	protected $items;
	function display($tmpl=null) 
	{
		$model=$this->getModel();
		$params=JFactory::getApplication()->getParams();
		$siteTableId=$params->get('site_table_id');
		$table_pagination=$params->get('table_pagination');
		$no_record_per_page=$params->get('no_record_per_page');
		$siteTableDetails=$model->getSiteTableDetails($siteTableId);
		$dbname=$siteTableDetails->dbname;
		$query=$siteTableDetails->query;
		$db=JMMCommon::getDBInstance(null,null,null,null, $dbname,null);
		$db->setQuery($query);		
		$this->items = $db -> loadAssocList();
		$document=JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'jmm.css');
        parent::display($tmpl);
	}
}
