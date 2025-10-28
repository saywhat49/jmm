<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;

/**
 * JMM component helper class
 */
class JMM {

	/**
	 * Get the base URL for the component
	 *
	 * @return  string  Base URL
	 */
	public static function baseURL() {
		return Uri::root() . '/components/com_jmm';
	}
}
