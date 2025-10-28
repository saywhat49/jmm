<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

/**
 * Insert Table class for JMM component
 */
class JMMTableInsert extends Table
{
	/**
	 * Constructor
	 *
	 * @param   \Joomla\Database\DatabaseDriver  $db  A database connector object
	 */
	public function __construct(&$db)
	{ 
		$app = Factory::getApplication();
		$input = $app->input;
		
		// Obtenir le nom de la table
		$tbl = $input->getString('tbl', '');
		
		// Si aucune table n'est spécifiée, utiliser une table par défaut
		if (empty($tbl)) {
			// Table temporaire ou par défaut pour éviter les erreurs
			$tbl = '#__jmm_default';
		}
		
		// Obtenir l'instance de la base de données
		$db = JMMCommon::getDBInstance();
		
		// Appeler le constructeur parent
		parent::__construct($tbl, 'id', $db);
	}
}
