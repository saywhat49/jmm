<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMTableSiteTable extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__jmm_sitetables','id',$db);
	}
}
