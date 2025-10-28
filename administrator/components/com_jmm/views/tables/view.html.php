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
class JMMViewTables extends HtmlView {

	protected $items;
	protected $state;
	protected $pagination;
	protected $tables;
	protected $databases;
	function display($tmpl = null) {
		$document=Joomla\CMS\Factory::getDocument();
		$document->addScript(JURI::root().'media/com_jmm/js/jquery-1.7.2.min.js');
		$document->addScript(JURI::root().'media/com_jmm/js/export.js');
		$action = JRequest::getString('action', '');
		$limit = JRequest::getInt('limit',15);
		$limitstart = JRequest::getInt('limitstart',0);
		$pagination='&limit='.$limit.'&limitstart='.$limitstart;
		$filter_order=JRequest::getString('filter_order',null);
		$filter_order_Dir=JRequest::getString('filter_order_Dir',null);
		if(isset($filter_order) && isset($filter_order_Dir)){
			$pagination.='&filter_order='.$filter_order.'&filter_order_Dir='.$filter_order_Dir;
		}
		$tbl = JRequest::getString('tbl');
		$filter_browsetable=JRequest::getVar('filter_browsetable','');
		if(isset($_REQUEST['dbname'])){
			$dbname=JRequest::getVar('dbname');
			$urlString='&dbname='.$dbname;
		}else{
			$urlString='';
		}
		if($filter_browsetable!=''){
			Joomla\CMS\Factory::getApplication()->redirect('index.php?option=com_jmm&view=tables&action=browse&tbl='.$filter_browsetable.$urlString.$pagination,'Table Data of '.$filter_browsetable);
		}
		$filter_tablestructure=JRequest::getVar('filter_tablestructure');
		if($filter_tablestructure!=''){			
			Joomla\CMS\Factory::getApplication()->redirect('index.php?option=com_jmm&view=tables&action=structure&tbl='.$filter_tablestructure.$urlString,'Table Structure of '.$filter_tablestructure);
		}
		$filter_chnagedatabase=JRequest::getVar('filter_chnagedatabase','');
		if($filter_chnagedatabase!=''){
			Joomla\CMS\Factory::getApplication()->redirect('index.php?option=com_jmm&view=tables&dbname='.$filter_chnagedatabase,'Database Switched to  '.$filter_chnagedatabase);
		}
		
		
		switch ($action) {
			case 'structure' :
				$this -> items = JMMCommon::showTableStructure($tbl);
				Joomla\CMS\Factory::getApplication()->enqueueMessage('Table Structure of '.$tbl, 'message');
				break;
			case 'browse' :
				$this -> items =  $this -> get('Items');
				Joomla\CMS\Factory::getApplication()->enqueueMessage('Table Data of '.$tbl, 'message');
				break;

			default :
				$this -> items = JMMCommon::showTableLists();
				Joomla\CMS\Factory::getApplication()->enqueueMessage('Table Lists', 'message');
				break;
		}
		$this->tables=$this->get('Tables');
		$this->databases=$this->get('Databases');
		$this -> pagination = $this -> get('Pagination');
		$this -> state = $this -> get('State');
		$this -> addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Tables', 'tables.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
