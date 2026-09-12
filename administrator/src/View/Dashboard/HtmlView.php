<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class HtmlView extends BaseHtmlView
{
    protected string $currentDb = '';
    protected array $serverInfo = [];
    protected int $databaseCount = 0;
    protected array $dbStats = [];
    protected array $largestTables = [];
    protected array $counts = [];
    protected array $templateHealth = [];
    protected array $recentQueries = [];

    public function display($tpl = null): void
    {
        /** @var \Saywhat49\Component\Jmm\Administrator\Model\DashboardModel $model */
        $model = $this->getModel();

        $this->currentDb      = $model->getCurrentDb();
        $this->serverInfo     = $model->getServerInfo();
        $this->databaseCount  = $model->getDatabaseCount();
        $this->dbStats        = $model->getDbStats($this->currentDb);
        $this->largestTables  = $model->getLargestTables($this->currentDb);
        $this->counts         = $model->getComponentCounts();
        $this->templateHealth = $model->getTemplateHealth();
        $this->recentQueries  = $model->getRecentQueries();

        $this->getDocument()->getWebAssetManager()->useStyle('com_jmm.admin');

        JmmHelper::addSubmenu('dashboard');
        $this->addToolbar();

        parent::display($tpl);
    }

    /** Formate un nombre d'octets en unite lisible. */
    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 o';
        }

        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return number_format($bytes / (1024 ** $power), $power > 1 ? 1 : 0, ',', ' ') . ' ' . $units[$power];
    }

    protected function formatNumber(int $value): string
    {
        return number_format($value, 0, ',', ' ');
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_JMM_DASHBOARD'), 'home-2');

        if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_jmm')) {
            ToolbarHelper::preferences('com_jmm');
        }
    }
}
