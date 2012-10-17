<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewSql extends JView {

	protected $items;

	function display($tmpl = null) {
		$this -> items=$this->get('Items');
		$this->addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Run SQL Queries', 'sql.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
