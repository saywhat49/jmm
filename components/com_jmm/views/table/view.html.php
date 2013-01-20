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
	function display($tmpl=null) 
	{	

		$this->rows = $this -> get('Items');
		$this->pagination =$this->get('Pagination');
		$this->columns=$this->get('Columns');
		$this->params=JFactory::getApplication()->getParams();
        $this->theme=$this->params->get('theme');
		$document=JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'jmm.css');
        parent::display($tmpl);
	}
}
