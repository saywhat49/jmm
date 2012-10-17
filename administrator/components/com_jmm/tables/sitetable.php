<?php
defined('_JEXEC') or die('Restricted access');
class JMMTableSiteTable extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__jmm_sitetables','id',$db);
	}
}
