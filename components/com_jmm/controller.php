<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;

/**
 * Main controller for JMM component
 */
class JMMController extends BaseController {

	/**
	 * Method to get items via AJAX
	 *
	 * @return  void
	 */
	public function getItems() {
		$app = Factory::getApplication();
		$input = $app->input;
		
		$arr = array();
		$arr['Result'] = "FALSE";
		$arr['TotalRecordCount'] = 0;
		$arr['Records'] = array();
		
		$siteTableId = $input->getInt('site_table_id', 0);
		$model = $this->getModel('Table');
		$siteTableDetails = $model->getSiteTableDetails($siteTableId);
		
		$jtStartIndex = $input->getInt('jtStartIndex', 0);
		$jtPageSize = $input->getInt('jtPageSize', 25);
		$jtSorting = $input->getString('jtSorting', null);
		
		if (isset($siteTableDetails)) {
			$dbname = $siteTableDetails->dbname;
			$db = JMMCommon::getDBInstance(null, null, null, null, $dbname, null);
			$query = $siteTableDetails->query;
			
			$db->setQuery($query);
			
			try {
				$db->execute();
				$total = $db->getNumRows();
				
				if (!empty($jtSorting)) {
					$query .= " ORDER BY " . $db->escape($jtSorting);
				}
				
				$query .= " LIMIT " . (int)$jtStartIndex . "," . (int)$jtPageSize;
				$db->setQuery($query);
				
				$items = $db->loadAssocList();
				$arr['Result'] = "OK";
				$arr['TotalRecordCount'] = $total;
				$arr['Records'] = $items;
			} catch (Exception $e) {
				$arr['Result'] = "ERROR";
				$arr['Message'] = $e->getMessage();
			}
		}
		
		echo new JsonResponse($arr);
		$app->close();
	}
}
