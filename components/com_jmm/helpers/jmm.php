<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMM{

	public static function baseURL(){
		return JURI::root().DS.'components'.DS.'com_jmm';
	}
}