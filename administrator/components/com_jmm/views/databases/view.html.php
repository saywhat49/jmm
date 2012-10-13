<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewDatabases extends JView
{

	function display($tmpl = null) {
		$this -> items = JMMCommon::showDatabaseLists();		
		$this -> pagination = $this -> get('Pagination');
		$this -> state = $this -> get('State');
		$this -> addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Databases', 'databases.png');
		JToolBarHelper::preferences('com_jmm');
	}
	
}
