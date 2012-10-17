<?php
defined('_JEXEC') or die('Restricted access');
class JMMTableCannedQuery extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__jmm_canned_queries','id',$db);
	}
}
