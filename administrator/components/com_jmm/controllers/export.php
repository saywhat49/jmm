<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class JMMControllerExport extends JControllerAdmin
{
	/**
	 * Export In CSV
	 */
	function csv(){
		$mainframe=JFactory::getApplication();
		$response = array();
		$query = JRequest::getVar('query', '');
		$filename = JRequest::getVar('filename', 'export');
		if (isset($query) && $query!='') {
			$SQLmodel=$this->getModel('sql');
			$Exportmodel=$this->getModel('export');
			if($rows=$SQLmodel->getItems()){
				$response['status'] = true;
				$response['msg'] = 'Exported Sucessfully';
				$exportedFileName=$Exportmodel->saveAsCSV($rows,$filename);
				$response['download_url'] = JURI::base().'components'.DS.'com_jmm'.DS.'exported'.DS.$exportedFileName;
			}else{
				$response['status'] = false;
				$response['msg'] = 'Erro Getting Data From Query';
			}
		}
		echo json_encode($response);
		$mainframe->close();
	}

	

}