
<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.jmodel');
class JMMModelTable extends JModel
{
  function getSiteTableDetails($siteTableId=null){
  	$siteTableId=intval($siteTableId);
  	$db=JFactory::getDBO();
	$db->setQuery("SELECT * FROM #__jmm_sitetables WHERE `id`='$siteTableId' AND `published`='1'");
	$rows = $db -> loadObject();
	return $rows;
  }
}