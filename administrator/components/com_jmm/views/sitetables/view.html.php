<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewSiteTables extends JView {
	
	protected $items;
	protected $pagination;
	protected $state;
	protected $databases;
	
	function display($tmpl = null) {
		$this -> items = $this -> get('Items');
		$this -> pagination = $this -> get('Pagination');
		$this -> state = $this -> get('State');
		$this->databases=$this->get('Databases');
		$this -> addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Site Tables', 'sitetable.png');
		JToolBarHelper::addNew('sitetable.add');
		JToolBarHelper::editList('sitetable.edit');
		
		JToolBarHelper::divider();
		JToolBarHelper::publishList('sitetables.publish');
		JToolBarHelper::unpublishList('sitetables.unpublish');
	
		JToolBarHelper::divider();
		
		
		JToolBarHelper::archiveList('sitetables.archive');
		JToolBarHelper::trash('sitetables.trash');
		JToolBarHelper::deleteList('Are you sure you want to delete this Site Table?', 'sitetables.delete' );
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_jmm');
	}

}
