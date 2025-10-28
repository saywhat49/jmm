<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die("Access Not Allowed");

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;

/**
 * JMM Helper Class
 */
class JMMHelper 
{
	/**
	 * Add the submenu items for the component
	 *
	 * @param   string  $viewName  The current view name
	 *
	 * @return  void
	 */
	public static function addSubmenu($viewName) {
		$app = Factory::getApplication();
		$input = $app->input;
		
		// Préparer la chaîne d'URL pour la base de données
		$urlString = '';
		if ($input->exists('dbname')) {
			$dbname = $input->getString('dbname');
			$urlString = '&dbname=' . $dbname;
		}
		
		// Ajouter les entrées du menu latéral
		Sidebar::addEntry(
			'Databases', 
			'index.php?option=com_jmm&amp;view=databases' . $urlString, 
			$viewName == 'databases' || $viewName == 'database'
		);
		
		Sidebar::addEntry(
			'Tables', 
			'index.php?option=com_jmm&amp;view=tables' . $urlString, 
			$viewName == 'tables' || $viewName == 'table'
		);
		
		Sidebar::addEntry(
			'Run SQL Query', 
			'index.php?option=com_jmm&amp;view=sql' . $urlString, 
			$viewName == 'sql'
		);
		
		Sidebar::addEntry(
			'Canned Query', 
			'index.php?option=com_jmm&amp;view=cannedqueries' . $urlString, 
			$viewName == 'cannedqueries' || $viewName == 'cannedquery'
		);
		
		Sidebar::addEntry(
			'Site Tables', 
			'index.php?option=com_jmm&amp;view=sitetables' . $urlString, 
			$viewName == 'sitetables' || $viewName == 'sitetable'
		);
		/**
		Sidebar::addEntry(
			'Create Table', 
			'index.php?option=com_jmm&amp;view=createtable' . $urlString, 
			$viewName == 'createtables' || $viewName == 'createtable'
		);
		
		Sidebar::addEntry(
			'Insert Data', 
			'index.php?option=com_jmm&amp;view=insert' . $urlString, 
			$viewName == 'insert'
		);
		*/
		
		Sidebar::addEntry(
			'Templates', 
			'index.php?option=com_jmm&amp;view=templates' . $urlString, 
			$viewName == 'templates' || $viewName == 'template'
		);
	}
}
