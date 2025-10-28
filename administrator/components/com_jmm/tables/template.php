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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;

/**
 * Template Table class for JMM component
 */
class JMMTableTemplate extends Table
{
	/**
	 * Constructor
	 *
	 * @param   \Joomla\Database\DatabaseDriver  $db  A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jmm_templates', 'id', $db);
	}

	/**
	 * Method to perform sanity checks on the JTable instance properties to ensure
	 * they are safe to store in the database.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 */
	public function check()
	{
		// Check if title exists
		if (trim($this->title) == '') {
			$this->setError(Text::_('COM_JMM_ERROR_TEMPLATE_TITLE_REQUIRED'));
			return false;
		}

		// Generate a valid alias
		if (trim($this->title) != '') {
			// Automatically create an alias
			$this->title = OutputFilter::stringURLSafe($this->title);
		}

		// Set published to 1 if not set
		if (!(int) $this->published) {
			$this->published = 1;
		}

		// Set datetime if not set
		if (empty($this->datetime)) {
			$this->datetime = date('Y-m-d H:i:s');
		}

		return true;
	}
}
