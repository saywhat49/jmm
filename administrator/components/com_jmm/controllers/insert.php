<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMControllerInsert extends JControllerForm
{	
	protected $view_list='insert';

	function save(){
		$jform=JRequest::getVar('jform',null,null,'validation type',JREQUEST_ALLOWRAW);
		$dbname=JRequest::getVar('dbname',JFactory::getApplication() -> getCfg('db'));
		$tbl=JRequest::getVar('tbl');
		$db=JMMCommon->getDBInstance();
		$query='INSERT INTO `'.$tbl.'`';
		$fields='';
		$values='';
		foreach($jform as $key=>$value){
			$fields.='`'.$key.'`,';
			$values.=$db->quote($value).',';		
		}
		$fields=rtrim($fields,',');
		$values=rtrim($values,',');
		$query.='('.$fields.') VALUES('.$values.')';
		$db->setQuery($query);
		$redirectUrl='index.php?option=com_jmm&view=insert&tbl='.$tbl.'&dbname='.$dbname;
		if($db->query()){
			JFactory::getApplication()->redirect($redirectUrl,"Record Inserted Sucessfully");
		}else{			
			JFactory::getApplication()->redirect($redirectUrl,$db->getErrorMsg(),'error');
		}
		
	}
	function apply(){
		
	}

}
