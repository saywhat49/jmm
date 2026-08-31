<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;

class CannedqueryModel extends AdminModel
{
    public function getTable($type = 'Cannedquery', $prefix = 'Administrator', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_jmm.cannedquery', 'cannedquery', ['control' => 'jform', 'load_data' => $loadData]);
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_jmm.edit.cannedquery.data', []);
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }
}