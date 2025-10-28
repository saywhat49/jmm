<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Insert Model for JMM component
 */
class JMMModelInsert extends AdminModel
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 */
	public function getTable($type = 'Insert', $prefix = 'JMMTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);		
	}	
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		$app = Factory::getApplication();
		$data = $app->getUserState('com_jmm.edit.insert.data', array());
		
		if (empty($data)) {
			$data = $this->getItem();
		}
		
		return $data;
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \Joomla\CMS\Form\Form|boolean  A Form object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		$form = $this->loadForm('com_jmm.insert', 'insert', array('control' => 'jform', 'load_data' => $loadData));
		
		return $form;
	}

	/**
	 * Method to get available databases.
	 *
	 * @return  array  The databases options.
	 */
	function getDatabases() {
		$rows = JMMCommon::getDataBaseLists();
		$databases = array();
		
		for ($i = 0; $i < count($rows); $i++) {
			$databases[] = HTMLHelper::_('select.option', $rows[$i], $rows[$i]);
		}
		
		return $databases;
	}

	/**
	 * Method to get available tables.
	 *
	 * @return  array  The tables options.
	 */
	function getTables() {
		$rows = JMMCommon::getTablesFromDB();
		$tables = array();
		
		for ($i = 0; $i < count($rows); $i++) {
			$tables[] = HTMLHelper::_('select.option', $rows[$i], $rows[$i]);
		}
		
		return $tables;
	}
}
