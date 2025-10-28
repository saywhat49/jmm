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

class JMMViewTables extends HtmlView {

	protected $items;
	protected $state;
	protected $pagination;
	protected $tables;
	protected $databases;
	
	function display($tmpl = null) {
		$app = Factory::getApplication();
		$input = $app->input;
		$document = Factory::getDocument();
		
		//$document->addScript(Uri::root().'media/com_jmm/js/jquery-1.7.2.min.js');
		$document->addScript('https://code.jquery.com/js/jquery-3.7.1.min.js');
		$document->addScript(Uri::root().'media/com_jmm/js/export.js');
		
		$action = $input->getString('action', '');
		$limit = $input->getInt('limit', 15);
		$limitstart = $input->getInt('limitstart', 0);
		$pagination = '&limit='.$limit.'&limitstart='.$limitstart;
		
		$filter_order = $input->getString('filter_order', null);
		$filter_order_Dir = $input->getString('filter_order_Dir', null);
		
		if(isset($filter_order) && isset($filter_order_Dir)){
			$pagination .= '&filter_order='.$filter_order.'&filter_order_Dir='.$filter_order_Dir;
		}
		
		$tbl = $input->getString('tbl');
		$filter_browsetable = $input->getString('filter_browsetable', '');
		
		if($input->exists('dbname')){
			$dbname = $input->getString('dbname');
			$urlString = '&dbname='.$dbname;
		} else {
			$urlString = '';
		}
		
		// Utiliser la création de redirection alternative qui fonctionne dans Joomla 5
		if($filter_browsetable != ''){
			// Utilisation d'une constante HTTP standard au lieu d'un nombre
			$url = 'index.php?option=com_jmm&view=tables&action=browse&tbl='.$filter_browsetable.$urlString.$pagination;
			$msg = Text::sprintf('COM_JMM_TABLE_DATA_OF', $filter_browsetable);
			$app->enqueueMessage($msg, 'message');
			$app->redirect(Route::_($url, false), '', 301);
			return;
		}
		
		$filter_tablestructure = $input->getString('filter_tablestructure', '');
		if($filter_tablestructure != ''){
			$url = 'index.php?option=com_jmm&view=tables&action=structure&tbl='.$filter_tablestructure.$urlString;
			$msg = Text::sprintf('COM_JMM_TABLE_STRUCTURE_OF', $filter_tablestructure);
			$app->enqueueMessage($msg, 'message');
			$app->redirect(Route::_($url, false), '', 301);
			return;
		}
		
		$filter_chnagedatabase = $input->getString('filter_chnagedatabase', '');
		if($filter_chnagedatabase != ''){
			$url = 'index.php?option=com_jmm&view=tables&dbname='.$filter_chnagedatabase;
			$msg = Text::sprintf('COM_JMM_DATABASE_SWITCHED_TO', $filter_chnagedatabase);
			$app->enqueueMessage($msg, 'message');
			$app->redirect(Route::_($url, false), '', 301);
			return;
		}
		
		switch ($action) {
			case 'structure' :
				$this->items = JMMCommon::showTableStructure($tbl);
				$msg = Text::sprintf('COM_JMM_TABLE_STRUCTURE_OF', $tbl);
				$app->enqueueMessage($msg, 'message');
				break;
			case 'browse' :
				$this->items = $this->get('Items');
				$msg = Text::sprintf('COM_JMM_TABLE_DATA_OF', $tbl);
				$app->enqueueMessage($msg, 'message');
				break;
			default :
				$this->items = JMMCommon::showTableLists();
				$msg = Text::_('COM_JMM_TABLE_LISTS');
				$app->enqueueMessage($msg, 'message');
				break;
		}
		
		$this->tables = $this->get('Tables');
		$this->databases = $this->get('Databases');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		ToolbarHelper::title(Text::_('Tables'), 'tables.png');
		ToolbarHelper::preferences('com_jmm');
	}
}
