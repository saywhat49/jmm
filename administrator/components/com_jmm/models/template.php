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
		$templateFolder=JPATH_SITE.'/components/com_jmm/templates/'.$item->title;
		$item->php=$this->getFileContent($templateFolder.'/index.php');
		$item->css=$this->getFileContent($templateFolder.'/css/default.css');
		$item->js=$this->getFileContent($templateFolder.'/js/custom.js');
		return $item;
	}
	protected function loadFormData()
	{
		$data=Joomla\CMS\Factory::getApplication()->getUserState('com_jmm.edit.template.data',array());
		
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
