<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewCannedQueries extends JView {

	function display($tmpl = null) {
		$this -> addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Canned Queries', 'cannedqueries.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
