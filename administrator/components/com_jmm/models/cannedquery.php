<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');
class JMMModelCannedQuery extends JModelAdmin
{
	public function getTable($type='CannedQuery',$prefix='JMMTable',$config=array())
	{
		return JTable::getInstance($type,$prefix,$config);		
	}	
	
	protected function loadFormData()
	{
		$data=Joomla\CMS\Factory::getApplication()->getUserState('com_jmm.edit.cannedquery.data',array());
		
		if(empty($data)){
			$data=$this->getItem();
		}
		return $data;
	}
	
	public function getForm($data=array(),$loadData=true) 
	{
		$form=$this->loadForm('com_jmm.cannedquery','cannedquery',array('control'=>'jform','load_data'=>$loadData));
		return $form;
	}
	
}
