<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMTableInsert extends JTable
{
	public function __construct(&$db)
	{ 
		$tbl=JRequest::getVar('tbl');
		$db=JMMCommon::getDBInstance();
		parent::__construct($tbl,null,$db);
	}
}
