<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class JMMControllerSiteTables extends JControllerAdmin
{
	protected $text_prefix='COM_JMM_SITETABLES';
	
	public function getModel($name='SiteTable',$prefix='JMMModel',$config=array('ignore_request'=>true))
	{
		$model=parent::getModel($name,$prefix,$config);
		return $model;
	}
	
}
