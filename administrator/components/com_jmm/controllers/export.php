<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Response\JsonResponse;

/**
 * Export Controller for JMM component
 */
class JMMControllerExport extends AdminController
{
	/**
	 * Export data in CSV format
	 *
	 * @return  void
	 */
	function csv()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$response = array();
		
		$query = $input->getString('query', '');
		$filename = $input->getString('filename', 'export');
		
		if (!empty($query)) {
			// CORRECTION : Noms de modèles cohérents
			$sqlModel = $this->getModel('SQL'); // Majuscule pour cohérence
			$exportModel = $this->getModel('Export'); // Majuscule pour cohérence
			
			if ($rows = $sqlModel->getItems()) {
				$response['status'] = true;
				$response['msg'] = 'Exported Successfully';
				$exportedFileName = $exportModel->saveAsCSV($rows, $filename);
				$response['download_url'] = Uri::base() . 'components/com_jmm/exported/' . $exportedFileName;
			} else {
				$response['status'] = false;
				$response['msg'] = 'Error Getting Data From Query';
			}
		} else {
			$response['status'] = false;
			$response['msg'] = 'No query provided';
		}
		
		// AMÉLIORATION : Réponse JSON plus robuste
		$app->setHeader('Content-Type', 'application/json; charset=utf-8');
		echo json_encode($response);
		$app->close();
	}
}
