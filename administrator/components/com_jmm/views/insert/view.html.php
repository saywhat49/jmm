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
 * Insert View for JMM component
 */
class JMMViewInsert extends HtmlView {
	
	/**
	 * The item object
	 *
	 * @var    object
	 */
	protected $item;
	
	/**
	 * The form object
	 *
	 * @var    object
	 */
	protected $form;
	
	/**
	 * The available databases
	 *
	 * @var    array
	 */
	protected $databases;
	
	/**
	 * The available tables
	 *
	 * @var    array
	 */
	protected $Tables;
	
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return  void
	 */
	function display($tpl = null) 
	{
		$app = Factory::getApplication();
		$input = $app->input;
		
		$dbname = $input->getString('dbname', null);
		
		if (empty($dbname)) {
			$defaultdbname = $app->get('db');
			$redirectUrl = 'index.php?option=com_jmm&view=insert';
			$redirectUrl .= '&dbname=' . $defaultdbname;
			$message = 'Default Joomla Database ' . $defaultdbname . ' Selected';
			
			$app->enqueueMessage($message, 'message');
			$app->redirect($redirectUrl);
			return;
		}
		
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		
		$document = Factory::getDocument();
// 1. jQuery UI CSS - Version 1.13.3
$document->addStyleSheet('https://code.jquery.com/ui/1.13.3/themes/ui-lightness/jquery-ui.css');
// 2. jQuery - Version 3.7.1
$document->addScript('https://code.jquery.com/js/jquery-3.7.1.min.js');
// 3. jQuery UI - Version 1.13.3
$document->addScript('https://code.jquery.com/ui/1.13.3/jquery-ui.min.js');
		//$document->addStyleSheet('https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
		$document->addStyleSheet(Uri::root() . 'media/com_jmm/css/jquery-ui-timepicker-addon.css');
		$document->addStyleSheet(Uri::root() . 'media/com_jmm/css/createtable.css');
		//$document->addScript(Uri::root() . 'media/com_jmm/js/jquery-1.7.2.min.js');
		//$document->addScript('https://code.jquery.com/ui/1.12.1/jquery-ui.js');
		
		$document->addScript(Uri::root() . 'media/com_jmm/js/jquery-ui-timepicker-addon.js');
		$document->addScript(Uri::root() . 'media/com_jmm/js/insert.js');
		
		$filter_chnagetable = $input->getString('filter_chnagetable', '');
		$filter_chnagedatabase = $input->getString('filter_chnagedatabase', '');
		$redirectUrl = 'index.php?option=com_jmm&view=insert';
		$message = '';
		
		if (!empty($filter_chnagetable)) {	
			$redirectUrl .= '&tbl=' . $filter_chnagetable;
			$message .= 'Table Switched to ' . $filter_chnagetable;		
		}
		
		if (!empty($filter_chnagedatabase)) {
			$redirectUrl .= '&dbname=' . $filter_chnagedatabase;
			$message .= 'Database Switched to ' . $filter_chnagedatabase;
		}
		
		if (!empty($filter_chnagetable) || !empty($filter_chnagedatabase)) {
			$app->enqueueMessage($message, 'message');
			$app->redirect($redirectUrl);
			return;
		}
		
		$this->databases = $this->get('Databases');
		$this->Tables = $this->get('Tables');
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	/**
	 * Add the page title and toolbar
	 *
	 * @return  void
	 */
	public function addToolbar()
	{
		if ($this->item->id) {
			ToolbarHelper::title(Text::_('Insert Into Table'), 'insert.png');
		} else {
			ToolbarHelper::title(Text::_('Save to Table'), 'insert.png');
		}
		
		ToolbarHelper::apply('insert.apply', Text::_('JToolBar_APPLY'));
		ToolbarHelper::save('insert.save', Text::_('JToolBar_SAVE'));
		ToolbarHelper::save2new('insert.save2new', Text::_('JToolBar_SAVE_AND_NEW'));
		ToolbarHelper::cancel('insert.cancel');
	}
}
