<?php

defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * Templates View for JMM component
 */
class JMMViewTemplates extends HtmlView {

	protected $items;
	protected $pagination;
    protected $form;
	protected $state;
	
	/**
	 * Display the view
	 *
	 * @param   string  $tmpl  The template file to use
	 *
	 * @return  void
	 */
	function display($tmpl = null) {

		
		$this->items = $this->get('Items');
        $this->form = $this->get('Form');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		
        $wa = $this->document->getWebAssetManager();
        $wa->useScript('form.validate');
        $wa->useScript('keepalive');
        
        $this->addToolbar();
        parent::display($tmpl);
    }


	/**
	 * Add the page title and toolbar
	 *
	 * @return  void
	 */
	public function addToolbar() {
		ToolbarHelper::title(Text::_('Templates'), 'template.png');
		ToolbarHelper::addNew('template.add');
		ToolbarHelper::editList('template.edit');
		
		ToolbarHelper::divider();
		ToolbarHelper::publishList('templates.publish');
		ToolbarHelper::unpublishList('templates.unpublish');
	
		ToolbarHelper::divider();
		
		ToolbarHelper::archiveList('templates.archive');
		ToolbarHelper::trash('templates.trash');
		ToolbarHelper::deleteList(Text::_('Are you sure you want to delete this Site Table?'), 'templates.delete');
		ToolbarHelper::divider();
		ToolbarHelper::preferences('com_jmm');
	}
}
