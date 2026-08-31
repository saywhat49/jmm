<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$chartType = $this->params->get('chart_type', 'PieChart');
$chartId = 'jmm_chart_' . (int) $this->siteTable->id;

// Prepare data for Google Charts
$labelCol = $this->columns[0] ?? 'Label';
$numCol   = $this->columns[1] ?? 'Valeur';

$chartRows = [];
$totalSum = 0;
foreach ($this->items as $row) {
    $label = (string) ($row[$labelCol] ?? '');
    $val   = (float) ($row[$numCol] ?? 0);
    $totalSum += $val;
    $chartRows[] = [$label, $val];
}

$chartDataJson = json_encode(array_merge([[$labelCol, $numCol]], $chartRows), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

$doc = Factory::getApplication()->getDocument();
$doc->addScript('https://www.gstatic.com/charts/loader.js');
$doc->addScriptDeclaration("
document.addEventListener('DOMContentLoaded', function() {
    if (typeof google === 'undefined') return;
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable({$chartDataJson});
        var options = {
            title: " . json_encode($this->siteTable->title) . ",
            is3D: true,
            pieHole: 0.3,
            chartArea: { width: '85%', height: '80%' },
            legend: { position: 'right' },
            colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6610f2', '#fd7e14', '#20c997']
        };

        var chartContainer = document.getElementById('{$chartId}');
        if (chartContainer) {
            var chart = new google.visualization.{$chartType}(chartContainer);
            chart.draw(data, options);
            window.addEventListener('resize', function() {
                chart.draw(data, options);
            });
        }
    }
});
");
?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span class="fw-bold fs-5"><span class="icon-pie me-2" aria-hidden="true"></span><?php echo $this->escape($this->siteTable->title); ?></span>
        <?php if ($totalSum > 0): ?>
            <span class="badge bg-light text-dark fs-6">Total : <?php echo number_format($totalSum, 0, ',', ' '); ?></span>
        <?php endif; ?>
    </div>
    <div class="card-body p-3">
        <div id="<?php echo $chartId; ?>" style="width: 100%; height: 480px;"></div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-light fw-bold"><?php echo Text::_('COM_JMM_DETAILED_DATA'); ?></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <?php foreach ($this->columns as $col): ?>
                            <th scope="col"><?php echo $this->escape(ucwords(str_replace('_', ' ', $col))); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $row): ?>
                        <tr>
                            <?php foreach ($row as $val): ?>
                                <td><?php echo htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>