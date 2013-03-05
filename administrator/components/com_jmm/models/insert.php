<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');
class JMMModelInsert extends JModelAdmin
{
	public function getTable($type='Insert',$prefix='JMMTable',$config=array())
	{
		return JTable::getInstance($type,$prefix,$config);		
	}	
	
	protected function loadFormData()
	{
		$data=JFactory::getApplication()->getUserState('com_jmm.edit.insert.data',array());
		
		if(empty($data)){
			$data=$this->getItem();
		}
		return $data;
	}
	
	public function getForm($data=array(),$loadData=true) 
	{
		$form=$this->loadForm('com_jmm.insert','insert',array('control'=>'jform','load_data'=>$loadData));
		return $form;
	}

	function getDatabases() {
		$rows = JMMCommon::getDataBaseLists();
		$databases = array();
		for ($i = 0; $i < count($rows); $i++) {
			$databases[] = JHTML::_('select.option', $rows[$i], $rows[$i]);
		}
		return $databases;
	}

	
	function getTables() {
		$rows = JMMCommon::getTablesFromDB();
		$tables = array();
		for ($i = 0; $i < count($rows); $i++) {
			$tables[] = JHTML::_('select.option', $rows[$i], $rows[$i]);
		}
		return $tables;
	}
	
}
