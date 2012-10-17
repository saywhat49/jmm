<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');
class JMMModelSiteTable extends JModelAdmin
{
	public function getTable($type='SiteTable',$prefix='JMMTable',$config=array())
	{
		return JTable::getInstance($type,$prefix,$config);		
	}	
	
	protected function loadFormData()
	{
		$data=JFactory::getApplication()->getUserState('com_jmm.edit.sitetbl.data',array());
		
		if(empty($data)){
			$data=$this->getItem();
		}
		return $data;
	}
	
	public function getForm($data=array(),$loadData=true) 
	{
		$form=$this->loadForm('com_jmm.sitetable','sitetable',array('control'=>'jform','load_data'=>$loadData));
		return $form;
	}
	
}
