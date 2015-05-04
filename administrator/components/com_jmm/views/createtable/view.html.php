<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMViewCreateTable extends JViewLegacy {

	protected $databases;
	
	function display($tmpl = null) {
		$this -> addToolbar();
		$this->sidebar = JHtmlSidebar::render();
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
