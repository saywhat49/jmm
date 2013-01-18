<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewCreateTable extends JView {

	protected $databases;
	
	function display($tmpl = null) {
		$this -> addToolbar();
		$document=JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'createtable.css');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'jquery-1.7.2.min.js');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'createtable.js');
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Tables', 'tables.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
