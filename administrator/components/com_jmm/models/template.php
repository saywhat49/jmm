<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;

/**
 * Template Model for JMM component
 */
class JMMModelTemplate extends AdminModel
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 */
	public function getTable($type = 'Template', $prefix = 'JMMTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);		
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  object|boolean  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		
		if ($item && !empty($item->title)) {
			$templateFolder = JPATH_SITE . '/components/com_jmm/templates/' . $item->title;
			$item->php = $this->getFileContent($templateFolder . '/index.php');
			$item->css = $this->getFileContent($templateFolder . '/css/default.css');
			$item->js = $this->getFileContent($templateFolder . '/js/custom.js');
		}
		
		return $item;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		$app = Factory::getApplication();
		$data = $app->getUserState('com_jmm.edit.template.data', array());
		
		if (empty($data)) {
			$data = $this->getItem();
		}
		
		return $data;
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  \Joomla\CMS\Form\Form|boolean  A Form object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		$form = $this->loadForm('com_jmm.template', 'template', array('control' => 'jform', 'load_data' => $loadData));
		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 */
	public function save($data)
	{
		$app = Factory::getApplication();
		$pk = (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable();
		$isNew = true;
		
		// Load the row if saving an existing item
		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}
		
		// Débogage
		//$app->enqueueMessage('Données à sauvegarder: ' . json_encode($data), 'notice');
		
		// Préparer les données de template
		$title = $data['title'];
		$php = $data['php'] ?? '';
		$css = $data['css'] ?? '';
		$js = $data['js'] ?? '';
		
		// Vérifier si le titre est défini
		if (empty($title)) {
			$this->setError(Text::_('COM_JMM_ERROR_TEMPLATE_TITLE_REQUIRED'));
			return false;
		}
		
		// Créer le dossier du template si nécessaire
		$templateFolder = JPATH_SITE . '/components/com_jmm/templates/' . $title;
		
		// Si le titre a changé, renommer le dossier
		if (!$isNew && $table->title != $title) {
			$oldTemplateFolder = JPATH_SITE . '/components/com_jmm/templates/' . $table->title;
			
			if (is_dir($oldTemplateFolder)) {
				// Renommer ou copier le dossier
				try {
					Folder::move($oldTemplateFolder, $templateFolder);
				} catch (Exception $e) {
					$this->setError($e->getMessage());
					return false;
				}
			}
		}
		
		// Créer les sous-dossiers s'ils n'existent pas
		if (!is_dir($templateFolder)) {
			try {
				Folder::create($templateFolder);
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		
		if (!is_dir($templateFolder . '/css')) {
			try {
				Folder::create($templateFolder . '/css');
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		
		if (!is_dir($templateFolder . '/js')) {
			try {
				Folder::create($templateFolder . '/js');
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		
		if (!is_dir($templateFolder . '/images')) {
			try {
				Folder::create($templateFolder . '/images');
			} catch (Exception $e) {
				$this->setError($e->getMessage());
				return false;
			}
		}
		
		// Écrire les fichiers
		$phpFile = $templateFolder . '/index.php';
		$cssFile = $templateFolder . '/css/default.css';
		$jsFile = $templateFolder . '/js/custom.js';
		
		try {
			File::write($phpFile, $php);
			File::write($cssFile, $css);
			File::write($jsFile, $js);
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
		
		// Enregistrer les données dans la base de données
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}
		
		// Ajouter des champs supplémentaires si nécessaire
		if ($isNew && !isset($data['datetime'])) {
			$table->datetime = date('Y-m-d H:i:s');
		}
		
		// Vérifier les données
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}
		
		// Stocker les données
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}
		
		// Nettoyer le cache
		$this->cleanCache();
		
		// Enregistrer l'ID
		$this->setState($this->getName() . '.id', $table->id);
		
		return true;
	}

	/**
	 * Get file content if file exists
	 *
	 * @param   string  $filePath  The file path
	 *
	 * @return  string  The file content or empty string
	 */
	function getFileContent($filePath)
	{
		if (file_exists($filePath)) {
			return file_get_contents($filePath);
		} else {
			return "";
		}
	}
}
