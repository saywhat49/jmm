<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
('Restricted access');
class JMMViewSiteTables extends HtmlView {
	
	protected $items;
	protected $pagination;
	protected $state;
	protected $databases;
	
	function display($tmpl = null) {
		$document=Joomla\CMS\Factory::getDocument();
		$document->addScript(JURI::root().'media/com_jmm/js/jquery-1.7.2.min.js');
		$document->addScript(JURI::root().'media/com_jmm/js/export.js');
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
