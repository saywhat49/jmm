<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.jmodel');
class JMMModelExport extends BaseDatabaseModel {
	/**
	 * Save as CSV File
	 */
	function saveAsCSV($rows,$fileName='export'){
		$fileName=$fileName.'-'.time().'.csv';
		$keys=array_keys($rows[0]);
		$lists=array();
		$lists[]=$keys;
		foreach($rows as $row){
			$tmp=array();
			foreach($row as $key=>$value){
				$tmp[]=$value;
			}
			$lists[]=$tmp;
		}
		$fp = fopen(JPATH_COMPONENT.'/exported/'.$fileName, 'w');
		foreach ($lists as $fields) {
		    fputcsv($fp, $fields);
		}
		fclose($fp);
		return $fileName;
	}
}
