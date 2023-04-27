<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMViewDatabases extends JViewLegacy
{

	function display($tmpl = null) {
		$this -> items = JMMCommon->showDatabaseLists();		
		$this -> pagination = $this -> get('Pagination');
		$this -> state = $this -> get('State');
		$this -> addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Databases', 'databases.png');
		JToolBarHelper::preferences('com_jmm');
	}
	
}
