<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewCannedQueries extends JView {

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
		JToolBarHelper::title('Canned Queries', 'cannedqueries.png');
		JToolBarHelper::addNew('cannedquery.add');
		JToolBarHelper::editList('cannedquery.edit');
		
		JToolBarHelper::divider();
		JToolBarHelper::publishList('cannedqueries.publish');
		JToolBarHelper::unpublishList('cannedqueries.unpublish');
	
		JToolBarHelper::divider();
		
		
		JToolBarHelper::archiveList('cannedqueries.archive');
		JToolBarHelper::trash('cannedqueries.trash');
		JToolBarHelper::deleteList('Are you sure you want to delete this Site Table?', 'cannedqueries.delete' );
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_jmm');
	}

}
