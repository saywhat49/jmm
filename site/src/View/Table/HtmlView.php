<?php
namespace Saywhat49\Component\Jmm\Site\View\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected array $items = [];
    protected array $columns = [];
    protected ?object $siteTable = null;
    protected $pagination;
    protected $params;

    public function display($tpl = null): void
    {
        /** @var \Saywhat49\Component\Jmm\Site\Model\TableModel $model */
        $model           = $this->getModel();
        $this->siteTable = $model->getSiteTableDetails();
        $this->items     = $model->getItems();
        $this->columns   = $model->getColumns();
        $this->pagination= $model->getPagination();
        $this->params    = Factory::getApplication()->getParams('com_jmm');

        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->useStyle('com_jmm.site');

        parent::display($tpl);
    }
}