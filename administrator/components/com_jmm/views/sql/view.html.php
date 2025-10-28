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
use Joomla\CMS\Router\Route;

/**
 * SQL View for JMM component
 */
class JMMViewSql extends HtmlView {

	/**
	 * The SQL query results
	 *
	 * @var    array
	 */
	protected $items;
	
	/**
	 * The available databases
	 *
	 * @var    array
	 */
	protected $databases;
	
	/**
	 * The canned queries
	 *
	 * @var    array
	 */
	protected $cannedQueries;
	
	/**
	 * The site tables
	 *
	 * @var    array
	 */
	protected $siteTables;

	/**
	 * Display the view
	 *
	 * @param   string  $tmpl  The template file to use
	 *
	 * @return  void
	 */
	function display($tmpl = null) {
		$app = Factory::getApplication();
		$input = $app->input;
		$document = Factory::getDocument();
		
		//$document->addScript(Uri::root().'media/com_jmm/js/jquery-1.7.2.min.js');
		$document->addScript('https://code.jquery.com/js/jquery-3.7.1.min.js');
		$document->addScript(Uri::root().'media/com_jmm/js/sql.js');
		
		$this->items = $this->get('Items');
		$this->databases = $this->get('Databases');
		$this->cannedQueries = $this->get('CannedQueries');
		$this->siteTables = $this->get('SiteTables');
				
		$filter_chnagedatabase = $input->getString('filter_chnagedatabase', '');
		$query = $input->getString('query', '');
		
		if($filter_chnagedatabase != '' && $query == '') {
			$url = 'index.php?option=com_jmm&view=sql&dbname='.$filter_chnagedatabase;
			$msg = Text::_('Database Changed to '.$filter_chnagedatabase);
			$app->enqueueMessage($msg, 'message');
			$app->redirect(Route::_($url, false), '', 301);
			return;
		}
		
		$this->addToolbar();
		parent::display($tmpl);
	}

	/**
	 * Add the page title and toolbar
	 *
	 * @return  void
	 */
	public function addToolbar() {
		ToolbarHelper::title(Text::_('Run SQL Queries'), 'sql.png');
		ToolbarHelper::preferences('com_jmm');
	}
}
