<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * Template View for JMM component
 */
class JMMViewTemplate extends HtmlView {
	
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
	 * Display the view
	 *
	 * @param   string  $tpl  The template file to use
	 *
	 * @return  void
	 */
	function display($tpl = null) 
	{
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		
		// Charger les assets JavaScript pour Joomla 5
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		$wa->useScript('form.validate');
		$wa->useScript('keepalive');
		
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
			ToolbarHelper::title(Text::_('Edit Template'), 'template.png');
		} else {
			ToolbarHelper::title(Text::_('Save Template'), 'template.png');
		}
		
		ToolbarHelper::apply('template.apply', Text::_('JTOOLBAR_APPLY'));
		ToolbarHelper::save('template.save', Text::_('JTOOLBAR_SAVE'));
		ToolbarHelper::save2new('template.save2new', Text::_('JTOOLBAR_SAVE_AND_NEW'));
		ToolbarHelper::cancel('template.cancel');
	}
}
