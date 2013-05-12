<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
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
	protected $themeBaseURL;
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
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'jquery-1.7.2.min.js');		
		$this->curThemeURL=JURI::root().'components'.DS.'com_jmm'.DS.'templates'.DS.$this->theme;
		$this->themeBaseURL=JURI::root().'components/com_jmm/templates/'.$this->theme;
        parent::display($tmpl);
	}
}
