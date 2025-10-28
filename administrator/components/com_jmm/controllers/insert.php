<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

/**
 * Insert Controller for JMM component
 */
class JMMControllerInsert extends FormController
{	
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 */
	protected $view_list = 'insert';

	/**
	 * Method to save a record.
	 *
	 * @return  void
	 */
	function save()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		
		// Get form data with raw filter for HTML content
		$jform = $input->get('jform', array(), 'array', JREQUEST_ALLOWRAW);
		$dbname = $input->getString('dbname', $app->get('db'));
		$tbl = $input->getString('tbl');
		
		// CORRECTION : Utiliser Factory::getDbo() au lieu de JMMCommon::getDBInstance()
		$db = Factory::getDbo();
		$query = 'INSERT INTO `' . $tbl . '`';
		$fields = '';
		$values = '';
		
		foreach ($jform as $key => $value) {
			$fields .= '`' . $key . '`,';
			$values .= $db->quote($value) . ',';		
		}
		
		$fields = rtrim($fields, ',');
		$values = rtrim($values, ',');
		$query .= '(' . $fields . ') VALUES(' . $values . ')';
		
		$db->setQuery($query);
		$redirectUrl = 'index.php?option=com_jmm&view=insert&tbl=' . $tbl . '&dbname=' . $dbname;
		
		try {
			$db->execute();
			$app->enqueueMessage('Record Inserted Successfully');
			$app->redirect($redirectUrl);
		} catch (Exception $e) {			
			$app->enqueueMessage($e->getMessage(), 'error');
			$app->redirect($redirectUrl);
		}
	}
	
	/**
	 * Method to apply changes.
	 *
	 * @return  void
	 */
	function apply()
	{
		// Implementation needed
	}
}
