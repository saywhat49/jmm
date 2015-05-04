<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMViewTable extends JViewLegacy
{
	protected $rows;
	protected $columns;
	protected $pagination;
	protected $theme;
	protected $params;
	protected $curThemeURL;
	protected $siteTableDetails;
	protected $defaultPagination;
	protected $templateBaseURL;
	function display($tmpl=null) 
	{	
		
		$this->params=JFactory::getApplication()->getParams();
		$siteTableId=0;
		if((int)$this->params->get('site_table_id')){
    		$siteTableId=(int)$this->params->get('site_table_id');
  		} 
  		if($siteTableId<1){
  			throw new Exception("Site table not found or you dont have permission to access", 404);
  			
  		}
		$this->rows = $this -> get('Items');
		$this->pagination =$this->get('Pagination');
		$this->columns=$this->get('Columns');
		$this->siteTableDetails=$this->get('SiteTableDetails');	
		$user_access_levels=JFactory::getUser()->getAuthorisedViewLevels();
		if($this->siteTableDetails->access_level && !in_array($this->siteTableDetails->access_level, $user_access_levels)){
			throw new Exception("you dont have permission to access", 404);
		}
		$this->defaultPagination=true;	
        $this->theme=$this->params->get('theme');
		$document=JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'jmm.css');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'jquery-1.9.0.min.js');		
		$this->curThemeURL=JURI::root().'components'.DS.'com_jmm'.DS.'templates'.DS.$this->theme;
		$this->templateBaseURL=JURI::root().'components/com_jmm/templates/'.$this->theme;
        parent::display($tmpl);
	}
}
