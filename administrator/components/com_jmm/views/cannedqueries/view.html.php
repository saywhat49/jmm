<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * Canned Queries View for JMM component
 */
class JMMViewCannedQueries extends HtmlView {

	/**
	 * The canned queries items
	 *
	 * @var    array
	 */
	protected $items;
	
	/**
	 * The pagination object
	 *
	 * @var    object
	 */
	protected $pagination;
	
	/**
	 * The model state
	 *
	 * @var    object
	 */
	protected $state;
	
	/**
	 * The available databases
	 *
	 * @var    array
	 */
	protected $databases;
	
	/**
	 * Display the view
	 *
	 * @param   string  $tmpl  The template file to use
	 *
	 * @return  void
	 */
function display($tmpl = null) {
    // Utiliser WebAsset Manager au lieu d'addScript direct
    $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
    $wa->useScript('jquery');
    
    $document = Factory::getDocument();
    $document->addScript(Uri::root().'media/com_jmm/js/export.js');
    
    $this->items = $this->get('Items');
    $this->pagination = $this->get('Pagination');
    $this->state = $this->get('State');
    $this->databases = $this->get('Databases');
    
    $this->addToolbar();
    parent::display($tmpl);
}

	/**
	 * Add the page title and toolbar
	 *
	 * @return  void
	 */
	public function addToolbar() {
		ToolbarHelper::title(Text::_('COM_JMM_CANNED_QUERIES'), 'cannedqueries.png');
		ToolbarHelper::addNew('cannedquery.add');
		ToolbarHelper::editList('cannedquery.edit');
		
		ToolbarHelper::divider();
		ToolbarHelper::publishList('cannedqueries.publish');
		ToolbarHelper::unpublishList('cannedqueries.unpublish');
	
		ToolbarHelper::divider();
		
		ToolbarHelper::archiveList('cannedqueries.archive');
		ToolbarHelper::trash('cannedqueries.trash');
		ToolbarHelper::deleteList(Text::_('COM_JMM_SITETABLES_SURE_TO_DELETE'), 'cannedqueries.delete');
		ToolbarHelper::divider();
		ToolbarHelper::preferences('com_jmm');
	}
}
