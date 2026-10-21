<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$d = $this->jmmDiagnostic;

// Le modele choisi dans l'element de menu prime sur le parametre display_layout.
$layout = !empty($d['layout_type']) ? $d['layout_type'] : $this->params->get('display_layout', 'table');

$useCustom = ($layout === 'custom' && !empty($d['file_exists']));

// Variables historiques mises a disposition des modeles utilisateur.
// Les modeles JMM 5.0.x s'attendent a trouver $rows, $cols, $params et $document.
$rows     = $this->items;
$cols     = $this->columns;
$params   = $this->params;
$document = Factory::getApplication()->getDocument();
$app      = Factory::getApplication();

$customFile = $this->jmmTemplatePath !== '' ? $this->jmmTemplatePath . '/index.php' : '';
$isManager  = $this->canSeeDiagnostic();

// Les diagnostics ne sont montres qu'aux gestionnaires du composant.
$problem = '';

if (!$useCustom && $isManager) {
    if ((int) $d['template_id'] === 0) {
        $problem = Text::sprintf('COM_JMM_DIAG_NO_TEMPLATE', $layout);
    } elseif (!$d['record_found']) {
        $problem = Text::sprintf('COM_JMM_DIAG_TEMPLATE_NOT_FOUND', (int) $d['template_id']);
    } elseif ($d['layout_type'] !== 'custom') {
        $problem = Text::sprintf('COM_JMM_DIAG_TEMPLATE_NOT_CUSTOM', $d['title'], $d['layout_type']);
    } elseif (empty($d['base_exists'])) {
        $problem = Text::_('COM_JMM_DIAG_FOLDER_MISSING');
    } else {
        $problem = Text::sprintf('COM_JMM_DIAG_INDEX_MISSING', str_replace(JPATH_SITE, '', (string) $d['path']) . '/index.php');
    }
}

// Avertissement dedie aux fichiers CSS/JS du modele personnalise.
$assetProblem = '';

if ($useCustom && $isManager) {
    $missing = [];

    if (empty($d['css_url'])) {
        $missing[] = 'css/default.css';
    }

    if (empty($d['js_url'])) {
        $missing[] = 'js/custom.js';
    }

    if ($missing) {
        $assetProblem = Text::sprintf('COM_JMM_DIAG_ASSETS_MISSING', implode(', ', $missing));
    }
}

// Les plugins de contenu (codes courts, protection des adresses e-mail...)
// ne s'appliquent a la sortie que si le menu le demande : les donnees de la
// base ne doivent pas pouvoir declencher un code court Joomla.
$prepareContent = (int) $params->get('prepare_content', 0) === 1;
?>
<div class="com-jmm-container py-3">
    <?php if ($assetProblem !== ''): ?>
        <div class="alert alert-warning shadow-sm">
            <span class="icon-exclamation-triangle me-2" aria-hidden="true"></span>
            <strong>JMM &mdash;</strong> <?php echo htmlspecialchars($assetProblem, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($problem !== ''): ?>
        <div class="alert alert-warning shadow-sm">
            <span class="icon-exclamation-triangle me-2" aria-hidden="true"></span>
            <strong>JMM &mdash;</strong> <?php echo htmlspecialchars($problem, ENT_QUOTES, 'UTF-8'); ?>
            <div class="small text-muted mt-1"><?php echo Text::_('COM_JMM_DIAG_MANAGERS_ONLY'); ?></div>
        </div>
    <?php endif; ?>

    <?php if ($isManager && $this->queryError !== ''): ?>
        <div class="alert alert-danger shadow-sm">
            <span class="icon-exclamation-triangle me-2" aria-hidden="true"></span>
            <strong>JMM &mdash;</strong> <?php echo htmlspecialchars($this->queryError, ENT_QUOTES, 'UTF-8'); ?>
            <div class="small text-muted mt-1"><?php echo Text::_('COM_JMM_DIAG_MANAGERS_ONLY'); ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->siteTable)): ?>
        <?php if (!$useCustom): ?>
            <?php
            // Libelle saisi dans l'element de menu ; a defaut, le titre de la table de site.
            $heading   = trim((string) $params->get('jmm_heading', ''));
            $showCount = (int) $params->get('show_record_count', 1) === 1;
            $total     = $this->pagination ? (int) $this->pagination->total : count($this->items);
            ?>
            <?php if ($heading !== '' || $showCount): ?>
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <?php if ($heading !== ''): ?>
                        <h1 class="h2 mb-0"><?php echo $this->escape($heading); ?></h1>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                    <?php if ($showCount && !empty($this->items)): ?>
                        <span class="badge bg-primary fs-6"><?php echo $total; ?> <?php echo Text::_('COM_JMM_RECORDS'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($this->items)): ?>
            <?php
            ob_start();
            try {
                if ($useCustom) {
                    include $customFile;
                } elseif ($layout === 'cards') {
                    echo $this->loadTemplate('cards');
                } elseif ($layout === 'chart') {
                    echo $this->loadTemplate('chart');
                } else {
                    echo $this->loadTemplate('table');
                }
                $tplOutput = (string) ob_get_clean();
                echo $prepareContent ? HTMLHelper::_('content.prepare', $tplOutput, '', 'com_jmm.table') : $tplOutput;
            } catch (\Throwable $e) {
                if (ob_get_level() > 0) {
                    ob_end_clean();
                }
                if ($isManager) {
                    echo '<div class="alert alert-danger"><strong>'
                        . htmlspecialchars(Text::sprintf('COM_JMM_DIAG_TEMPLATE_ERROR', (string) $d['title']), ENT_QUOTES, 'UTF-8')
                        . '</strong> '
                        . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
                        . '<br><small>' . htmlspecialchars(str_replace(JPATH_SITE, '', $e->getFile()), ENT_QUOTES, 'UTF-8')
                        . ' : ' . (int) $e->getLine() . '</small></div>';
                }
            }
            ?>

            <?php if ($layout !== 'chart' && $this->defaultPagination && $this->pagination && $this->pagination->pagesTotal > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
            <?php endif; ?>
        <?php elseif ($this->queryError === ''): ?>
            <div class="alert alert-info shadow-sm">
                <span class="icon-info-circle me-2" aria-hidden="true"></span>
                <?php echo Text::_('COM_JMM_NO_RECORDS_FOUND'); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-warning shadow-sm">
            <span class="icon-exclamation-triangle me-2" aria-hidden="true"></span>
            <?php echo Text::_('COM_JMM_SITE_TABLE_NOT_SPECIFIED'); ?>
        </div>
    <?php endif; ?>
</div>
