<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewSql extends JView {

	protected $items;
	protected $databases;
	protected $cannedQueries;
	protected $siteTables;

	function display($tmpl = null) {
		$document=JFactory::getDocument();
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'jquery-1.7.2.min.js');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'sql.js');
		$this -> items=$this->get('Items');
		$this->databases=$this->get('Databases');
		$this->cannedQueries=$this->get('CannedQueries');
		$this->siteTables=$this->get('SiteTables');
				
		$filter_chnagedatabase=JRequest::getVar('filter_chnagedatabase','');
		$query=JRequest::getVar('query','');
		if($filter_chnagedatabase!='' && $query==''){
			JFactory::getApplication()->redirect('index.php?option=com_jmm&view=sql&dbname='.$filter_chnagedatabase,'Database Changed to '.$filter_chnagedatabase);
		}
		$this->addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Run SQL Queries', 'sql.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
