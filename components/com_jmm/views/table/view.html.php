<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewTable extends JView
{
	protected $rows;
	protected $columns;
	protected $pagination;
	protected $theme;
	protected $params;
	protected $curThemeURL;
	protected $siteTableDetails;
	protected $defaultPagination;
	function display($tmpl=null) 
	{	

		$this->rows = $this -> get('Items');
		$this->pagination =$this->get('Pagination');
		$this->columns=$this->get('Columns');
		$this->siteTableDetails=$this->get('SiteTableDetails');	
		$this->defaultPagination=true;	
		$this->params=JFactory::getApplication()->getParams();
        $this->theme=$this->params->get('theme');
		$document=JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'jmm.css');
		$this->curThemeURL=JURI::root().'components'.DS.'com_jmm'.DS.'themes'.DS.'sitetables'.DS.$this->theme;
        parent::display($tmpl);
	}
}
