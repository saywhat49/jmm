<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
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

// Message de diagnostic, visible uniquement par un gestionnaire du composant.
$problem = '';

if (!$useCustom && $this->canSeeDiagnostic()) {
    if ((int) $d['template_id'] === 0) {
        $problem = 'Aucun modele JMM selectionne dans cet element de menu (parametre "Modele JMM"). La mise en page generique "'
            . $layout . '" est utilisee.';
    } elseif (!$d['record_found']) {
        $problem = 'Le modele n° ' . (int) $d['template_id'] . ' est introuvable ou non publie.';
    } elseif ($d['layout_type'] !== 'custom') {
        $problem = 'Le modele "' . $d['title'] . '" est regle sur la mise en page "' . $d['layout_type']
            . '". Passez-le sur "Modele PHP personnalise" pour que son index.php soit execute.';
    } elseif (empty($d['base_exists'])) {
        $problem = 'Le dossier /components/com_jmm/templates est absent du serveur.';
    } else {
        $problem = 'Le fichier index.php est introuvable : ' . str_replace(JPATH_SITE, '', (string) $d['path']) . '/index.php';
    }
}
?>
<?php
// Avertissement dedie aux fichiers CSS/JS du modele personnalise.
$assetProblem = '';

if ($useCustom && $this->canSeeDiagnostic()) {
    $missing = [];

    if (empty($d['css_url'])) {
        $missing[] = 'css/default.css';
    }

    if (empty($d['js_url'])) {
        $missing[] = 'js/custom.js';
    }

    if ($missing) {
        $assetProblem = 'Fichier(s) absent(s) du dossier du modele : ' . implode(', ', $missing)
            . '. Le modele fonctionne, mais sans mise en forme ni script.';
    }
}
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
            <strong>JMM —</strong> <?php echo htmlspecialchars($problem, ENT_QUOTES, 'UTF-8'); ?>
            <div class="small text-muted mt-1">Message visible uniquement par les gestionnaires du composant.</div>
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
            <?php if ($useCustom): ?>
                <?php
                try {
                    include $customFile;
                } catch (\Throwable $e) {
                    if ($this->canSeeDiagnostic()) {
                        echo '<div class="alert alert-danger"><strong>JMM — erreur dans le modele '
                            . htmlspecialchars((string) $d['title'], ENT_QUOTES, 'UTF-8') . ' :</strong> '
                            . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
                            . '<br><small>' . htmlspecialchars(str_replace(JPATH_SITE, '', $e->getFile()), ENT_QUOTES, 'UTF-8')
                            . ' ligne ' . (int) $e->getLine() . '</small></div>';
                    }
                }
                ?>
            <?php elseif ($layout === 'cards'): ?>
                <?php echo $this->loadTemplate('cards'); ?>
            <?php elseif ($layout === 'chart'): ?>
                <?php echo $this->loadTemplate('chart'); ?>
            <?php else: ?>
                <?php echo $this->loadTemplate('table'); ?>
            <?php endif; ?>

            <?php if ($layout !== 'chart' && $this->defaultPagination && $this->pagination && $this->pagination->pagesTotal > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
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
