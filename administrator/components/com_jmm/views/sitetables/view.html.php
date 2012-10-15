<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewSiteTables extends JView {

	function display($tmpl = null) {
		$this -> addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Site Tables', 'sitetable.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
