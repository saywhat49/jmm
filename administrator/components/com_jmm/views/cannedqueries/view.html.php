<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewCannedQueries extends JView {

	protected $items;
	protected $pagination;
	protected $state;
	protected $databases;
	
	function display($tmpl = null) {
		$document=JFactory::getDocument();
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'jquery-1.7.2.min.js');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'export.js');
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
