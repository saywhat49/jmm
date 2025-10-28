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
class JMMViewCreateTable extends HtmlView {

	protected $databases;
	
	function display($tmpl = null) {
		$this -> addToolbar();
		$document=Joomla\CMS\Factory::getDocument();
		$document->addStyleSheet(JURI::root().'media/com_jmm/css/createtable.css');
		$document->addScript(JURI::root().'media/com_jmm/js/jquery-1.7.2.min.js');
		$document->addScript(JURI::root().'media/com_jmm/js/createtable.js');
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Tables', 'tables.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
