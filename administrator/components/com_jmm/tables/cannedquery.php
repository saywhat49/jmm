<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;

/**
 * CannedQuery Table class for JMM component
 */
class JMMTableCannedQuery extends Table
{
	/**
	 * Constructor
	 *
	 * @param   \Joomla\Database\DatabaseDriver  $db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jmm_canned_queries', 'id', $db);
	}
}
