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
 * CreateTable View for JMM component
 */
class JMMViewCreateTable extends HtmlView {

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
		// Utiliser WebAsset Manager pour Joomla 5
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		$wa->useScript('jquery');
		
		$document = Factory::getDocument();
		$document->addStyleSheet(Uri::root().'media/com_jmm/css/createtable.css');
		$document->addScript(Uri::root().'media/com_jmm/js/createtable.js');
		
		// Passer des données PHP au JavaScript de manière sécurisée
		$scriptData = array(
			'baseUrl' => Uri::root(),
			'option' => 'com_jmm',
			'token' => Factory::getSession()->getFormToken()
		);
		
		$document = Factory::getDocument();
		$document->addScriptDeclaration('var JMMConfig = ' . json_encode($scriptData) . ';');
		$document->addScript(Uri::root().'media/com_jmm/js/createtable.js');
		
		$this->addToolbar();
		parent::display($tmpl);
	}

	/**
	 * Add the page title and toolbar
	 *
	 * @return  void
	 */
	public function addToolbar() {
		ToolbarHelper::title(Text::_('Create Table'), 'tables.png');
		ToolbarHelper::preferences('com_jmm');
	}
}
