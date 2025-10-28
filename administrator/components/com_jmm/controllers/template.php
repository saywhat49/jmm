<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Template Controller for JMM component
 */
class JMMControllerTemplate extends FormController
{	
	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 */
	protected $view_list = 'templates';
	
	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean  True if allowed
	 */
	protected function allowSave($data = array(), $key = 'id')
	{
		return Factory::getUser()->authorise('core.edit', 'com_jmm');
	}
	
	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		$this->checkToken();
		
		$app = Factory::getApplication();
		$input = $app->input;
		$model = $this->getModel();
		$data = $input->get('jform', array(), 'array');
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();
		
		// Débogage - Log les données reçues
		//$app->enqueueMessage('Données reçues : ' . json_encode($data), 'notice');
		
		// Validation du formulaire
		$form = $model->getForm($data, false);
		
		if (!$form) {
			$app->enqueueMessage($model->getError(), 'error');
			return false;
		}
		
		$validData = $model->validate($form, $data);
		
		if ($validData === false) {
			$errors = $model->getErrors();
			
			foreach ($errors as $error) {
				if ($error instanceof Exception) {
					$app->enqueueMessage($error->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($error, 'warning');
				}
			}
			
			// Redirection vers le formulaire d'édition
			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($data[$key] ?? null, $key), false
				)
			);
			
			return false;
		}
		
		// Tentative de sauvegarde
		if (!$model->save($validData)) {
			// Redirection vers le formulaire d'édition
			$this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'error');
			$this->setRedirect(
				Route::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($data[$key] ?? null, $key), false
				)
			);
			
			return false;
		}
		
		// Message de succès et redirection
		$this->setMessage(Text::_('COM_JMM_TEMPLATE_SAVED_SUCCESSFULLY'));
		
		// Redirection selon le type de sauvegarde
		switch ($task) {
			case 'apply':
				// Redirection vers le formulaire d'édition
				$this->setRedirect(
					Route::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($data[$key] ?? null, $key), false
					)
				);
				break;
				
			case 'save2new':
				// Redirection vers un nouveau formulaire
				$this->setRedirect(
					Route::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend(null, $key), false
					)
				);
				break;
				
			default:
				// Redirection vers la liste
				$this->setRedirect(
					Route::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
				break;
		}
		
		return true;
	}
}
