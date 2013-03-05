<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
class JMMModelDatabases extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('pp.id', 'pp.points', 'pp.issuer', 'pp.recipients', 'pp.datetime', 'pp.published', 'ui.username', 'ur.username');
		}
		parent::__construct($config);
	}

	function getItems() {
		$db=JFactory::getDBO();
		$db->setQuery($this->getListQuery());
		$items=$db->loadAssocList();
		foreach($items as &$item){
			$item['link']='<a href="index.php?option=com_jmm&tbl=">';
		}
		return $items;
	}

	public function getListQuery() {
		$query="SHOW DATABASES";
		return $query;
	}

	protected function populateState($ordering = null, $direction = null) {
		$search = $this -> getUserStateFromRequest($this -> context . '.filter_search', 'filter_search');
		$this -> setState('filter.search', $search);
		$published = $this -> getUserStateFromRequest($this -> context . '.filter.published', 'filter_published');
		$this -> setState('filter.published', $published);
		parent::populateState($ordering, $direction);

	}

}
