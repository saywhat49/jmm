<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modeladmin');
class JMMModelTemplate extends JModelAdmin
{
	public function getTable($type='Template',$prefix='JMMTable',$config=array())
	{
		return JTable::getInstance($type,$prefix,$config);		
	}	
	public function getItem(){
		$item=parent::getItem();
		$templateFolder=JPATH_SITE.DS.'components'.DS.'com_jmm'.DS.'templates'.DS.$item->title;
		$item->php=$this->getFileContent($templateFolder.DS.'index.php');
		$item->css=$this->getFileContent($templateFolder.DS.'css'.DS.'default.css');
		$item->js=$this->getFileContent($templateFolder.DS.'js'.DS.'custom.js');
		return $item;
	}
	protected function loadFormData()
	{
		$data=JFactory::getApplication()->getUserState('com_jmm.edit.template.data',array());
		
		if(empty($data)){
			$data=$this->getItem();
		}
		return $data;
	}
	
	public function getForm($data=array(),$loadData=true) 
	{
		$form=$this->loadForm('com_jmm.template','template',array('control'=>'jform','load_data'=>$loadData));
		return $form;
	}

	function getFileContent($filePath){
		if(file_exists($filePath)){
			return file_get_contents($filePath);
		}else{
			return "";
		}
	}
	
}
