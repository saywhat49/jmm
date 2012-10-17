<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewSql extends JView {

	protected $items;

	function display($tmpl = null) {
		$document=JFactory::getDocument();
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'jquery-1.7.2.min.js');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'sql.js');
		$this -> items=$this->get('Items');
		$this->addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Run SQL Queries', 'sql.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
