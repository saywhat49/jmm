<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewTable extends JView
{
	protected $items;
	protected $pagination;
	function display($tmpl=null) 
	{	

		$this->items = $this -> get('Items');
		$this->pagination =$this->get('Pagination');
		$document=JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'jmm.css');
        parent::display($tmpl);
	}
}
