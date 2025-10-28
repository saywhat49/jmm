<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
('Restricted access');
class JMMViewTable extends HtmlView
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
		$this->params=Joomla\CMS\Factory::getApplication()->getParams();
        $this->theme=$this->params->get('theme');
		$document=Joomla\CMS\Factory::getDocument();
		$document->addStyleSheet(JURI::root().'media/com_jmm/css/jmm.css');
		$document->addScript(JURI::root().'media/com_jmm/js/jquery-1.7.2.min.js');		
		$this->curThemeURL=JURI::root().'components/com_jmm/templates/'.$this->theme;
		$this->themeBaseURL=JURI::root().'components/com_jmm/templates/'.$this->theme;
        parent::display($tmpl);
	}
}
