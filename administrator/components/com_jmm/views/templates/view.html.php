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
class JMMViewTemplates extends HtmlView {

	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tmpl = null) {
		$document=Joomla\CMS\Factory::getDocument();
		$document->addScript(JURI::root().'media/com_jmm/js/jquery-1.7.2.min.js');
		$document->addScript(JURI::root().'media/com_jmm/js/export.js');
		$this -> items = $this -> get('Items');
		$this -> pagination = $this -> get('Pagination');
		$this -> state = $this -> get('State');
		$this -> addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Templates', 'template.png');
		JToolBarHelper::addNew('template.add');
		JToolBarHelper::editList('template.edit');
		
		JToolBarHelper::divider();
		JToolBarHelper::publishList('templates.publish');
		JToolBarHelper::unpublishList('templates.unpublish');
	
		JToolBarHelper::divider();
		
		
		JToolBarHelper::archiveList('templates.archive');
		JToolBarHelper::trash('templates.trash');
		JToolBarHelper::deleteList('Are you sure you want to delete this Site Table?', 'templates.delete' );
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_jmm');
	}

}
