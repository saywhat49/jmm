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

class JMMViewCannedQuery extends HtmlView {
	
	protected $item;
	protected $form;
	
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
	
	public function addToolbar()
	{
		if($this->item->id){
			ToolbarHelper::title(Text::_('Edit Canned Query'));
		} else {
			ToolbarHelper::title(Text::_('Save Canned Query'));
		}
		
		ToolbarHelper::apply('cannedquery.apply', Text::_('JTOOLBAR_APPLY'));
		ToolbarHelper::save('cannedquery.save', Text::_('JTOOLBAR_SAVE'));
		ToolbarHelper::save2new('cannedquery.save2new', Text::_('JTOOLBAR_SAVE_AND_NEW'));
		ToolbarHelper::cancel('cannedquery.cancel');
	}
}
