<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class JMMControllerCannedQueries extends JControllerAdmin
{
	protected $text_prefix='COM_JMM_CANNED_QUERIES';
	
	public function getModel($name='CannedQuery',$prefix='JMMModel',$config=array('ignore_request'=>true))
	{
		$model=parent::getModel($name,$prefix,$config);
		return $model;
	}
	
}